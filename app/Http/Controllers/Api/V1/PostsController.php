<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PostRequest;
use App\Http\Resources\PostCollectResource;
use App\Http\Resources\PostCommentResource;
use App\Http\Resources\PostLikeResource;
use App\Http\Resources\PostResource;
use App\ModelFilters\PostFilter;
use App\Models\Post;
use App\Models\PostCollect;
use App\Models\PostComment;
use App\Models\PostLike;
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
        $post = new Post(
            $request->only('content', 'visible_status')
        );
        $post->published_at = now();
        $post->user()->associate(Auth::user());
        $post->save();

        return PostResource::make($post);
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return response()->noContent();
    }

    public function comment(Request $request, Post $post)
    {
        $comment = new PostComment(
            $request->only('content')
        );
        $comment->user()->associate(Auth::user());
        $comment->post()->associate($post);
        $comment->save();

        return PostCommentResource::make($comment);
    }

    public function like(Post $post)
    {
        $like = new PostLike();
        $like->user()->associate(Auth::user());
        $like->post()->associate($post);
        $like->save();

        return PostLikeResource::make($like);
    }


    public function unLike(Post $post)
    {
        $like = PostLike::query()->where('user_id', Auth::id())->where('post_id', $post->id)->first();
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

        return PostCollectResource::make($collect);
    }


    public function unCollect(Post $post)
    {
        $collect = PostCollect::query()->where('user_id', Auth::id())->where('post_id', $post->id)->first();
        if ($collect) {
            $collect->delete();
        }

        return response()->noContent();
    }
}
