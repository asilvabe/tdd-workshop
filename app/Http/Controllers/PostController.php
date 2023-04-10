<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        return view('posts.index', [
            'posts' => Post::whereNotNull('approved_at')->orderByDesc('created_at')->paginate(10),
        ]);
    }
}
