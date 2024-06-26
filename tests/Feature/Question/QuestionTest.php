<?php
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

#artisan test --filter=Question

# artisan test --filter "should be create a question bigger than 255 characters"
it('should be create a question bigger than 255 characters', function () {
    # Arrange

    $user = User::factory()->create();
    actingAs($user);

    # Act
    $request = post(route('questions.store'), [
        'question' => str_repeat('*', 260) . '?',
    ]);

    # Assert
    $request->assertRedirect();
    assertDatabaseCount('questions', 1);
    assertDatabaseHas('questions', ['question' => str_repeat('*', 260) . '?']);
});

# artisan test --filter "should check if the question ends with ?"
it('should check if the question ends with ?', function () {
    # Arrange

    $user = User::factory()->create();
    actingAs($user);

    # Act
    $request = post(route('questions.store'), [
        'question' => str_repeat('*', 11),
    ]);

    # Assert
    $request->assertSessionHasErrors(['question']);
    assertDatabaseCount('questions', 0);
});

# artisan test --filter "should have at least 10 characters"
it('should have at least 10 characters', function () {
    # Arrange

    $user = User::factory()->create();
    actingAs($user);

    # Act
    $request = post(route('questions.store'), [
        'question' => str_repeat('*', 7) . '?',
    ]);

    # Assert
    $request->assertSessionHasErrors([
        'question' => __('validation.min.string', [
            'min' => 10, 'attribute' => 'question',
        ]),
    ]);
    assertDatabaseCount('questions', 0);
});

# artisan test --filter "should be create a question as a draft all the time"
it('should be create a question as a draft all the time', function () {
    # Arrange

    $user = User::factory()->create();
    actingAs($user);

    # Act
    $request = post(route('questions.store'), [
        'question' => str_repeat('*', 55) . '?',
    ]);

    # Assert
    $request->assertRedirect();
    assertDatabaseCount('questions', 1);
    assertDatabaseHas('questions', ['is_draft' => true]);
});

# artisan test --filter "should only allows authenticated users can create a question"
it('should only allows authenticated users can create a question', function () {
    # Arrange

    # Act
    post(route('questions.store'), [
        'question' => str_repeat('*', 55) . '?',
    ])->assertRedirect(route('login'));

    # Assert

});
