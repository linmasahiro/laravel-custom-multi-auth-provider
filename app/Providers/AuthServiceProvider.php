<?php

namespace App\Providers;

use App\Guards\AdminGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\UsersModel' => 'App\Policies\UsersModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // 註冊 Guard
        Auth::extend('admin', function ($app, $name, $config) {
            return new AdminGuard($name, new CustomUserProvider($app['hash'], 'App\Models\UsersModel'), $app['session.store']);
        });

        // 註冊Provider
        Auth::provider('myAuthProvider', function ($app, array $config) {
            // Return an instance of Illuminate\Contracts\Auth\UserProvider...
            return new CustomUserProvider($app['hash'], 'App\Models\UsersModel');
        });
    }
}
