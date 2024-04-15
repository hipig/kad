<?php

namespace App\Console\Commands\Fixed;

use Illuminate\Console\Command;

class PostComment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixed:post-comment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $comments = \App\Models\PostComment::query()->with(['comment', 'comment.user'])->whereNotNull('comment_id')->get();

        foreach ($comments as $comment) {
            $comment->commentUser()->associate($comment->comment?->user);
            $comment->save();
        }
    }
}
