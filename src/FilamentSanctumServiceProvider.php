<?php

namespace Eightygrit\FilamentSanctum;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentSanctumServiceProvider extends PackageServiceProvider
{

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-sanctum')
            ->hasViews()
            ->hasConfigFile()
            ->hasAssets('filament-sanctum')
            ->hasTranslations();
    }
}
