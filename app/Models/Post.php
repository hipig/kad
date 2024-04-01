<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use Filterable;

    const VISIBLE_STATUS_COMMON = 1;
    const VISIBLE_STATUS_PRIVATE = 2;

    public static $visibleStatusMap = [
        self::VISIBLE_STATUS_COMMON => '公开',
        self::VISIBLE_STATUS_PRIVATE => '私密'
    ];

    protected $fillable = [
        'repost_post_id',
        'content',
        'visible_status'
    ];

    protected $casts = [
        'top_at' => 'datetime',
        'published_at' => 'datetime'
    ];

    protected $with  = [
        'selfCollect',
        'selfLike'
    ];

    protected $appends = [
        'is_self',
        'is_top',
        'is_liked',
        'is_collected',
        'visible_status_text'
    ];

    public function repostPost()
    {
        return $this->belongsTo(Post::class, 'repost_post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->belongsToMany(Upload::class, 'post_has_images', 'post_id', 'upload_id')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class, 'post_id');
    }

    public function repostUsers()
    {
        return $this->belongsToMany(User::class, 'post_reposts', 'post_id', 'user_id')->withTimestamps();
    }

    public function selfCollect()
    {
        return $this->hasOne(PostComment::class, 'post_id')->ofMany([
            'id' => 'max',
        ], function (Builder $query) {
            $query->where('user_id', Auth::id());
        });
    }

    public function selfLike()
    {
        return $this->hasOne(PostLike::class, 'post_id')->ofMany([
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

    protected function isCollected(): Attribute
    {
        return Attribute::get(function () {
            return !is_null($this->selfCollect);
        });
    }

    protected function isLiked(): Attribute
    {
        return Attribute::get(function () {
            return !is_null($this->selfLike);
        });
    }

    protected function isTop(): Attribute
    {
        return Attribute::get(function () {
            return !is_null($this->top_at);
        });
    }

    protected function visibleStatusText(): Attribute
    {
        return Attribute::get(function () {
            return self::$visibleStatusMap[$this->visible_status] ?? '';
        });
    }
}
