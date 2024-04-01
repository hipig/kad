<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PostRequest;
use App\Http\Resources\PostBlockResource;
use App\Http\Resources\PostCollectResource;
use App\Http\Resources\PostCommentResource;
use App\Http\Resources\PostLikeResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\ReportResource;
use App\ModelFilters\Api\PostFilter;
use App\Models\Post;
use App\Models\PostBlock;
use App\Models\PostCollect;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\PostRepost;
use App\Models\Report;
use App\Notifications\PostCollected;
use App\Notifications\PostCommented;
use App\Notifications\PostLiked;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostsController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::filter($request->all(), PostFilter::class)->with(['user', 'images', 'comments', 'comments.user', 'repostPost', 'repostUsers'])->paginate($request->page_size ?? 15);
        $authId = Auth::id();


        foreach ($posts->getCollection() as $post) {
            $post->is_self = $authId === $post->user_id;
        }

        return PostResource::collection($posts);
    }

    public function store(PostRequest $request)
    {
        $post = DB::transaction(function () use($request) {
            $repostPostId = $request->input('repost_post_id');
            $content = $request->input('content');
            $visibleStatus = $request->input('visible_status') ?? Post::VISIBLE_STATUS_COMMON;
            $post = new Post([
                'repost_post_id' => $repostPostId,
                'content' => $content,
                'visible_status' => $visibleStatus
            ]);
            $post->published_at = now();
            $post->user()->associate(Auth::user());
            $post->save();

            $post->images()->sync($request->image_ids);
            $post->save();

            if ($post->repostPost) {
                $post->repostPost->increment('repost_count');
                $repost = new PostRepost();
                $repost->post()->associate($post->repostPost);
                $repost->user()->associate(Auth::user());
                $repost->repostedPost()->associate($post);
                $repost->save();
            }

            return $post;
        });

        return PostResource::make($post);
    }

    public function show(Post $post)
    {
        $post->load(['user', 'images', 'comments', 'comments.user', 'repostPost', 'repostUsers']);
        return PostResource::make($post);
    }

    public function destroy(Post $post)
    {
        $this->authorize('own', $post);

        $post->delete();

        $post->images()->delete();

        return response()->noContent();
    }

    public function setVisible(Post $post)
    {
        $post->visible_status = $post->visible_status === Post::VISIBLE_STATUS_COMMON ? Post::VISIBLE_STATUS_PRIVATE : Post::VISIBLE_STATUS_COMMON;
        $post->save();

        return PostResource::make($post);
    }

    public function setTop(Post $post)
    {
        $post->top_at = is_null($post->top_at) ? now() : null;
        $post->save();

        return PostResource::make($post);
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
        $this->validate($request, [
            'content' => 'required'
        ]);

        $comment = new PostComment(
            $request->only('content')
        );
        $comment->user()->associate(Auth::user());
        $comment->post()->associate($post);
        $comment->save();

        $post->increment('comment_count');

        $post->user->notify(new PostCommented($comment));

        return PostCommentResource::make($comment);
    }

    public function like(Post $post)
    {
        $user = Auth::user();
        $like = PostLike::query()->where('user_id', $user->id)->where('post_id', $post->id)->first();
        if (!$like) {
            $like = new PostLike();
            $like->user()->associate($user);
            $like->post()->associate($post);
            $like->save();

            $post->user->notify(new PostLiked($like));
        }

        $post->increment('like_count');

        return PostLikeResource::make($like);
    }


    public function unLike(Post $post)
    {
        $like = PostLike::query()->where('user_id', Auth::id())->where('post_id', $post->id)->first();

        if ($like) {
            $this->authorize('own', $like);
            $like->delete();

            $post->decrement('like_count');
        }

        return response()->noContent();
    }



    public function collect(Post $post)
    {
        $user = Auth::user();
        $collect = PostCollect::query()->where('user_id', $user->id)->where('post_id', $post->id)->first();

        if (!$collect) {
            $collect = new PostCollect();
            $collect->user()->associate($user);
            $collect->post()->associate($post);
            $collect->save();

            $post->increment('collect_count');

            $post->user->notify(new PostCollected($collect));
        }

        return PostCollectResource::make($collect);
    }

    public function unCollect(Post $post)
    {
        $collect = PostCollect::query()->where('user_id', Auth::id())->where('post_id', $post->id)->first();

        if ($collect) {
            $this->authorize('own', $collect);
            $collect->delete();

            $post->decrement('collect_count');
        }

        return response()->noContent();
    }

    public function block(Post $post)
    {
        $block = PostBlock::query()->where('user_id', Auth::id())->where('post_id', $post->id)->first();

        if (!$block) {
            $block = new PostBlock();
            $block->user()->associate(Auth::user());
            $block->post()->associate($post);
            $block->save();
        }

        return PostBlockResource::make($block);
    }

    public function unBlock(Post $post)
    {
        $block = PostBlock::query()->where('user_id', Auth::id())->where('post_id', $post->id)->first();

        if ($block) {
            $this->authorize('own', $block);
            $block->delete();
        }

        return response()->noContent();
    }
}
