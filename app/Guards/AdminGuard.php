<?php

namespace App\Guards;

use Illuminate\Auth\SessionGuard;

/**
 * 管理者守門員
 *
 * @author LIN CHENGHUNG <k80092@hotmail.com>
 */
class AdminGuard extends SessionGuard
{
    /**
     * 登入驗證
     *
     * @param  array  $credentials
     * @param  bool  $remember
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        // 自行定義驗證內容，這次不做修改
        parent::attempt($credentials, $remember);
    }
}
