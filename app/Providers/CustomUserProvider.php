<?php

namespace App\Providers;

use App\Models\UsersModel;
use Illuminate\Auth\EloquentUserProvider;

/**
 * 自定義登入時驗證項目
 *
 * @author LIN CHENGHUNG <k80092@hotmail.com>
 */
class CustomUserProvider extends EloquentUserProvider
{
    /**
     * 透過驗證資訊來比對身份
     *
     * @param array $credentials 驗證資訊
     *
     * @return Illuminate\Contracts\Auth\Authenticatable
     */
    public function retrieveByCredentials(array $credentials)
    {
        $user = UsersModel::whereHas('roles', function ($query) {
            $role = 1; // 這是你的role的代號
            $query->whereRole($role);
        })->where('email', '=', $credentials['email'])->first();

        return $user;
    }
}
