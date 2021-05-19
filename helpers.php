<?php
function ttt()
{
    dd('testing');
}

if (!function_exists('cronjob_frequencies')) {
    function cronjob_frequencies()
    {
        return Help::cronjob_frequencies();
    }
}
if (!function_exists('timezones')) {
    function timezones()
    {
        return Help::timezones();
    }
}
if (!function_exists('queue_keys')) {
    function queue_keys($driver = 'redis')
    {
        return Help::queue_keys($driver);
    }
}
if (!function_exists('qs_url')) {
    function qs_url($path = null, $qs = [], $secure = null)
    {
        return Help::qs_url($path, $qs, $secure);
    }
}
if (!function_exists('prettyPrintJson')) {
    function prettyPrintJson($value = '')
    {
        return Help::prettyPrintJson($value);
    }
}
if (!function_exists('settings')) {
    function settings($name, $default = '')
    {
        return Help::settings($name, $default);
    }
}
if (!function_exists('rebuildUrl')) {
    function rebuildUrl($url, $params = [])
    {
        return Help::rebuildUrl($url, $params);
    }
}
if (!function_exists('findHashTag')) {
    function findHashTag($string)
    {
        return Help::findHashTag($string);
    }
}
if (!function_exists('getModels')) {
    function getModels($path, $namespace)
    {
        return Help::getModels($path, $namespace);
    }
}
if (!function_exists('getModelsList')) {
    function getModelsList()
    {
        return Help::getModelsList();
    }
}
if (!function_exists('audit')) {
    function audit($message, $data = [], $model = null, $ip = '')
    {
        return Help::audit($message, $data, $model, $ip);
    }
}
if (!function_exists('agent')) {
    function agent()
    {
        return Help::agent();
    }
}
if (!function_exists('agents')) {
    function agents($key = '')
    {
        return Help::agents($key);
    }
}
if (!function_exists('opendns')) {
    function opendns()
    {
        return Help::opendns();
    }
}
if (!function_exists('iplocation')) {
    function iplocation($ip = '')
    {
        return Help::iplocation($ip);
    }
}

if (!function_exists('scan_langs_dir')) {
    function scan_langs_dir()
    {
        return Help::scan_langs_dir();
    }
}

if (!function_exists('pushered')) {
    function pushered($data = [], $channel = '', $event = 'general')
    {
        return Help::pushered($data, $channel, $event);
    }
}
if (!function_exists('isMenuActive')) {
    function isMenuActive($patterns = [])
    {
        return Help::isMenuActive($patterns);
    }
}
if (!function_exists('viewRenderer')) {
    function viewRenderer($__php, $__data = [])
    {
        return Help::viewRenderer($__php, $__data);
    }
}
if (!function_exists('route_slug')) {
    function route_slug($name, string $slug = '', array $parameters = [], $locale = '', $absolute = true)
    {
        return Help::route_slug($name, $slug, $parameters, $locale, $absolute);
    }
}
if (!function_exists('getBrandNameByHost')) {
    function getBrandNameByHost($domain = '')
    {
        return Help::getBrandNameByHost($domain);
    }
}
if (!function_exists('getDomain')) {
    function getDomain($brandName = '')
    {
        return Help::getDomain($brandName);
    }
}
if (!function_exists('brand')) {
    function brand($brandName = '')
    {
        return Help::brand($brandName);
    }
}
if (!function_exists('renderSlug')) {
    function renderSlug($slug, $locale = '')
    {
        return Help::renderSlug($slug, $locale);
    }
}
if (!function_exists('getBrand')) {
    function getBrand($brandName)
    {
        return Help::getBrand($brandName);
    }
}
if (!function_exists('sendAlert')) {
    function sendAlert(array $data = [])
    {
        return Help::sendAlert($data);
    }
}
if (!function_exists('permissionUserIds')) {
    function permissionUserIds($permission, $brand_id = 0)
    {
        return Help::permissionUserIds($permission, $brand_id);
    }
}
