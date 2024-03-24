<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class AdminUser extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2;
    public static $statusMap = [
        self::STATUS_ENABLE => '启用',
        self::STATUS_DISABLE => '禁用'
    ];

    protected $fillable = [
        'username',
        'name',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];
}
