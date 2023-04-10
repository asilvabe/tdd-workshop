<?php

namespace App\Console\Commands;

use App\Actions\StorePostAction;
use Exception;
use Illuminate\Console\Command;

class CreatePost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new post';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $title = $this->ask('What is the title of the post?');
        $content = $this->ask('What is the content of the post?');

        $this->info('Creating a new post...');

        try {
            $post = StorePostAction::execute([
                'title' => $title,
                'content' => $content,
            ]);
        } catch (Exception $e) {
            $this->error('Error creating the post: ' . $e->getMessage());

            return 1;
        }

        $this->info("Post created with ID: {$post->id}");

        return 0;
    }
}
