<?php

namespace Hanan\FlightReservation;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FlightReservationServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-flight-reservation')
            ->hasConfigFile('flight-reservation')
            ->hasViews()
            ->hasMigrations([
                'create_flights_table',
            ])
            ->hasRoute('web')
            ->hasRoute('api');



            
    }

    public function packageBooted(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
    
    
    public function packageRegistered(): void
    {
      
    }
   
}