<?php

namespace Wikichua\Instant;

use App\Providers\RouteServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class InstantServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // moved to composer.json
        // $this->app->register(\Wikichua\Instant\Providers\HelpServiceProvider::class);
        // $this->app->register(\Wikichua\Instant\Providers\BrandServiceProvider::class);
        // $this->app->register(\Wikichua\Instant\Providers\ValidatorServiceProvider::class);

        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'wikichua');
        $this->loadMiddlewares();

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
            $this->bootForConsole();
        }

        $this->configSettings();
        $isNotBrand = \Help::getBrandNameByHost(request()->getHost());
        if ((isset(parse_url(config('app.url'))['host']) && parse_url(config('app.url'))['host'] == request()->getHost()) || is_null($isNotBrand)) {
            $this->loadWebRoutes();
            $this->gatePermissions();
            $this->registerLengthAwarePaginator();
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/instant.php', 'instant');

        // Register the service the package provides.
        $this->app->singleton('instant', function ($app) {
            return new Instant;
        });
    }

    public function provides()
    {
        return ['instant'];
    }

    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/instant.php' => config_path('instant.php'),
        ], 'instant.config');

        $this->commands([
            Console\Commands\Install::class,
            Console\Commands\Report::class,
        ]);
    }

    protected function loadWebRoutes()
    {
        Route::middleware('web')
            ->prefix(config('instant.route.root', 'dashboard'))
            ->group(function () {
                $files = cache()->rememberForever('setup:instant-web-routes-files', function () {
                    // load package routes
                    $files = File::files(__DIR__.'/../routes');
                    $out = [];
                    foreach ($files as $file) {
                        $out[] = $file->getPathname();
                    }
                    // load app routes
                    if (File::exists(app_path('../routes/instant'))) {
                        $files = File::files(app_path('../routes/instant/'));
                        foreach ($files as $file) {
                            $out[] = $file->getPathname();
                        }
                    }
                    return $out;
                });

                Route::group([], function () use ($files) {
                    foreach ($files as $file) {
                        include_once $file;
                    }
                });
            });

        if (RouteServiceProvider::HOME != config('instant.route.root', 'admin')) {
            Route::redirect(RouteServiceProvider::HOME, config('instant.route.root', 'admin'));
        }
        if (config('instant.route.root', 'admin') != '/') {
            Route::redirect('/', config('instant.route.root', 'admin'));
        }
    }

    protected function loadApiRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->group(function () {
                $files = cache()->rememberForever('setup:instant-api-routes-files', function () {
                    $files = File::files(__DIR__.'/../routes/api/');
                    $out = [];
                    foreach ($files as $file) {
                        $out[] = $file->getPathname();
                    }
                    if (File::exists(app_path('../routes/instant'))) {
                        $files = File::files(app_path('../routes/instant/'));
                        foreach ($files as $file) {
                            $out[] = $file->getPathname();
                        }
                    }
                    return $out;
                });
                Route::group([], function () use ($files) {
                    foreach ($files as $file) {
                        include_once $file;
                    }
                });
            });
    }

    protected function loadMiddlewares()
    {
        $this->app['router']->aliasMiddleware('intend_url', 'Wikichua\Instant\Http\Middleware\IntendUrl');
        $this->app['router']->aliasMiddleware('auth', 'Wikichua\Instant\Http\Middleware\Authenticate');
        $this->app['router']->aliasMiddleware('auth_admin', 'Wikichua\Instant\Http\Middleware\AuthAdmin');
        $this->app['router']->aliasMiddleware('reauth_admin', 'Wikichua\Instant\Http\Middleware\ReAuth');
        // $this->app['router']->aliasMiddleware('optimizeImages', 'Spatie\LaravelImageOptimizer\Middlewares\OptimizeImages');

        $this->app['router']->pushMiddlewareToGroup('web', \Wikichua\Instant\Http\Middleware\HandleInertiaRequests::class);
        $this->app['router']->pushMiddlewareToGroup('web', \Wikichua\Instant\Http\Middleware\PhpDebugBar::class);
        $this->app['router']->pushMiddlewareToGroup('web', \Wikichua\Instant\Http\Middleware\HttpsProtocol::class);
        $this->app['router']->pushMiddlewareToGroup('web', \Spatie\Honeypot\ProtectAgainstSpam::class);
        $this->app['router']->pushMiddlewareToGroup('web', \Spatie\LaravelImageOptimizer\Middlewares\OptimizeImages::class);
        $this->app['router']->pushMiddlewareToGroup('api', \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class);
    }

    protected function gatePermissions()
    {
        Gate::before(function ($user, $permission) {
            if ($user->hasPermission($permission)) {
                return true;
            }
        });
    }

    protected function configSettings()
    {
        if (Schema::hasTable('settings')) {
            cache()->rememberForever('setup:config-settings', function () {
                $settings = app(config('instant.Models.Setting'))->all();
                foreach ($settings as $setting) {
                    Config::set('settings.'.$setting->key, $setting->value);
                }
            });
        }
    }

    protected function registerLengthAwarePaginator()
    {
        $this->app->bind(LengthAwarePaginator::class, function ($app, $values) {
            return new class(...array_values($values)) extends LengthAwarePaginator {
                public function only(...$attributes)
                {
                    return $this->transform(function ($item) use ($attributes) {
                        return $item->only($attributes);
                    });
                }

                public function transform($callback)
                {
                    $this->items->transform($callback);

                    return $this;
                }

                public function toArray()
                {
                    return [
                        'data' => $this->items->toArray(),
                        'links' => $this->links(),
                    ];
                }

                public function links($view = null, $data = [])
                {
                    $this->appends(\Request::all());

                    $window = UrlWindow::make($this);

                    $elements = array_filter([
                        $window['first'],
                        is_array($window['slider']) ? '...' : null,
                        $window['slider'],
                        is_array($window['last']) ? '...' : null,
                        $window['last'],
                    ]);

                    return Collection::make($elements)->flatMap(function ($item) {
                        if (is_array($item)) {
                            return Collection::make($item)->map(function ($url, $page) {
                                return [
                                    'url' => $url,
                                    'label' => $page,
                                    'active' => $this->currentPage() === $page,
                                ];
                            });
                        } else {
                            return [
                                [
                                    'url' => null,
                                    'label' => '...',
                                    'active' => false,
                                ],
                            ];
                        }
                    })->prepend([
                        'url' => $this->previousPageUrl(),
                        'label' => 'Previous',
                        'active' => false,
                    ])->push([
                        'url' => $this->nextPageUrl(),
                        'label' => 'Next',
                        'active' => false,
                    ]);
                }
            };
        });
    }
}
