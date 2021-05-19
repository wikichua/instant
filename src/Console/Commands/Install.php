<?php

namespace Wikichua\Instant\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Install extends Command
{
    protected $signature = 'instant:install {--no-compiled}';

    protected $description = 'Instant Installation';

    public function __construct()
    {
        parent::__construct();
        $this->vendorPath = base_path('vendor/wikichua/instant');
        if (is_dir(base_path('packages/Wikichua/Instant'))) {
            $this->vendorPath = base_path('packages/Wikichua/Instant');
        } elseif (is_dir(base_path('packages/wikichua/instant'))) {
            $this->vendorPath = base_path('packages/wikichua/instant');
        }
    }

    public function handle()
    {
        $vendorPath = $this->vendorPath;

        $files = [
            $vendorPath.'/package.json' => base_path('package.json'),
            $vendorPath.'/tailwind.config.js' => base_path('tailwind.config.js'),
            $vendorPath.'/webpack.config.js' => base_path('webpack.config.js'),
            $vendorPath.'/webpack.mix.js' => base_path('webpack.mix.js'),
            $vendorPath.'/resources/css' => resource_path('css'),
            $vendorPath.'/resources/js' => resource_path('js'),
            $vendorPath.'/resources/views/app.blade.php' => resource_path('views/app.blade.php'),
            $vendorPath.'/resources/vues' => resource_path('vues'),
        ];
        $this->copiesFileOrDirectory($files);
        $this->checkCacheDriver();
        $this->replaceRouteServiceProviderHomeConst();
        $this->replaceUserModelExtends();
        $this->injectRunCronjobsCallIntoConsoleKernel();
        $this->injectUseArtisanTraitIntoConsoleKernel();
        $this->injectDisableCommandsCallConsoleKernel();
        if ($this->option('no-compiled') != true) {
            $this->dumpComposer();
            if ($this->confirm('npm install?', true)) {
                $output = shell_exec('npm install');
                $this->info($output);
            }
            if ($this->confirm('npm run prod?', true)) {
                $output = shell_exec('npm run prod');
                $this->info($output);
            }
        }
        $this->removeDefaultWebRoute();
        cache()->flush();
        return ;
    }

    protected function dumpComposer()
    {
        $output = shell_exec('composer dump');
        $this->info($output);
    }

    protected function checkCacheDriver()
    {
        if (in_array(config('cache.default'), ['file'])) {
            $file = config_path('cache.php');
            $content = @File::get($file);
            if (!str_contains($content, '\'default\' => env(\'CACHE_DRIVER\', \'array\'),')) {
                $lines = explode(PHP_EOL, $content);
                foreach ($lines as $key => $line) {
                    if (str_contains($line, '\'default\' => env(\'CACHE_DRIVER\', \'file\'),')) {
                        $from = $line;
                        $to = $lines[$key] = str_repeat("\t", 1).'\'default\' => env(\'CACHE_DRIVER\', \'array\'),';
                    }
                }
                if (isset($from)) {
                    @File::replace($file, implode(PHP_EOL, $lines));
                    $this->info('Replace '.trim($from).' to '. trim($to) . ' in ' . $file);
                    $this->newLine();
                }
            }
        }
        $file = base_path('.env');
        $content = @File::get($file);
        if (str_contains($content, 'CACHE_DRIVER=file')) {
            $lines = explode(PHP_EOL, $content);
            foreach ($lines as $key => $line) {
                if (str_contains($line, 'CACHE_DRIVER=file')) {
                    $from = $line;
                    $to = $lines[$key] = 'CACHE_DRIVER=array';
                }
            }
            if (isset($from)) {
                @File::replace($file, implode(PHP_EOL, $lines));
                $this->info('Replace '.trim($from).' to '. trim($to) . ' in ' . $file);
                $this->newLine();
            }
        }
    }

    protected function copiesFileOrDirectory(array $data)
    {
        foreach ($data as $from => $to) {
            is_dir($from)? @File::copyDirectory($from, $to):@File::copy($from, $to);
            $this->info('Copy '.$from.' to '. $to);
            $this->newLine();
        }
    }

