<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Servers;
use App\Models\Chapters;
use App\Models\Topics;
use App\Models\Posts;
use App\Policies\ServersPolicy;
use App\Policies\ChaptersPolicy;
use App\Policies\TopicsPolicy;
use App\Policies\PostsPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Servers::class => ServersPolicy::class,
        Chapters::class => ChaptersPolicy::class,
        Topics::class => TopicsPolicy::class,
        Posts::class => PostsPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
