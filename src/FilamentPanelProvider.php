<?php

namespace Eightygrit\FilamentSanctum;

use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Support\Assets\Css;
use Filament\Navigation\MenuItem;
use Filament\Support\Facades\FilamentAsset;
use Eightygrit\FilamentSanctum\Pages\Sanctum;
use Filament\Panel;

class FilamentPanelProvider implements Plugin
{
	public function getId(): string
	{
		return 'filament-sanctum';
	}

	public function register(Panel $panel): void
	{
		// Register the page
		if (config('filament-sanctum.navigation.should_register', true)) {
			$panel->pages([
				Sanctum::class,
			]);
		}

		// Register assets
		FilamentAsset::register([
			Css::make('filament-sanctum', __DIR__ . '/../resources/dist/app.css'),
		]);
	}

	public function boot(Panel $panel): void
	{
		// Register user menu items
		if (config('filament-sanctum.user_menu')) {
			Filament::serving(function () {
				Filament::registerUserMenuItems([
					MenuItem::make()
						->label(trans(config('filament-sanctum.label')))
						->url(route('filament.pages.' . config('filament-sanctum.slug')))
						->icon('heroicon-o-finger-print'),
				]);
			});
		}
	}
}
