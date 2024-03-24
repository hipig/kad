<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PostComment;
use Illuminate\Http\Request;

class PostCommentsController extends Controller
{
    public function destroy(PostComment $comment)
    {
        $this->authorize('own', $comment);

        $comment->delete();

        return response()->noContent();
    }
}
