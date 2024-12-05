<?php

namespace App\Tables\Columns;

use Closure;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Concerns\CanBeCopied;
use Filament\Tables\Columns\Concerns\CanWrap;

class ColorColumn extends Column
{
    use CanBeCopied;
    use CanWrap;

    /**
     * @var view-string
     */
    protected string $view = 'filament.tables.columns.color-column';

    protected string | Closure | null $text = null;

    protected string | Closure $textColor = '#000';

    public function text(string | Closure | null $value, string | Closure $color = '#000'): static
    {
        $this->text = $value;
        $this->textColor = $color;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->evaluate($this->text, [
            'record' => $this->getRecord(),
        ]);
    }

    public function getTextColor(): mixed
    {
        return $this->evaluate($this->textColor, [
            'record' => $this->getRecord(),
        ]);
    }
}
