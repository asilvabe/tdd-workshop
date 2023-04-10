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
    public function a_post_can_not_be_created_due_validation_errors(string $attribute, array $invalidData): void
    {
        $this->actingAs(User::factory()->create());

        $this
            ->post('/posts', $invalidData)
            ->assertSessionHasErrors([$attribute]);
    }

    public function postsDataprovider(): array
    {
        return [
            'title is requied' => [
                'title',
                [
                    'content' => 'This is my first post',
                ],
            ],
            'title must to be string' => [
                'title',
                [
                    'title' => 123,
                    'content' => 'This is my first post',
                ]
            ],
            'title must to hace until 100 characters' => [
                'title',
                [
                    'title' => Str::random(101),
                    'content' => 'This is my first post',
                ]
            ],
            'content is requied' => [
                'content',
                [
                    'title' => 'My first post',
                ],
            ],
            'content must to be string' => [
                'content',
                [
                    'title' => 'My first post',
                    'content' => 123,
                ]
            ],
            'content must to hace until 100 characters' => [
                'content',
                [
                    'title' => 'My first post',
                    'content' => Str::random(1001),
                ]
            ],
        ];
    }
}
