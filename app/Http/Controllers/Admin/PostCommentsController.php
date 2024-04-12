<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostCommentResource;
use App\ModelFilters\Admin\PostCommentFilter;
use App\Models\PostComment;
use Illuminate\Http\Request;

class PostCommentsController extends Controller
{
    public function index(Request $request)
    {
        $users = PostComment::filter($request->all(),PostCommentFilter::class)->with(['user', 'post'])->latest()->paginate($request->page_size ?? 15);

        return PostCommentResource::collection($users);
    }

    public function destroy(PostComment $comment)
    {
        $comment->delete();

        return response()->noContent();
    }
}
