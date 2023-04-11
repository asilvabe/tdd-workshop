<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    public function approve(User $user): bool
    {
        return $user->isAdmin();
    }
}
