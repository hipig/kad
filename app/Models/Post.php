<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'content',
        'visible_status'
    ];

    protected $casts = [
        'published_at' => 'datetime'
    ];

    protected $appends = [
        'visible_status_text'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->belongsToMany(Upload::class, 'post_has_images', 'post_id', 'upload_id')->withTimestamps();
    }

    protected function visibleStatusText(): Attribute
    {
        return Attribute::get(function () {
            return self::$visibleStatusMap[$this->visible_status] ?? '';
        });
    }
}
