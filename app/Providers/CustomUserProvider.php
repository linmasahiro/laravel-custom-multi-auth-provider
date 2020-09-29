<?php

namespace App\Providers;

use App\Models\UsersModel;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Arrayable;

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
        if (
            empty($credentials) ||
            (count($credentials) === 1 &&
            Str::contains($this->firstCredentialKey($credentials), 'password'))
        ) {
            return;
        }

        $query = $this->newModelQuery();

        // 追加條件
        $query = $query->whereHas('roles', function ($query) {
                 $query->whereRole(config('const.ROLE')['ADMIN']);
        });

        foreach ($credentials as $key => $value) {
            if (Str::contains($key, 'password')) {
                continue;
            }

            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }
}
