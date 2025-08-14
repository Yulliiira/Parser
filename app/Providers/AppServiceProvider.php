<?php

namespace App\Providers;

use App\Contracts\LogAnalyticsServiceInterface;
use App\Contracts\LogParser\FormatInterface;
use App\Contracts\LogParser\PatternInterface;
use App\Services\LogAnalyticsService;
use App\Services\LogParser\Format;
use App\Services\LogParser\Pattern;
use Illuminate\Support\ServiceProvider;

use App\Contracts\LogParserServiceInterface;
use App\Contracts\LogRepositoryInterface;
use App\Repositories\LogRepository;
use App\Services\LogParserService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogParserServiceInterface::class, LogParserService::class);
        $this->app->bind(LogRepositoryInterface::class, LogRepository::class);
        $this->app->bind(LogAnalyticsServiceInterface::class, LogAnalyticsService::class);
        $this->app->bind(PatternInterface::class, Pattern::class );
        $this->app->bind(FormatInterface::class, Format::class );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
