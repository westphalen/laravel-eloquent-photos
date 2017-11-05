<?php
/**
 * Created by PhpStorm.
 * User: sune
 * Date: 05/11/2017
 * Time: 12.38
 */

namespace Westphalen\Laravel\Photos\Providers;

use Illuminate\Support\ServiceProvider;

class LumenServiceProvider extends ServiceProvider
{
    /**
     * Register default config.
     */
    public function register()
    {
        // Merge default config values.
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/photo.php', 'photo'
        );
    }

    /**
     * Load config and migrations for the package.
     */
    public function boot()
    {
        // Load migration.
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Publish config.
        $this->publishes([
            __DIR__ . '/../../config/photo.php' => config_path('photo.php'),
        ]);
    }
}
