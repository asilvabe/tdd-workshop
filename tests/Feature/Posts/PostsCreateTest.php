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

    /**
     * @test
     * @dataProvider postsDataprovider
     */
    public function a_post_can_not_be_created_due_validation_errors(mixed $invalidTitle): void
    {
        $data = [
            'title' => $invalidTitle,
            'content' => 'This is my first post',
        ];

        $this->actingAs(User::factory()->create());

        $this
            ->post('/posts', $data)
            ->assertSessionHasErrors(['title']);
    }

    public function postsDataprovider(): array
    {
        return [
            'title is requied' => [null],
            'title must to be string' => [123],
            'title must to hace until 100 characters' => [Str::random(101)],
        ];
    }
}
