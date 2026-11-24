<?php

namespace App\Livewire;

use App\Support\MarkdownRenderer;
use Livewire\Component;

class MarkdownPreview extends Component
{
    public string $markdown = '';

    public function render(MarkdownRenderer $renderer)
    {
        return view('livewire.markdown-preview', [
            'html' => $renderer->render($this->markdown),
        ]);
    }
}
