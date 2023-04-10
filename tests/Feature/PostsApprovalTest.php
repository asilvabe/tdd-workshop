<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostsApprovalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_user_can_approve_a_post(): void
    {
        $post = Post::factory()->unapproved()->create();

        $this->assertFalse($post->isApproved());

        $this
            ->actingAs(User::factory()->admin()->create())
            ->put("/posts/{$post->id}/approve")
            ->assertRedirect('/posts');

        $this->assertTrue($post->fresh()->isApproved());
    }

    /** @test */
    public function a_non_admin_user_cannot_approve_a_post(): void
    {
        $post = Post::factory()->unapproved()->create();

        $this->assertFalse($post->isApproved());

        $this
            ->actingAs(User::factory()->create())
            ->put("/posts/{$post->id}/approve")
            ->assertForbidden();

        $this->assertFalse($post->fresh()->isApproved());
    }
}
