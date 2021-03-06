<?php

/** @noinspection PropertyInitializationFlawsInspection */

namespace App\Providers;

use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $isAdmin = static fn(User $user) => $user->is_admin;
        \Gate::define('access_users', $isAdmin);
        \Gate::define('manage_organizations', $isAdmin);
    }
}
