<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * 用戶模型
 *
 * @author k80092@hotmail.com
 */
class UsersModel extends Authenticatable
{
    use HasFactory;
    use Notifiable;


    /**
     * 表單名稱
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * 主鍵
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 用戶權限
     *
     * @return void
     */
    public function roles()
    {
        return $this->hasMany(RoleModel::class, 'user_id');
    }

    /**
     * 有無某權限
     *
     * @param int $role 權限ID
     *
     * @return boolean
     */
    public function hasRole($role)
    {
        if (is_int($role)) {
            return $this->roles->contains('role', $role);
        }

        return !!$role->intersect($this->roles)->count();
    }
}
