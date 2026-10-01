<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Company::class => \App\Policies\CompanyPolicy::class,
        \App\Models\Team::class => \App\Policies\TeamPolicy::class,
        \App\Models\Channel::class => \App\Policies\ChannelPolicy::class,
        \App\Models\Message::class => \App\Policies\MessagePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability, $args = []) {
            if (! $user) {
                return false;
            }

            if ($ability === 'view' || $ability === 'manageMembers') {
                return null;
            }

            return null;
        });
    }
}
