<?php

use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Livewire\ContactForm;
use App\Models\ContactMessage;
use App\Models\User;
use Livewire\Livewire;

test('a valid message is stored', function (): void {
    Livewire::test(ContactForm::class)
        ->set('name', 'Ada Lovelace')
        ->set('email', 'ada@example.com')
        ->set('message', 'Hello there, I would like to work with you.')
        ->call('submit')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('contact_messages', [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'message' => 'Hello there, I would like to work with you.',
        'is_read' => false,
    ]);
});

test('required fields are validated', function (array $payload, string $field): void {
    Livewire::test(ContactForm::class)
        ->set($field, $payload[$field])
        ->call('submit')
        ->assertHasErrors($field);

    expect(ContactMessage::count())->toBe(0);
})->with([
    'missing name' => [['name' => '', 'email' => 'a@example.com', 'message' => 'Hi'], 'name'],
    'missing email' => [['name' => 'Ada', 'email' => '', 'message' => 'Hi'], 'email'],
    'missing message' => [['name' => 'Ada', 'email' => 'a@example.com', 'message' => ''], 'message'],
]);

test('an invalid email address is rejected and nothing is stored', function (): void {
    Livewire::test(ContactForm::class)
        ->set('name', 'Ada')
        ->set('email', 'not-an-email')
        ->set('message', 'Hello')
        ->call('submit')
        ->assertHasErrors('email');

    expect(ContactMessage::count())->toBe(0);
});

test('submissions are rate limited', function (): void {
    for ($i = 0; $i < 5; $i++) {
        Livewire::test(ContactForm::class)
            ->set('name', 'Ada')
            ->set('email', 'ada@example.com')
            ->set('message', 'Hello')
            ->call('submit');
    }

    Livewire::test(ContactForm::class)
        ->set('name', 'Ada')
        ->set('email', 'ada@example.com')
        ->set('message', 'Hello again')
        ->call('submit')
        ->assertHasErrors('message');

    expect(ContactMessage::count())->toBe(5);
});

test('the message field is trimmed before storage', function (): void {
    Livewire::test(ContactForm::class)
        ->set('name', '  Ada  ')
        ->set('email', '  ada@example.com  ')
        ->set('message', '  Hello  ')
        ->call('submit')
        ->assertHasNoErrors();

    $message = ContactMessage::sole();

    expect($message->name)->toBe('Ada')
        ->and($message->email)->toBe('ada@example.com')
        ->and($message->message)->toBe('Hello');
});

test('an administrator can toggle a message between read and unread', function (): void {
    $message = ContactMessage::factory()->create(['is_read' => false]);

    $this->actingAs(User::factory()->create());

    Livewire::test(ListContactMessages::class)
        ->callTableAction('toggleRead', $message)
        ->assertHasNoTableActionErrors();

    expect($message->refresh()->is_read)->toBeTrue();

    Livewire::test(ListContactMessages::class)
        ->callTableAction('toggleRead', $message)
        ->assertHasNoTableActionErrors();

    expect($message->refresh()->is_read)->toBeFalse();
});

test('the unread inbox is reachable', function (): void {
    ContactMessage::factory()->create(['is_read' => false]);

    $this->actingAs(User::factory()->create())
        ->get('/admin/contact-messages')
        ->assertSuccessful();
});
