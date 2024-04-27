<?php

namespace Secretwebmaster\LaravelOptionable\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Secretwebmaster\LaravelOptionable\Models\Option;
use Secretwebmaster\LaravelOptionable\Models\Post;

class CreatePostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = false;

    /** @test */
    public function a_post_can_be_created()
    {
        $post = Post::factory()->create();
        $this->assertCount(1, Post::all());
    }

    /** @test */
    public function a_post_option_can_be_created()
    {
        $post = Post::factory()->create();
        $option = $post->options()->create([
            'key' => 'is_recommended',
            'value' => true,
        ]);
        $this->assertCount(1, Option::all());
    }
    
}