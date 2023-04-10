<?php

namespace Tests\Feature\Posts;

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
        $this
            ->get('/posts')
            ->assertOk();
    }

    /** @test */
    public function posts_list_must_show_a_title(): void
    {
        $this
            ->get('/posts')
            ->assertSee('Posts list');
    }

    /** @test */
    public function the_title_must_be_translated(): void
    {
        app()->setLocale('es');

        $this
            ->get('/posts')
            ->assertSee('Listado de publicaciones');

        app()->setLocale('en');

        $this
            ->get('/posts')
            ->assertSee('Posts list');
    }

    /** @test */
    public function posts_list_must_show_the_posts_title_and_the_creation_date(): void
    {
        $post = Post::factory()->approved()->create();

        $this
            ->get('/posts')
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

    /** @test */
    public function posts_list_must_be_ordered_by_creation_date_desc(): void
    {
        $firstPost = Post::factory()->approved()->create(['created_at' => now()->subDays(2)]);
        $secondPost = Post::factory()->approved()->create(['created_at' => now()->subDay()]);
        $thirdPost = Post::factory()->approved()->create(['created_at' => now()]);

        $this
            ->get('/posts')
            ->assertSeeTextInOrder([
                $thirdPost->title,
                $secondPost->title,
                $firstPost->title,
            ]);
    }

    /** @test */
    public function only_approved_posts_must_be_showed(): void
    {
        Post::factory()->approved()->count(5)->create();

        $unapprovedPost = Post::factory()->unapproved()->create();

        $response = $this->get('/posts');

        $this->assertCount(5, $response->viewData('posts'));
        $response->assertDontSee($unapprovedPost->title);
    }
}
