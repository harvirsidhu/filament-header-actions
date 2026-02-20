<?php

namespace Harvirsidhu\FilamentHeaderActions;

use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Harvirsidhu\FilamentHeaderActions\Commands\FilamentHeaderActionsCommand;
use Harvirsidhu\FilamentHeaderActions\Testing\TestsFilamentHeaderActions;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentHeaderActionsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-header-actions';

    public static string $viewNamespace = 'filament-header-actions';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('harvirsidhu/filament-header-actions');
            });

        if (file_exists($package->basePath('/../config/header-actions.php'))) {
            $package->hasConfigFile('header-actions');
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(FilamentHeaderActions::class, fn (): FilamentHeaderActions => new FilamentHeaderActions);
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Testing
        Testable::mixin(new TestsFilamentHeaderActions);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'harvirsidhu/filament-header-actions';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('filament-header-actions', __DIR__ . '/../resources/dist/components/filament-header-actions.js'),
            // Css::make('filament-header-actions-styles', __DIR__ . '/../resources/dist/filament-header-actions.css'),
            // Js::make('filament-header-actions-scripts', __DIR__ . '/../resources/dist/filament-header-actions.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentHeaderActionsCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

}
