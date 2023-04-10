<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function store(Request $request): RedirectResponse
    {
        Post::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        return to_route('posts.index')->with('success', 'Post created successfully!');
    }
}
