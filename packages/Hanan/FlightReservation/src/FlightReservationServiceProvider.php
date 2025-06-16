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
            ->hasRoute('web');



            
    }

    public function packageBooted(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
    
    
    public function packageRegistered(): void
    {
      
    }
   
}