    protected function replaceRouteServiceProviderHomeConst()
    {
        $file = app_path('Providers/RouteServiceProvider.php');
        $content = @File::get($file);
        if (!str_contains($content, "public const HOME = '/dashboard';")) {
            $lines = explode(PHP_EOL, $content);
            foreach ($lines as $key => $line) {
                if (str_contains($line, 'public const HOME')) {
                    $from = $line;
                    $to = $lines[$key] = "\tpublic const HOME = '/dashboard';";
                }
            }
            if (isset($from) && '' != $from) {
                @File::replace($file, implode(PHP_EOL, $lines));
                $this->info('Replace '.trim($from).' to '. trim($to) . ' in ' . $file);
                $this->newLine();
            }
        }
    }

    protected function replaceUserModelExtends()
    {
        $file = app_path('Models/User.php');
        $content = @File::get($file);
        if (!str_contains($content, 'class User extends \Wikichua\Instant\Models\User')) {
            $lines = explode(PHP_EOL, $content);
            foreach ($lines as $key => $line) {
                if (str_contains($line, 'class User extends Authenticatable')) {
                    $from = $line;
                    $to = $lines[$key] = "class User extends \Wikichua\Instant\Models\User";
                }
            }
            if (isset($from) && '' != $from) {
                @File::replace($file, implode(PHP_EOL, $lines));
                $this->info('Replace '.trim($from).' to '. trim($to) . ' in ' . $file);
                $this->newLine();
            }
        }
    }

    protected function injectRunCronjobsCallIntoConsoleKernel()
    {
        $file = app_path('Console/Kernel.php');
        $content = @File::get($file);
        if (!str_contains($content, '$this->runCronjobs($schedule);')) {
            $lines = explode(PHP_EOL, $content);
            foreach ($lines as $key => $line) {
                if (str_contains($line, '$schedule->command(\'inspire\')->hourly();')) {
                    $from = $line;
                    $to = $lines[$key] = $line.PHP_EOL.str_repeat("\t", 2).'$this->runCronjobs($schedule);';
                }
            }
            if (isset($from)) {
                @File::replace($file, implode(PHP_EOL, $lines));
                $this->info('Replace '.trim($from).' to '. trim($to) . ' in ' . $file);
                $this->newLine();
            }
        }
    }

    protected function injectUseArtisanTraitIntoConsoleKernel()
    {
        $file = app_path('Console/Kernel.php');
        $content = @File::get($file);
        if (!str_contains($content, 'use \Wikichua\Instant\Http\Traits\ArtisanTrait;')) {
            $lines = explode(PHP_EOL, $content);
            foreach ($lines as $key => $line) {
                if (str_contains($line, 'protected $commands')) {
                    $from = $line;
                    $to = $lines[$key] = str_repeat("\t", 1).'use \Wikichua\Instant\Http\Traits\ArtisanTrait;'.PHP_EOL.str_repeat("\t", 1).'protected $commands_disabled = [
        \'production\' => [\'migrate:fresh\',\'migrate:refresh\',\'migrate:reset\',\'Instant:install\'],
    ];'.PHP_EOL.$line;
                }
            }
            if (isset($from)) {
                @File::replace($file, implode(PHP_EOL, $lines));
                $this->info('Replace '.trim($from).' to '. trim($to) . ' in ' . $file);
                $this->newLine();
            }
        }
    }

    protected function injectDisableCommandsCallConsoleKernel()
    {
        $file = app_path('Console/Kernel.php');
        $content = @File::get($file);
        if (!str_contains($content, '$this->disableCommands();')) {
            $lines = explode(PHP_EOL, $content);
            foreach ($lines as $key => $line) {
                if (str_contains($line, '$this->load(__DIR__.\'/Commands\');')) {
                    $from = $line;
                    $to = $lines[$key] = str_repeat("\t", 2).'$this->disableCommands();'.PHP_EOL.$line;
                }
            }
            if (isset($from)) {
                @File::replace($file, implode(PHP_EOL, $lines));
                $this->info('Replace '.trim($from).' to '. trim($to) . ' in ' . $file);
                $this->newLine();
            }
        }
    }
    protected function removeDefaultWebRoute()
    {
        $file = base_path('routes/web.php');
        $content = @File::get($file);
        $lines = explode(PHP_EOL, $content);
        foreach ($lines as $key => $line) {
            if (Str::contains($line, 'Route::get(\'/\', function () {')) {
                $string = $lines[$key].PHP_EOL.$lines[$key + 1].PHP_EOL.$lines[$key + 2];
                unset($lines[$key], $lines[$key + 1], $lines[$key + 2]);
            }
        }
        if (isset($string)) {
            @File::replace($file, implode(PHP_EOL, $lines));
            $this->info('Removed '.$string);
            $this->newLine();
        }
    }
}
