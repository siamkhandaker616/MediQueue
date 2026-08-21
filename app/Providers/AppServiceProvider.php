<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            if (Auth::check() && ($view->getName() === 'components.layouts.staff' || str_starts_with($view->getName(), 'doctor.') || str_starts_with($view->getName(), 'admin.'))) {
                $view->with('navigation', $this->navigationFor(Auth::user()));
            }
        });
    }

    private function navigationFor($user): array
    {
        if ($user->isDoctor()) {
            return [
                ['route' => 'doctor.dashboard', 'label' => 'Queue dashboard', 'icon' => 'M3 4h13M3 8h9m-9 4h6m4 0l4-4 4 4m-8 0l4 4 4-4M3 16h15'],
                ['route' => 'doctor.schedule', 'label' => 'Schedule & leave', 'icon' => 'M8 7V3m8 4V3M3 9h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['route' => 'doctor.prescriptions.index', 'label' => 'Prescriptions', 'icon' => 'M9 12h6m-6 4h6M9 8h6m-8 12h10a2 2 0 002-2V6a2 2 0 00-2-2H9l-5 5v11a2 2 0 002 2z'],
            ];
        }

        return [
            ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'M4 5a2 2 0 012-2h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 5h10M7 13h6m-6 4h4'],
            ['route' => 'admin.reviews', 'label' => 'Reviews', 'icon' => 'M11.48 3.5a.56.56 0 011.04 0l2.12 5.11.55.35 5.52.44c.5.04.7.66.32.99l-4.2 3.6-.18.56 1.28 5.39a.56.56 0 01-.84.6l-4.72-2.88-.49-.3-.5.3-4.72 2.89a.56.56 0 01-.84-.61l1.29-5.39-.18-.56-4.2-3.6a.56.56 0 01.32-.99l5.51-.44.55-.35 2.13-5.11z'],
            ['route' => 'admin.analytics', 'label' => 'Analytics', 'icon' => 'M4 20V10m5 10V4m5 16v-8m5 8v-4'],
            ['route' => 'admin.refunds', 'label' => 'Refunds', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['route' => 'admin.departments.index', 'label' => 'Departments', 'icon' => 'M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm3 3h4m-4 4h4m6-4h1m-1 4h1'],
            ['route' => 'admin.doctors.index', 'label' => 'Doctors', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM5 21a7 7 0 0114 0'],
        ];
    }
}

