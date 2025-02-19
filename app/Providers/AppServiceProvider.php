<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use App\Models\CompletedJobs;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      Paginator::useBootstrap();
		Schema::defaultStringLength(191);
		if (config('app.env') !== 'local' && config('app.env') !== 'demo') {
            $this->app['request']->server->set('HTTPS','on');
            URL::forceScheme('https');
		}

        Queue::after(function (JobProcessed $event) {
            $c = new CompletedJobs();
            $c->payload = json_encode($event->job->payload());
            $c->save();
        });

    }
}
