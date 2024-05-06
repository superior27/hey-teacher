<?php
use App\Models\Question;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

#artisan test --filter=EditQuestionsTest

# artisan test --filter "should be able to open a question to edit"
it('should be able to open a question to edit', function () {

    //Arrange
    $user     = User::factory()->create();
    $question = Question::factory()->create(['created_by' => $user->id]);
    dump($question->toArray());

    //Act
    actingAs($user);

    //Assert
    get(route('questions.edit', $question->uuid))
        ->assertSuccessful();
});

# artisan test --filter "should return a view"
it('should return a view', function () {

    //Arrange
    $user     = User::factory()->create();
    $question = Question::factory()->create(['created_by' => $user->id]);
    dump($question->toArray());

    //Act
    actingAs($user);

    //Assert
    get(route('questions.edit', $question->uuid))
        ->assertViewIs('question.edit');
});

# artisan test --filter "should make sure that only question with status DRAFT can be edited"
it('should make sure that only question with status DRAFT can be edited', function () {

    //Arrange
    $user          = User::factory()->create();
    $questionDraft = Question::factory()->create([
        'created_by' => $user->id,
        'is_draft'   => true,
    ]);
    $questionPublished = Question::factory()->create([
        'created_by' => $user->id,
        'is_draft'   => false,
    ]);
    dump($questionDraft->toArray());
    dump($questionPublished->toArray());

    //Act
    actingAs($user);

    //Assert
    get(route('questions.edit', $questionPublished->uuid))->assertForbidden();
    get(route('questions.edit', $questionDraft->uuid))->assertSuccessful();
});