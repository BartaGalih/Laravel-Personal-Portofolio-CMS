<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('the seeder creates the administrator from the environment', function (): void {
    putenv('ADMIN_EMAIL=admin@example.com');
    putenv('ADMIN_PASSWORD=secret-password');

    $this->seed();
    $this->seed();

    $user = User::sole();

    expect($user->email)->toBe('admin@example.com')
        ->and(Hash::check('secret-password', $user->password))->toBeTrue();

    putenv('ADMIN_EMAIL');
    putenv('ADMIN_PASSWORD');
});

test('the seeder skips when credentials are absent', function (): void {
    putenv('ADMIN_EMAIL');
    putenv('ADMIN_PASSWORD');

    $this->seed();

    expect(User::count())->toBe(0);
});
