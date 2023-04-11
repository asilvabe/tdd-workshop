<?php

namespace App\Actions;

use App\Mail\PostApprovedMail;
use App\Models\Post;
use Illuminate\Support\Facades\Mail;

class ApprovePostAction
{
    public static function execute(Post $post): void
    {
        $post->approved_at = now();

        $post->save();

        Mail::to($post->author)->send(new PostApprovedMail());
    }
}
