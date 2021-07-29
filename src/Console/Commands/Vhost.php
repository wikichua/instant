<?php

 namespace Wikichua\Instant\Console\Commands;

use Illuminate\Console\Command;

class Vhost extends Command
{
    protected $signature = 'instant:vhost {action} {domain?} {path?}';
    protected $description = 'Manage Virtual Host (Linux)';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $action = strtolower(trim($this->argument('action')));
        if (in_array($action, ['create', 'delete'])) {
            $domain = trim($this->argument('domain'));
            $path = trim($this->argument('path')) == '' ? public_path() : trim($this->argument('path'));
            $vhostsh = base_path('vendor/wikichua/instant/stubs/virtualhost-nginx');
            $result = shell_exec('sudo '.$vhostsh.' '.$action.' '.$domain.' '.$path);
            $this->line($result);
        } else {
            $hosts = explode(PHP_EOL, shell_exec('cat /etc/hosts'));
            $results = [];
            foreach ($hosts as $host) {
                if (str_contains($host, '127.0.0.1')) {
                    $results[] = explode("\t", $host);
                }
            }
            $this->table(['IP', 'DNS'], $results);
        }
        cache()->flush();
    }
}
