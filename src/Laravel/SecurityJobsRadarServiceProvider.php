<?php

declare(strict_types=1);

namespace URLCV\SecurityJobsRadar\Laravel;

use Illuminate\Support\ServiceProvider;

class SecurityJobsRadarServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'security-jobs-radar');
    }
}
