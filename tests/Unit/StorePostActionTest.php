<?php

namespace Tests\Unit;

use App\Actions\StorePostAction;
use App\Models\Post;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class StorePostActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_store_a_post_and_returns_the_recent_created_post(): void
    {
        User::factory()->admin()->create();

        $data = [
            'title' => 'My first post',
            'content' => 'This is my first post',
        ];

        $post = StorePostAction::execute($data);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals($data['title'], $post->title);
        $this->assertEquals($data['content'], $post->content);
        $this->assertDatabaseHas('posts', $data);
    }

    /**
     * @test
     * @throws Exception
     */
    public function it_throws_an_exception_if_the_data_is_empty(): void
    {
        User::factory()->admin()->create();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The given data was invalid.');

        StorePostAction::execute([]);
    }
}
