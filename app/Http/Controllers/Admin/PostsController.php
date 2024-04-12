<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\ModelFilters\Admin\PostFilter;
use App\Models\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function index(Request $request)
    {
        $users = Post::filter($request->all(), PostFilter::class)->with(['user', 'images'])->latest()->paginate($request->page_size ?? 15);

        return PostResource::collection($users);
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return response()->noContent();
    }
}
