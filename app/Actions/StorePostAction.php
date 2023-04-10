<?php

namespace App\Actions;

use App\Models\Post;
use Exception;
use InvalidArgumentException;

class StorePostAction
{
    /** @throws Exception */
    public static function execute(array $data): Post
    {
        self::validate($data);

        $post = new Post();

        $post->title = $data['title'];
        $post->content = $data['content'];

        try {
            $post->save();
        } catch (Exception $e) {
            report($e);

            throw new Exception('Error creating the post: ' . $e->getMessage());
        }

        return $post;
    }

    private static function validate(array $data): void
    {
        if (
            empty($data)
            || !array_key_exists('title', $data)
            || !array_key_exists('content', $data)
            || empty($data['title'])
            || empty($data['content'])
        ) {
            throw new InvalidArgumentException('The given data was invalid.');
        }
    }
}
