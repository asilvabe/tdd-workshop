<?php

namespace Tests\Feature;

use App\Mail\PostApprovedMail;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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

    /** @test */
    public function an_email_is_sent_to_the_post_author_when_the_post_is_approved(): void
    {
        Mail::fake();

        $post = Post::factory()->unapproved()->create();

        $this->assertFalse($post->isApproved());

        $this
            ->actingAs(User::factory()->admin()->create())
            ->put("/posts/{$post->id}/approve")
            ->assertRedirect('/posts');

        $this->assertTrue($post->fresh()->isApproved());

        Mail::assertSent(PostApprovedMail::class, function(PostApprovedMail $mail) use ($post) {
            return $mail->hasTo($post->author->email);
        });
    }
}
