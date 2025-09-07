<?php

namespace Secretwebmaster\LaravelOptionable\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $key
 * @property array|null $value
 */
class Option extends Model
{
    protected $guarded = [];

    protected $casts = [
        'value' => 'array',
    ];

    public function optionable()
    {
        return $this->morphTo();
    }
}
