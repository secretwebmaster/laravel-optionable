<?php

namespace Secretwebmaster\LaravelOptionable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Wncms\Translatable\Traits\HasTranslations;

/**
 * @property string $key
 * @property array|null $value
 */
class Option extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public static $modelKey = 'option';

    protected $translatable = ['value'];

    // protected $casts = [
    //     'value' => 'array',
    // ];

    public function optionable(): MorphTo
    {
        return $this->morphTo();
    }
}
