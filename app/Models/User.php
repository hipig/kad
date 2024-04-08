<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\TencentIM\TLSSigAPIv2;
use DateTimeInterface;
use EloquentFilter\Filterable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
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

    public static $associatedFieldMap = [
        'nickname' => 'Tag_Profile_IM_Nick',
        'avatar' => 'Tag_Profile_IM_Image',
        'wallet_account' => 'Tag_Profile_Custom_Wallet',
        'gender' => 'Tag_Profile_IM_Gender',
        'birthday' => 'Tag_Profile_IM_BirthDay',
        'location' => 'Tag_Profile_IM_Location',
        'self_signature' => 'Tag_Profile_IM_SelfSignature',
        'allow_type' => 'Tag_Profile_IM_AllowType',
        'language' => 'Tag_Profile_IM_Language',
        'admin_forbid_type' => 'Tag_Profile_IM_AdminForbidType',
        'level' => 'Tag_Profile_IM_Level',
        'role' => 'Tag_Profile_IM_Role'
    ];

    const USERNAME_ADMINISTRATOR = 'administrator';

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
        'homepage_cover',
        'gender',
        'location',
        'birthday',
        'self_signature',
        'allow_type',
        'language',
        'level',
        'role',
        'admin_forbid_type',
        'status'
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
        'defined_data' => 'json'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->nickname) {
                $model->nickname = '新用户' . Str::random(8);
            }
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

    protected function isFollower(): Attribute
    {
        return Attribute::get(function () {
            $follower = UserFollower::query()->where('follower_id', Auth::id())->where('user_id', $this->id)->first();
            return !is_null($follower);
        });
    }

    protected function isFollowing(): Attribute
    {
        return Attribute::get(function () {
            $following = UserFollower::query()->where('follower_id', $this->id)->where('user_id', Auth::id())->first();
            return !is_null($following);
        });
    }

    protected function avatar(): Attribute
    {
        return Attribute::make(
            function ($value) {
                if (empty($value) || Str::startsWith($value, ['http://', 'https://'])) {
                    return $value;
                }

                return Storage::disk('upload')->url($value);
            }
        );
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
