# Eloquent Photos for Laravel

Simple package for handling photos in Laravel/Lumen.

The photos are flexible and can be used in relationship with users or any other Eloquent model.

# Installation

#### Add the package to `composer.json`:

    ...
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/westphalen/laravel-eloquent-photos"
        }
    ],
    ...
    "require": {
        ...
        "westphalen/laravel-eloquent-photos": "dev-master"
    }
    ...
    
#### Include the service provider in your project.

For Laravel you may add it to `config/app.php`:

    'providers' => [
        // Other Service Providers
    
        \Westphalen\Laravel\Photos\Providers\LaravelServiceProvider::class
    ],

or for Lumen, you can add it to `bootstrap/app.php`:

    ...
    $app->register(\Westphalen\Laravel\Photos\Providers\LumenServiceProvider::class);

#### Run the migrations (to add the `photos` table):

    php artisan migrate    


#### Utilize the routes

You may use the bundled `routes/web.php` or you can add it yourself: 

    Route::resource('photo', \Westphalen\Laravel\Photos\Controllers\PhotoController::class);

#### Start uploading photos.

Upload photos to `POST /photo`. You may pass an HTML file (from `<input type="file">`) to the `file` input parameter, or send a base64 data string to the `data` input parameter.

A `Photo` model will be returned, with a `url` parameter that can be used to display/download the photo. For example: `/photo/abcdef-1234.jpg`.

#### Attach photos to users.

You can create a pivot table, `photo_user` and use a `many-to-many` relationship on your `User` model like so:

    public function photos()
    {
        return $this->belongsToMany(Photo::class);
    }

and start attaching photos to users.

## Special for Lumen

As this package utilizes Laravel's `Storage` helpers, you need to activate this functionality in Lumen or you will see an error message like: `Target [Illuminate\Contracts\Filesystem\Factory] is not instantiable.` 

### Activating Laravel Storage Filesystem

Require dependencies:

    $ composer require "league/flysystem: ~1.0"
    

In your `bootstrap/app.php` add the binding:

    $app->singleton(
        Illuminate\Contracts\Filesystem\Factory::class,
        function ($app) {
            return new Illuminate\Filesystem\FilesystemManager($app);
        }
    );
    
If you haven't got it, create `config/filesystems.php` and load it in `bootstrap/app.php`:

    $app->configure('filesystems');
    
The contents of `config/filesystems.php` should at least define the default disk and its driver.

*Consider just copying it from Laravel: https://github.com/laravel/laravel/blob/master/config/filesystems.php*

    return [
        'default' => 'local',
        'disks' => [
            'local' => [
                'driver' => 'local',
                'root' => storage_path('app'),            
            ],                
        ],
    ];
