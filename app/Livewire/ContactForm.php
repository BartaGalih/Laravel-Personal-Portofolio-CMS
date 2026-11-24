<?php

namespace App\Livewire;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $message = '';

    public bool $sent = false;

    public function submit(): void
    {
        $this->resetErrorBag();

        if (RateLimiter::tooManyAttempts($this->rateLimitKey(), 5)) {
            $this->addError('message', __('Too many messages sent. Please try again later.'));

            return;
        }

        $this->name = trim($this->name);
        $this->email = trim($this->email);
        $this->message = trim($this->message);

        $validated = $this->validate((new StoreContactMessageRequest)->rules());

        ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        RateLimiter::hit($this->rateLimitKey(), 3600);

        $this->reset('name', 'email', 'message');
        $this->sent = true;
    }

    public function render(): View
    {
        return view('livewire.contact-form');
    }

    private function rateLimitKey(): string
    {
        return 'contact-form:'.request()->ip();
    }
}
