<?php

namespace App\Providers;

use App\Models\SampleTest;
use App\Models\TestReport;
use App\Models\TestResult;
use App\Observers\SampleTestObserver;
use App\Observers\TestReportObserver;
use App\Observers\TestResultObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        SampleTest::observe(SampleTestObserver::class);
        TestResult::observe(TestResultObserver::class);
        TestReport::observe(TestReportObserver::class);
    }
}
