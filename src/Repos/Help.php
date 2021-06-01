<?php

namespace Wikichua\Instant\Repos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Help
{
    public function qs_url($path = null, $qs = [], $secure = null)
    {
        $url = app('url')->to($path, $secure);
        if (count($qs)) {
            foreach ($qs as $key => $value) {
                $qs[$key] = sprintf('%s=%s', $key, urlencode($value));
            }
            $url = sprintf('%s?%s', $url, implode('&', $qs));
        }

        return $url;
    }

    public function prettyPrintJson($value = '')
    {
        return stripcslashes(json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    public function settings($name, $default = '')
    {
        if (!is_array(config('settings.'.$name)) && json_decode(config('settings.'.$name), 1)) {
            return json_decode(config('settings.'.$name), 1) ? json_decode(config('settings.'.$name), 1) : $default;
        }

        return config('settings.'.$name, $default);
    }

    public function rebuildUrl($url, $params = [])
    {
        if (count($params)) {
            $parsedUrl = parse_url($url);
            if (null == $parsedUrl['path']) {
                $url .= '/';
            }
            $separator = (null == $parsedUrl['query']) ? '?' : '&';

            return $url .= $separator.http_build_query($params);
        }

        return $url;
    }

    public function findHashTag($string)
    {
        preg_match_all('/#(\\w+)/', $string, $matches);

        return $matches[1];
    }

    public function getModels()
    {
        $autoload = array_keys(include base_path('/vendor/composer/autoload_classmap.php'));
        $models = [];
        foreach ($autoload as $namespace) {
            if (Str::contains($namespace, ['Wikichua','Brand','App']) && !in_array($namespace, ['Wikichua\Instant\Models\User','Wikichua\Instant\Models\Searchable'])) {
                if (Str::contains($namespace, 'Models')) {
                    $models[] = $namespace;
                }
            }
        }
        return array_unique($models);
    }

    public function getModelsList()
    {
        return $this->getModels();
    }

    public function opendns()
    {
        return trim(shell_exec('dig +short myip.opendns.com @resolver1.opendns.com'));
    }

    public function iplocation($ip = '')
    {
        if ('' == $ip) {
            $ip = Cache::remember('sessions-ip:'.session()->getId(), (60 * 60 * 24 * 30), function () {
                return $this->opendns();
            });
        }

        return Cache::remember('iplocation:'.$ip, (60 * 60 * 24 * 30), function () use ($ip) {
            $fields = [
                'status', 'message', 'continent', 'continentCode', 'country', 'countryCode', 'region', 'regionName', 'city', 'district', 'zip', 'lat', 'lon', 'timezone', 'offset', 'currency', 'isp', 'org', 'as', 'asname', 'reverse', 'mobile', 'proxy', 'hosting', 'query',
            ];

            return json_decode(\Http::get('//ip-api.com/json/'.$ip, ['fields' => implode(',', $fields)]), 1);
        }) + ['locale' => request()->route('locale')];
    }

    public function agent()
    {
        return new \Jenssegers\Agent\Agent();
    }

    public function agents($key = '')
    {
        $agent = $this->agent();
        $data = [
            'languages' => $agent->languages(),
            'device' => $agent->device(),
            'platform' => $agent->platform(),
            'platform_version' => $agent->version($agent->platform()),
            'browser' => $agent->browser(),
            'browser_version' => $agent->version($agent->browser()),
            'isDesktop' => $agent->isDesktop(),
            'isPhone' => $agent->isPhone(),
            'isRobot' => $agent->isRobot(),
            'headers' => request()->headers->all(),
            'ips' => request()->ips(),
        ];
        if ('' != $key && isset($data[$key])) {
            return $data[$key];
        }

        return $data;
    }

    public function audit($message, $data = [], $model = null, $ip = '')
    {
        // unset hidden form fields
        foreach (['_token', '_method', '_submit'] as $unset_key) {
            if (isset($data[$unset_key])) {
                unset($data[$unset_key]);
            }
        }
        if ('' == $ip) {
            $ip = $this->opendns();
        }

        app(config('instant.Models.Audit'))->create([
            'user_id' => auth()->check() ? auth()->user()->id : 1,
            'model_id' => $model ? $model->id : null,
            'model_class' => $model ? get_class($model) : null,
            'message' => $message,
            'data' => $data ? $data : null,
            'brand_id' => auth()->check() ? auth()->user()->brand_id : null,
            'opendns' => $ip,
            'agents' => $this->agents(),
            'iplocation' => $this->iplocation($ip),
        ]);
    }

    public function scan_langs_dir()
    {
        $locales = [];
        $iterator = new DirectoryIterator(resource_path('lang'));
        foreach ($iterator as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $locales[] = $fileinfo->getFilename();
            }
        }

        return $locales;
    }

    public function pushered($data, $channel = '', $event = 'general', $locale = 'en', $driver = '')
    {
        if ('' == $driver) {
            $driver = config('instant.broadcast.driver');
        }
        if ('' == $driver) {
            return false;
        }
        $actual_data = [];
        if (is_object($data)) {
            return false;
        }
        if (!is_array($data)) {
            if (json_decode($data)) {
                $data = json_decode($data, 1);
            } else {
                $actual_data['message'] = trim($data);
            }
        }
        $actual_data['sender_id'] = sha1(
            $data['sender_id'] ?? (
                auth()->check() ? auth()->id() : 0
            )
        );
        if (is_array($data)) {
            if (isset($data['message'])) {
                $actual_data = array_merge($actual_data, $data);
            } else {
                $actual_data['message'] = implode('<br />', $data);
            }
        }

        $channel = sha1('' != $channel ? $channel : config('app.name'));
        $event = sha1($event.'-'.$locale);
        $config = config('broadcasting.connections.'.$driver);
        if ('pusher' == $driver) {
            $pusher = new \Pusher\Pusher(
                $config['key'],
                $config['secret'],
                $config['app_id'],
                $config['options'],
            );

            return $pusher->trigger($channel, $event, $actual_data);
        }
        if ('ably' == $driver) {
            $ably = new \Ably\AblyRest($config['key']);

            return $ably->channel($channel)->publish($event, $actual_data);
        }
    }

    public function isMenuActive($patterns = [])
    {
        return preg_match('/'.(implode('|', $patterns)).'/', request()->route()->getName()) ? 'active' : '';
    }

    public function viewRenderer($__php, $__data = [])
    {
        $__php = \Blade::compileString($__php);
        $__data['__env'] = app(\Illuminate\View\Factory::class);
        $obLevel = ob_get_level();
        ob_start();
        extract($__data, EXTR_SKIP);

        try {
            eval('?'.'>'.$__php);
        } catch (Exception $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            throw $e;
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            throw new \Symfony\Component\Debug\Exception\FatalThrowableError($e);
        }

        return ob_get_clean();
    }

    public function timezones()
    {
        return array_combine(timezone_identifiers_list(), timezone_identifiers_list());
    }

    public function cronjob_frequencies()
    {
        return [
            // 'everySeconds' => 'Every Seconds',
            'everyMinute' => 'Every Minute',
            'everyTwoMinutes' => 'Every Two Minutes',
            'everyThreeMinutes' => 'Every Three Minutes',
            'everyFourMinutes' => 'Every Four Minutes',
            'everyFiveMinutes' => 'Every Five Minutes',
            'everyTenMinutes' => 'Every Ten Minutes',
            'everyFifteenMinutes' => 'Every Fifteen Minutes',
            'everyThirtyMinutes' => 'Every Thirty Minutes',
            'everyTwoHours' => 'Every Two Hours',
            'everyThreeHours' => 'Every Three Hours',
            'everyFourHours' => 'Every Four Hours',
            'everySixHours' => 'Every Six Hours',
            'hourly' => 'Hourly',
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
        ];
    }

    public function route_slug($name, string $slug = '', array $parameters = [], $locale = '', $absolute = true)
    {
        if ('' == $locale) {
            $locale = '' != app()->getLocale() ? app()->getLocale() : config('app.locale');
        }
        $string = '' != $slug ? '.'.str_replace('/', '.', $slug) : '';

        return route($name.$string, array_merge([$locale], $parameters), $absolute);
    }

    public function getBrandNameByHost($domain = '')
    {
        if (!is_dir(base_path('brand'))) {
            return null;
        }
        $configs = Cache::tags('brand')->remember('brand-configs', (60 * 60 * 24), function () {
            $configs = [];
            $dirs = File::directories(base_path('brand'));
            foreach ($dirs as $dir) {
                $brand = basename($dir);
                $config = require $dir.'/config/domains.php';
                $configs[$config['main']] = $brand;
                foreach ($config['aliases'] as $alias) {
                    $configs[$alias] = $brand;
                }
            }

            return $configs;
        });

        return '' == $domain ? $configs : (isset($configs[$domain]) ? $configs[$domain] : null);
    }

    public function getDomain($brandName = '')
    {
        $domains = $this->getBrandNameByHost();
        $return = isset($domains[request()->getHost()]) ? request()->getHost() : null;
        if ('' != $brandName && null == $return) {
            $domains = array_flip($domains);
            $return = isset($domains[$brandName]) ? (app()->runningInConsole() ? $brandName : $domains[$brandName]) : null;
        }

        return $return;
    }

    public function brand($brandName = '')
    {
        $brandName = '' != $brandName ? $brandName : $this->getBrandNameByHost(request()->getHost());

        return Cache::tags('brand')->remember('brand-'.$brandName, (60 * 60 * 24), function () use ($brandName) {
            return app(config('instant.Models.Brand'))->query()->whereStatus('A')->whereName($brandName)->where('published_at', '<', date('Y-m-d 23:59:59'))->where('expired_at', '>', date('Y-m-d 23:59:59'))->first();
        });
    }

    public function queue_keys($driver = 'redis')
    {
        $keys = [];
        if ('redis' == $driver) {
            $keys = Queue::getRedis()->keys('*');
            $queues = [];
            foreach ($keys as $i => $key) {
                $keys[$i] = str_replace([config('database.redis.options.prefix').'queues:'], '', $key);
            }
        }

        return $keys;
    }

    public function getBrand($brandName)
    {
        $brand = cache()->tags('brand')->remember('register-'.$brandName, (60 * 60 * 24), function () use ($brandName) {
            return app(config('instant.Models.Brand'))->query()
                ->where('name', $brandName)->first();
        });
        \Config::set('main.brand', $brand);

        return $brand;
    }

    public function renderSlug($slug, $locale = '')
    {
        $model = app(config('instant.Models.Page'))->query()
            ->where('brand_id', config('main.brand')->id)
            ->where('locale', app()->getLocale())
            ->where('slug', strtolower($slug))
            ->first()
        ;

        return $this->viewRenderer($model->blade);
    }

    public function subPathRoutes($brandName, $controller)
    {
        $models = cache()->tags('page')->remember('page-'.$brandName, (60 * 60 * 24), function () use ($brandName) {
            $brand = getBrand($brandName);
            if ($brand) {
                return app(config('instant.Models.Page'))->query()
                    ->where('brand_id', $brand->id)
                    ->where('slug', 'not like', 'https://%')
                    ->where('slug', 'not like', 'http://%')
                    ->get()
                ;
            }
        });
        if ($models) {
            foreach ($models as $model) {
                $routeName = str_replace('/', '.', $model->slug);
                Route::get('/'.$model->slug, $controller)->name($routeName);
            }
        }
    }

    public function sendAlert(array $data = [])
    {
        // brand_id,link,message,sender_id,receiver_id
        if (count($data) && $data['receiver_id'] != auth()->id() && 0 != $data['receiver_id']) {
            if (is_array($data['receiver_id'])) {
                dispatch(function () use ($data) {
                    $receiver_ids = $data['receiver_id'];
                    foreach ($receiver_ids as $receiver_id) {
                        $data['receiver_id'] = $receiver_id;
                        if ($data['receiver_id'] != auth()->id()) {
                            app(config('instant.Models.Alert'))->create($data);
                        }
                    }
                });
            } else {
                app(config('instant.Models.Alert'))->create($data);
            }
        }

        return true;
    }

    public function permissionUserIds($permission, $brand_id = 0)
    {
        $permission = app(config('instant.Models.Permission'))->where('name', str_slug($permission))->first();

        return cache()->tags(['permissions'])->rememberForever('permission_users:'.$permission->id.':'.$brand_id, function () use ($permission, $brand_id) {
            $ids = [];
            $ids = app(config('instant.Models.Role'))->where('name', 'Admin')->first()->users()->whereNull('brand_id')->pluck('users.id')->toArray();
            $roles = $permission->roles;
            foreach ($roles as $role) {
                $ids = array_merge($ids, $role->users()->where('brand_id', $brand_id)->pluck('users.id')->toArray());
            }

            return $ids = array_unique($ids);
        });
    }
}
