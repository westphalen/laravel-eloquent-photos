<?php
/**
 * Created by PhpStorm.
 * User: sune
 * Date: 05/11/2017
 * Time: 12.13
 */

namespace Westphalen\Laravel\Photos\Providers;

class LaravelServiceProvider extends LumenServiceProvider
{
    /**
     * Boot the routes for the package.
     */
    public function boot()
    {
        parent::boot();

        // Load laravel routes.
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
    }
}
