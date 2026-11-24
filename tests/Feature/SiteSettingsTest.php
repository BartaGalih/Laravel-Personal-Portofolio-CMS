<?php

use App\Filament\Pages\ManageSettings;
use App\Models\Setting;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function (): void {
    Storage::fake('public');
});

test('the settings page saves the site name and tagline', function (): void {
    $this->actingAs(User::factory()->create());

    Livewire::test(ManageSettings::class)
        ->fillForm([
            'name' => 'Barta Galih',
            'tagline_en' => 'Notes on the craft',
            'tagline_id' => 'Catatan soal keahlian',
            'header_brand' => 'name',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(SiteSettings::name())->toBe('Barta Galih')
        ->and(SiteSettings::tagline('en'))->toBe('Notes on the craft')
        ->and(SiteSettings::tagline('id'))->toBe('Catatan soal keahlian');
});

test('an empty indonesian tagline falls back to english', function (): void {
    $this->actingAs(User::factory()->create());

    Livewire::test(ManageSettings::class)
        ->fillForm([
            'name' => 'Site',
            'tagline_en' => 'English only',
            'tagline_id' => '',
            'header_brand' => 'name',
        ])
        ->call('save');

    expect(SiteSettings::tagline('id'))->toBe('English only');
});

test('the header shows the site name by default', function (): void {
    Setting::put(SiteSettings::KEY, ['name' => 'My Portfolio', 'header_brand' => 'name']);

    $this->get('/en')
        ->assertSuccessful()
        ->assertSee('My Portfolio');
});

test('the header shows the uploaded logo when selected', function (): void {
    $this->actingAs(User::factory()->create());

    $logo = UploadedFile::fake()->image('logo.png', 400, 100);

    Livewire::test(ManageSettings::class)
        ->fillForm([
            'name' => 'My Portfolio',
            'header_brand' => 'logo',
            'logo_path' => $logo,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $path = SiteSettings::get('logo_path');
    expect($path)->toBeString();
    Storage::disk('public')->assertExists($path);

    $this->get('/en')
        ->assertSuccessful()
        ->assertSee(Storage::disk('public')->url($path), escape: false);
});

test('a missing logo file falls back to the site name', function (): void {
    // Selected "logo", but the file is gone — the header must not render blank.
    Setting::put(SiteSettings::KEY, [
        'name' => 'Fallback Name',
        'header_brand' => 'logo',
        'logo_path' => 'branding/deleted.png',
    ]);

    expect(SiteSettings::headerLogoUrl())->toBeNull();

    $this->get('/en')
        ->assertSuccessful()
        ->assertSee('Fallback Name');
});

test('the favicon is linked on the public site when set', function (): void {
    Storage::disk('public')->put('branding/favicon.png', 'fake');

    Setting::put(SiteSettings::KEY, ['name' => 'Site', 'favicon_path' => 'branding/favicon.png']);

    $this->get('/en')
        ->assertSuccessful()
        ->assertSee('<link rel="icon"', escape: false);
});

test('an empty name falls back to the configured app name', function (): void {
    Setting::put(SiteSettings::KEY, ['name' => '']);

    expect(SiteSettings::name())->toBe((string) config('app.name'));
});

test('clearing an uploaded image stores null rather than an empty string', function (): void {
    $this->actingAs(User::factory()->create());

    Setting::put(SiteSettings::KEY, ['name' => 'Site', 'logo_path' => 'branding/old.png']);

    Livewire::test(ManageSettings::class)
        ->fillForm(['name' => 'Site', 'header_brand' => 'name', 'logo_path' => null])
        ->call('save');

    expect(SiteSettings::get('logo_path'))->toBeNull();
});

test('saving settings does not create duplicate rows', function (): void {
    $this->actingAs(User::factory()->create());

    foreach (['First', 'Second'] as $name) {
        Livewire::test(ManageSettings::class)
            ->fillForm(['name' => $name, 'header_brand' => 'name'])
            ->call('save');
    }

    expect(Setting::query()->where('key', SiteSettings::KEY)->count())->toBe(1)
        ->and(SiteSettings::name())->toBe('Second');
});
