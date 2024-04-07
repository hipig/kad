<?php

namespace App\Models;


use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;

class PostComment extends Model
{
    protected $fillable = [
        'content'
    ];

    protected $with  = [
        'selfLike'
    ];

    protected $appends = [
        'is_self',
        'is_liked',
    ];

    public function comment()
    {
        return $this->belongsTo(PostComment::class, 'comment_id');
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class, 'comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function selfLike()
    {
        return $this->hasOne(PostCommentLike::class, 'comment_id')->ofMany([
            'id' => 'max',
        ], function (Builder $query) {
            $query->where('user_id', Auth::id());
        });
    }

    protected function isSelf(): Attribute
    {
        return Attribute::get(function () {
            return Auth::id() === $this->user_id;
        });
    }

    protected function isLiked(): Attribute
    {
        return Attribute::get(function () {
            return !is_null($this->selfLike);
        });
    }
}
