<?php

namespace Stephenjude\FilamentFeatureFlag\Traits;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;

trait HasAuthorization
{
    use EvaluatesClosures;

    public Closure|bool $authorized = true;

    public function authorize(Closure|bool $condition = true): static
    {
        $this->authorized = $condition;

        return $this;
    }

    public function authorized(): bool
    {
        return $this->evaluate($this->authorized) === true;
    }
}
