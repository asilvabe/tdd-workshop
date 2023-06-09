<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
            ->assertSessionDoesntHaveErrors()
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
            ->assertInvalid([$attribute]);
    }

    /** @test */
    public function recent_created_post_must_to_be_unapproved_by_default(): void
    {
        $data = [
            'title' => 'My first post',
            'content' => 'This is my first post',
        ];

        $this->actingAs(User::factory()->create());

        $this->post('/posts', $data);

        $post = Post::first();

        $this->assertNull($post->approved_at);
        $this->assertFalse($post->isApproved());
    }

    /** @test */
    public function it_can_create_posts_trhough_an_artisan_command(): void
    {
        User::factory()->admin()->create();

        $post = [
            'title' => 'My first post',
            'content' => 'This is my first post',
        ];

        $this
            ->artisan('app:create-post')
            ->expectsQuestion('What is the title of the post?', $post['title'])
            ->expectsQuestion('What is the content of the post?', $post['content'])
            ->expectsOutput('Creating a new post...')
            ->expectsOutput('Post created with ID: 1')
            ->assertExitCode(0);

        $this->assertDatabaseHas('posts', $post);
    }

    /** @test */
    public function it_throws_an_error_when_creating_a_post_with_invalid_data(): void
    {
        $this
            ->artisan('app:create-post')
            ->expectsQuestion('What is the title of the post?', null)
            ->expectsQuestion('What is the content of the post?', 'This is my first post')
            ->expectsOutput('Creating a new post...')
            ->expectsOutput('Error creating the post: The given data was invalid.')
            ->assertExitCode(1);
    }

    /** @test */
    public function it_can_store_an_image_when_storing_a_post(): void
    {
        Storage::fake('posts');

        $file = UploadedFile::fake()->image('cover.jpeg');

        $data = [
            'title' => 'My first post',
            'content' => 'This is my first post',
            'image' => $file,
        ];

        $this->actingAs(User::factory()->create());

        $this->post('/posts', $data);

        $this->assertDatabaseHas('posts', [
            'title' => 'My first post',
            'content' => 'This is my first post',
            'image_path' => $file->hashName(),
        ]);

        Storage::disk('posts')->assertExists($file->hashName());
    }

    /**
     * @test
     * @dataProvider invalidImagesDataProvider
     */
    public function cannot_store_an_image_due_invalid_file(mixed $file): void
    {
        Storage::fake();

        $data = [
            'title' => 'My first post',
            'content' => 'This is my first post',
            'image' => $file,
        ];

        $this->actingAs(User::factory()->create());

        $this->post('/posts', $data)->assertInvalid(['image']);

        $this->assertDatabaseMissing('posts', [
            'title' => 'My first post',
            'content' => 'This is my first post',
            'image_path' => "posts-images/{$file->hashName()}",
        ]);

        Storage::assertMissing("posts-images/{$file->hashName()}");
    }

    public function invalidImagesDataProvider(): array
    {
        return [
            'invalid extension' => [
                UploadedFile::fake()->create('document.pdf', 1024),
            ],
            'invalid size' => [
                UploadedFile::fake()->image('cover.jpeg')->size(2048),
            ],
        ];
    }

    public function postsDataprovider(): array
    {
        return [
            'title is required' => [
                'title',
                [
                    'title' => '',
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
