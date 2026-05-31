<?php

namespace Stephenjude\FilamentFeatureFlag\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\Livewire\Partials\DataStoreOverride;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Laravel\Pennant\PennantServiceProvider;
use Livewire\LivewireServiceProvider;
use Livewire\Mechanisms\DataStore;
use Orchestra\Testbench\TestCase as Orchestra;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;
use Stephenjude\FilamentFeatureFlag\FeatureFlagPluginServiceProvider;
use Stephenjude\FilamentFeatureFlag\Traits\WithFeatureResolver;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Filament\Support\SupportServiceProvider binds DataStore::class to
        // DataStoreOverride::class via a plain bind(), which overrides Livewire's
        // instance() registration and creates a new DataStoreOverride on every
        // app(DataStore::class) call. Re-register as a singleton so the WeakMap
        // inside DataStore persists across all calls within a single request.
        $this->app->singleton(DataStore::class, DataStoreOverride::class);

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Stephenjude\\FilamentFeatureFlag\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        $providers = [
            ActionsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SchemasServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            PennantServiceProvider::class,
            FeatureFlagPluginServiceProvider::class,
            TestPanelProvider::class,
        ];

        // blade-capture-directive became a Filament dependency in v5.6.x.
        // Skip it on older Filament installs where the package isn't present.
        if (class_exists(BladeCaptureDirectiveServiceProvider::class)) {
            array_unshift($providers, BladeCaptureDirectiveServiceProvider::class);
        }

        return $providers;
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        config()->set('database.default', 'testing');

        config()->set('pennant.default', 'array');

        $migration = include __DIR__.'/../database/migrations/create_filament_feature_flags_table.php.stub';
        $migration->up();
    }
}

/**
 * @class dummy feature for testing
 */
class TestFeature
{
    use WithFeatureResolver;
}
