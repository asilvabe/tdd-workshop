<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostsCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_see_the_create_post_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/posts/create');

        $response->assertOk();
    }
}
