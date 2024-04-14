<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    protected $fillable = [
        'type',
        'content'
    ];

    protected $casts = [
        'handled_at' => 'datetime'
    ];

    protected $appends = [
        'handle_type_text'
    ];

    const HANDLE_TYPE_NONE = 'NONE';
    const HANDLE_TYPE_DISABLE_USER  = 'DISABLE_USER';

    public static $handleTypeMap = [
        self::HANDLE_TYPE_NONE => '不处理',
        self::HANDLE_TYPE_DISABLE_USER => '禁用用户'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function handleTypeText(): Attribute
    {
        return Attribute::get(function () {
            return self::$handleTypeMap[$this->handle_type] ?? '';
        });
    }
}
