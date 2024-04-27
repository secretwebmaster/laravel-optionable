<?php

namespace Secretwebmaster\LaravelOptionable\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $guarded = [];

    public function optionable()
    {
        return $this->morphTo();
    }
}
