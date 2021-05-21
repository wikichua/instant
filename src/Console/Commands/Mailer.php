<?php

 namespace Wikichua\Instant\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Mailer extends Command
{
    protected $signature = 'instant:make:mailer {name} {--brand=} {--force}';
    protected $description = 'Create Mail Class';
    protected $name;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->brand = $this->option('brand') ? $this->option('brand') : null;

        $this->name = \Str::studly($this->argument('name'));

        if ($this->brand) {
            $this->brandName = \Str::studly($this->option('brand'));
            $this->name .= $this->brandName;
            $this->brand($this->brandName);
        } else {
            $this->app();
        }
        cache()->flush();
    }

    protected function app()
    {
        File::ensureDirectoryExists(app_path('Mail'));
        $mail_file = app_path('Mail/'.$this->name.'.php');
        if (false == $this->option('force')) {
            if (File::exists($mail_file)) {
                $this->info('Mail has already exists');

                return;
            }
        }
        File::put($mail_file, $this->mailString());
        $this->info('Mail File added to '.$mail_file);

        $msg = 'Migration file created';
        $filename = "instant{$this->name}MailSeed.php";
        $database_dir = database_path('migrations');
        $migration_file = database_path('migrations/'.date('Y_m_d_000000_').$filename);
        foreach (File::files($database_dir) as $file) {
            if (str_contains($file->getPathname(), $filename)) {
                $migration_file = $file->getPathname();
                $msg = 'Migration file overwritten';
            }
        }
        File::put($migration_file, $this->seedString());
        $this->line($msg.': <info>'.$migration_file.'</info>');
    }

    protected function brand($brand_string)
    {
        $brand_service_path = base_path('brand/'.$brand_string.'/Mail');
        if (true != File::exists($brand_service_path)) {
            File::makeDirectory($brand_service_path);
        }
        $service_file = $brand_service_path.'/'.$this->name.'.php';
        if (false == $this->option('force')) {
            if (File::exists($service_file)) {
                $this->info('Mail has already exists');

                return;
            }
        }
        File::put($service_file, $this->mailString(1));
        $this->info('Mail File added to '.$service_file);

        $msg = 'Migration file created';
        $filename = "instant{$this->name}MailSeed.php";
        $database_dir = base_path('brand/'.$this->brand.'/database/migrations');
        $migration_file = base_path('brand/'.$this->brand.'/database/migrations/'.date('Y_m_d_000000_').$filename);
        foreach (File::files($database_dir) as $file) {
            if (str_contains($file->getPathname(), $filename)) {
                $migration_file = $file->getPathname();
                $msg = 'Migration file overwritten';
            }
        }
        File::put($migration_file, $this->seedString(1));
        $this->line($msg.': <info>'.$migration_file.'</info>');
    }

    protected function mailString($isBrand = 0)
    {
        $name = $this->name;
        $namespace = 'App\Mail';
        if ($isBrand) {
            $namespace = "Brand\\{$this->brandName}\\Mail";
        }

        return <<<EOT
            <?php

            namespace {$namespace};

            use Illuminate\\Bus\\Queueable;
            use Illuminate\\Contracts\\Queue\\ShouldQueue;
            use Illuminate\\Queue\\SerializesModels;
            use Spatie\\MailTemplates\\TemplateMailable;
            use Wikichua\\Instant\\Http\\Traits\\MailableTrait;

            class {$name} extends TemplateMailable
            {
                use Queueable, SerializesModels, MailableTrait;

                public \$name, \$email;

                /*
                Usage :
                \$user = app(config('instant.Models.User'))->find(2);
                Mail::to(\$user->email)->send(app('{$namespace}\\{$name}')->init(\$user));
                */
                public function init(\$user = null)
                {
                    // All your codes should be here
                    if (\$user) {
                        \$this->name = \$user->name;
                        \$this->email = \$user->email;
                    }
                    // Keep this please
                    return \$this;
                }

                public function getHtmlLayout(): ?string
                {
                    // Blade view: `return view('mailLayouts.main', \$data)->render();`
                    return '{{{ body }}}';
                }
            }

            EOT;
    }

    protected function seedString($isBrand = 0)
    {
        $name = $this->name;
        $namespace = 'App\Mail';
        if ($isBrand) {
            $namespace = "Brand\\{$this->brandName}\\Mail";
        }

        return <<<EOT
            <?php

            use Illuminate\\Database\\Migrations\\Migration;
            use Illuminate\\Database\\Schema\\Blueprint;
            use Illuminate\\Support\\Facades\\Schema;

            class instant{$name}MailSeed extends Migration
            {
                public function up()
                {
                    app(config('instant.Models.Mailer'))->create([
                        'mailable' => {$namespace}\\{$name}::class,
                        'subject' => 'Welcome, {{ name }}',
                        'html_template' => '<h1>Hello, {{ name }}!</h1>',
                        'text_template' => 'Hello, {{ name }}!',
                    ]);
                }
                public function down()
                {
                    app(config('instant.Models.Mailer'))->where('mailable', {$namespace}\\{$name}::class)->forceDelete();
                }
            }


            EOT;
    }
}
