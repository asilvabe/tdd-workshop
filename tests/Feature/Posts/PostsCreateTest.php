<?php

namespace Tests\Feature\Posts;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PostsCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_see_the_create_post_form(): void
    {
        $this
            ->get('/posts/create')
            ->assertRedirect('/login');

        $this->actingAs(User::factory()->create());

        $this
            ->get('/posts/create')
            ->assertOk()
            ->assertViewIs('posts.create');
    }

    /** @test */
    public function authenticated_users_can_create_posts(): void
    {
        $data = [
            'title' => 'My first post',
            'content' => 'This is my first post',
        ];

        $this->post('/posts', $data)->assertRedirect('/login');

        $this->actingAs(User::factory()->create());

        $this
            ->post('/posts', $data)
            ->assertRedirect();

        $this->assertDatabaseHas('posts', $data);
    }

    /** @test */
    public function title_is_required(): void
    {
        $data = [
            'content' => 'This is my first post',
        ];

        $this->actingAs(User::factory()->create());

        $this
            ->post('/posts', $data)
            ->assertInvalid(['title']);

        $this->assertDatabaseMissing('posts', $data);
    }

    /** @test */
    public function title_must_to_be_string(): void
    {
        $data = [
            'title' => 123,
            'content' => 'This is my first post',
        ];

        $this->actingAs(User::factory()->create());

        $this
            ->post('/posts', $data)
            ->assertInvalid(['title']);

        $this->assertDatabaseMissing('posts', $data);
    }

    /** @test */
    public function title_must_to_be_have__until_100_characters(): void
    {
        $data = [
            'title' => Str::random(101),
            'content' => 'This is my first post',
        ];

        $this->actingAs(User::factory()->create());

        $this
            ->post('/posts', $data)
            ->assertInvalid(['title']);

        $this->assertDatabaseMissing('posts', $data);
    }
}
