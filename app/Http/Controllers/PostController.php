<?php

namespace App\Http\Controllers;

use App\Actions\ApprovePostAction;
use App\Actions\StorePostAction;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        return view('posts.index', [
            'posts' => Post::approved()->orderByDesc('created_at')->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('posts.create');
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        StorePostAction::execute($request->validated());

        return to_route('posts.index')->with('success', 'Post created successfully!');
    }

    /**
     * @throws AuthorizationException
     */
    public function approve(Post $post): RedirectResponse
    {
        $this->authorize('approve', $post);

        ApprovePostAction::execute($post);

        return to_route('posts.index')->with('success', 'Post approved successfully!');
    }
}
