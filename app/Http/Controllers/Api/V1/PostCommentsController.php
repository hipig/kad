<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostCommentLikeResource;
use App\Http\Resources\PostCommentResource;
use App\Models\PostCommentLike;
use App\Models\PostComment;
use App\Notifications\PostCommentCommented;
use App\Notifications\PostCommentLiked;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostCommentsController extends Controller
{
    public function like(PostComment $comment)
    {
        $user = Auth::user();
        $like = PostCommentLike::query()->where('user_id', $user->id)->where('comment_id', $comment->id)->first();
        if (!$like) {
            $like = new PostCommentLike();
            $like->user()->associate($user);
            $like->comment()->associate($comment->id);
            $like->post()->associate($comment->post);
            $like->save();

            $comment->increment('like_count');

            $comment->user->notify(new PostCommentLiked($like));
        }

        return PostCommentLikeResource::make($like);
    }

    public function unLike(PostComment $comment)
    {
        $like = PostCommentLike::query()->where('user_id', Auth::id())->where('comment_id', $comment->id)->first();

        if ($like) {
            $this->authorize('own', $like);
            $like->delete();

            $comment->decrement('like_count');
        }

        return response()->noContent();
    }

    public function comment(Request $request, PostComment $comment)
    {
        $this->validate($request, [
            'content' => 'required'
        ]);

        $subComment = new PostComment(
            $request->only('content')
        );
        $subComment->user()->associate(Auth::user());
        $subComment->comment()->associate($comment);
        $subComment->post()->associate($comment->post);
        $subComment->save();

        $comment->increment('comment_count');
        $comment->post->increment('comment_count');

        $comment->user->notify(new PostCommentCommented($subComment));

        return PostCommentResource::make($subComment);
    }

    public function destroy(PostComment $comment)
    {
        $this->authorize('own', $comment);

        $comment->delete();

        return response()->noContent();
    }
}
