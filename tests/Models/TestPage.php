<?php

namespace Secretwebmaster\LaravelOptionable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Secretwebmaster\LaravelOptionable\Traits\HasOptions;

class TestPage extends Model
{
    use HasOptions;

    protected $table = 'test_pages';

    protected $guarded = [];
}
