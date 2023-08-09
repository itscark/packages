<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\TotalApplications;
use App\Nova\Metrics\TotalPackages;
use App\Nova\Metrics\TotalDownloads;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Dashboards\Main as Dashboard;

class Main extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            new TotalDownloads,
            new TotalApplications,
            new TotalPackages,
        ];
    }
}
