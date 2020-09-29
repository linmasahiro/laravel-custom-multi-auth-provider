# 在 Laravel 上進行多權限登入管理

### Provider
 需求：
  - 該功能基於原有的 users 表不作變動的情況下，讓一位使用者有多個權限，並限制某權限能否登入該系統

 事前準備：
  - 安裝好 Laravel 並且最少建立起能夠執行登入登出的功能
  - 建立一張名為 role 的表，表內最少包含以下欄位
  - > id(int)
  - > user_id(int)
  - > role(int)

 開始：
  - 從原有的 Users.php 拷貝一個 UsersModel.php (沿用原有的 Users 也可以)
  - 建立一個 RoleModel.php
  - 在 UsersModel.php 內加上以下方法
    ```sh
        public function roles()
        {
            return $this->hasMany(RoleModel::class, 'user_id');
        }
    ```

  - 執行 php artisan make:provider CustomUserProvider 建立一個自己認證用的 Provider
  - 這個 Provider 必需繼承 EloquentUserProvider 或 UserProvider。並且換掉 retrieveByCredentials() 函數的執行內容來進行驗證。
    ```sh
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

    ```

  - 修改 AuthServiceProvider.php 來將寫好的 Provider 註冊
    ```sh
        Auth::provider('myAuthProvider', function ($app, array $config) {
            return new CustomUserProvider($app['hash'], 'App\Models\UsersModel');
        });
    ```

  - 修改 Auth.php 中預設的 eloquent provider 換成自己寫的 myAuthProvider
    ```sh
        ......
            'providers' => [
                'users' => [
                    'driver' => 'myAuthProvider', // 修改這裡
                    'model' => App\Models\UsersModel::class,
                ],
        ......
    ```

### Guard

 基本到這裡為止就算沒有實作這個 Guard 也已經完成一個最簡單的限制特定權限登入的功能。不過如果需要針對認證流程再作定義的話，可以在實作一個 Guard 來完成！

  - 建立一個管理員用的 Guard 叫做 AdminGuard（我放在 App\Guards 底下）
    ```sh
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
                // 自行定義驗證內容
                return parent::attempt($credentials, $remember);
            }
        }
    ```

  - 修改 AuthServiceProvider.php 來將寫好的 Guard 註冊
    ```sh
        Auth::extend('admin', function ($app, $name, $config) {
            return new AdminGuard($name, new CustomUserProvider($app['hash'], 'App\Models\UsersModel'), $app['session.store']);
        });
    ```


  - 增加 Auth.php 中的 guard
    ```sh
        ......
            'guards' => [
                'web' => [
                    'driver' => 'session',
                    'provider' => 'users',
                ],
                // 增加這邊
                'admin' => [
                    'driver' => 'admin',
                    'provider' => 'users',
                ],
        ......
    ```
  - 這樣就可以用以下的方式來進行登入
    ```sh
        if (auth('admin')->attempt($credentials, $remember)) {
            // dosomething
        }
    ```

  - 然後在像下面這樣設定路由的 guard 來限制哪些是管理者才能訪問的ＵＲＬ
    ```sh
        Route::middleware('auth:admin')->get('test',function() {
            echo 'test';
        });
    ```
