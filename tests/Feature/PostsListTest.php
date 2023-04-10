<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PostsListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_user_can_see_the_posts_list(): void
    {
        $response = $this->get('/posts');

        $response->assertOk();
    }

    /** @test */
    public function posts_list_must_show_a_title(): void
    {
        $response = $this->get('/posts');

        $response->assertSee('Posts list');
    }

    /** @test */
    public function posts_list_must_show_the_posts_title_and_the_creation_date(): void
    {
        $post = Post::factory()->create();

        $response = $this->get('/posts');

        $response
            ->assertViewHas('posts')
            ->assertSee($post->title)
            ->assertSee($post->created_at->format('d/m/Y'));
    }

    /** @test */
    public function posts_list_must_be_paginated(): void
    {
        Post::factory()->count(50)->create();

        $response = $this->get('/posts');

        $this->assertInstanceOf(LengthAwarePaginator::class, $response->viewData('posts'));
        $this->assertEquals(10, $response->viewData('posts')->perPage());
    }
}
