<?php

namespace Secretwebmaster\LaravelOptionable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Secretwebmaster\LaravelOptionable\Models\Post;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->sentence(),
        ];
    }
}