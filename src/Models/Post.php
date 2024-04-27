<?php

namespace Secretwebmaster\LaravelOptionable\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Secretwebmaster\LaravelOptionable\Database\Factories\PostFactory;
use Secretwebmaster\LaravelOptionable\Traits\HasOptions;

class Post extends Model
{
    use HasFactory;
    use HasOptions;

    protected $guarded = [];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return PostFactory::new();
    }
}
