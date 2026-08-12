<?php

namespace App\Providers;

use App\Repositories\Contracts\CourseRepositoryInterface;
use App\Repositories\CourseRepository;
use App\Repositories\BookRepository;
use App\Repositories\Contracts\BookRepositoryInterface;
use App\Repositories\CartRepository;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\OrderRepository;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CourseRepositoryInterface::class,
            CourseRepository::class
        );
		
		$this->app->bind(
			BookRepositoryInterface::class,
			BookRepository::class
		);
		
		$this->app->bind(
			CartRepositoryInterface::class,
			CartRepository::class
		);
		
		$this->app->bind(
			OrderRepositoryInterface::class,
			OrderRepository::class
		);
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}