<?php

namespace Secretwebmaster\LaravelOptionable\Traits;

use Secretwebmaster\LaravelOptionable\Models\Option;

trait HasOptions
{
    public function options()
    {
        return $this->morphMany(Option::class, 'optionable');
    }
}