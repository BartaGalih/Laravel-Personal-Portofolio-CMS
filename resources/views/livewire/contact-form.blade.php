<div>
    @if ($sent)
        <p class="mb-8 rounded-xl border border-stone bg-oat px-5 py-4 font-serif text-base text-ink">
            {{ __('Thanks! Your message has been sent.') }}
        </p>
    @endif

    <form wire:submit="submit" class="flex max-w-xl flex-col gap-6">
        <div>
            <label for="contact-name" class="ui-label mb-2 block text-cloud-dark">{{ __('Name') }}</label>
            <input id="contact-name" type="text" wire:model="name"
                   class="w-full rounded-xl border border-cloud-dark bg-card px-4 py-3 font-serif text-lg text-ink outline-none focus:border-ink">
            @error('name') <p class="mt-2 font-sans text-xs text-clay-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="contact-email" class="ui-label mb-2 block text-cloud-dark">{{ __('Email') }}</label>
            <input id="contact-email" type="email" wire:model="email"
                   class="w-full rounded-xl border border-cloud-dark bg-card px-4 py-3 font-serif text-lg text-ink outline-none focus:border-ink">
            @error('email') <p class="mt-2 font-sans text-xs text-clay-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="contact-message" class="ui-label mb-2 block text-cloud-dark">{{ __('Message') }}</label>
            <textarea id="contact-message" wire:model="message" rows="6"
                      class="w-full rounded-xl border border-cloud-dark bg-card px-4 py-3 font-serif text-lg text-ink outline-none focus:border-ink"></textarea>
            @error('message') <p class="mt-2 font-sans text-xs text-clay-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <button type="submit" class="btn-clay">
                {{ __('Send message') }}
            </button>
        </div>
    </form>
</div>
