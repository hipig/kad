<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\TencentIM\TLSSigAPIv2;
use DateTimeInterface;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, Filterable;

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2;

    public static $statusMap  = [
        self::STATUS_ENABLE => '启用',
        self::STATUS_DISABLE => '禁用'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'nickname',
        'avatar',
        'wallet_account',
        'homepage_cover'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->nickname = '新用户' . Str::random(8);
            if (!$model->username) {
                $model->username = static::findAvailableUsername();
                if (!$model->username) {
                    return false;
                }
            }
        });
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'user_followers', 'follower_id', 'user_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_followers', 'user_id', 'follower_id');
    }

    public function postLikes()
    {
        return $this->hasMany(PostLike::class, 'user_id');
    }

    public function postCollects()
    {
        return $this->hasMany(PostCollect::class, 'user_id');
    }

    public function likePosts()
    {
        return $this->belongsToMany(Post::class, 'post_likes', 'user_id', 'post_id');
    }

    public function collectPosts()
    {
        return $this->belongsToMany(Post::class, 'post_collects', 'user_id', 'post_id');
    }



    protected function userSig() :Attribute
    {
        return Attribute::get(function () {
            $cacheKey = "user_sig:{$this->id}";
            $ttl = 86400 * 7;
            return Cache::remember($cacheKey, $ttl, function () use ($ttl) {
                return app(TLSSigAPIv2::class)->genUserSig($this->username, $ttl);
            });
        });
    }

    public static function findAvailableUsername()
    {
        for ($i = 0; $i < 10; $i++) {
            $username = Str::random(32);
            if (!static::query()->where('username', $username)->exists()) {
                return $username;
            }
        }
        \Log::warning('find username failed');

        return false;
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

}
