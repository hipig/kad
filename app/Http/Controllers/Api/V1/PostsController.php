<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PostRequest;
use App\Http\Resources\PostCollectResource;
use App\Http\Resources\PostCommentResource;
use App\Http\Resources\PostLikeResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\ReportResource;
use App\ModelFilters\Api\PostFilter;
use App\Models\Post;
use App\Models\PostCollect;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\Report;
use App\Notifications\PostCollected;
use App\Notifications\PostCommented;
use App\Notifications\PostLiked;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostsController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::filter($request->all(), PostFilter::class)->paginate($request->page_size ?? 15);

        return PostResource::collection($posts);
    }

    public function store(PostRequest $request)
    {
        $content = $request->input('content');
        $visibleStatus = $request->input('visible_status') ?? Post::VISIBLE_STATUS_COMMON;
        $post = new Post([
            'content' => $content,
            'visible_status' => $visibleStatus
        ]);
        $post->published_at = now();
        $post->user()->associate(Auth::user());
        $post->save();

        $post->images()->sync($request->image_ids);

        return PostResource::make($post);
    }

    public function destroy(Post $post)
    {
        $this->authorize('own', $post);

        $post->delete();

        $post->images()->delete();

        return response()->noContent();
    }

    public function report(Request $request, Post $post)
    {
        $this->validate($request, [
            'type' => 'required'
        ]);

        $authUser = Auth::user();

        if ($authUser->id === $post->user_id) {
            throw new InvalidRequestException('不要举报自己的动态哦');
        }

        $report = new Report(
            $request->only([
                'type',
                'content'
            ])
        );
        $report->user()->associate($authUser);
        $report->reportable()->associate($post);
        $report->save();

        return ReportResource::make($report);
    }

    public function comment(Request $request, Post $post)
    {
        $comment = new PostComment(
            $request->only('content')
        );
        $comment->user()->associate(Auth::user());
        $comment->post()->associate($post);
        $comment->save();

        $post->user->notify(new PostCommented($comment));

        return PostCommentResource::make($comment);
    }

    public function like(Post $post)
    {
        $like = new PostLike();
        $like->user()->associate(Auth::user());
        $like->post()->associate($post);
        $like->save();

        $post->user->notify(new PostLiked($like));

        return PostLikeResource::make($like);
    }


    public function unLike(Post $post)
    {
        $like = PostLike::query()->where('user_id', Auth::id())->where('post_id', $post->id)->first();

        $this->authorize('own', $like);

        if ($like) {
            $like->delete();
        }

        return response()->noContent();
    }



    public function collect(Post $post)
    {
        $collect = new PostCollect();
        $collect->user()->associate(Auth::user());
        $collect->post()->associate($post);
        $collect->save();

        $post->user->notify(new PostCollected($collect));

        return PostCollectResource::make($collect);
    }


    public function unCollect(Post $post)
    {
        $collect = PostCollect::query()->where('user_id', Auth::id())->where('post_id', $post->id)->first();

        $this->authorize('own', $collect);

        if ($collect) {
            $collect->delete();
        }

        return response()->noContent();
    }
}
