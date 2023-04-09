<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostsTest extends TestCase
{
    /** @test */
    public function an_user_can_see_the_posts_list(): void
    {
        $response = $this->get('/posts');

        $response->assertStatus(200);
    }
}
