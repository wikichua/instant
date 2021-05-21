<?php

 namespace Wikichua\Instant\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class Module extends Command
{
    protected $signature = 'instant:make:module {model} {--brand=} {--force}';
    protected $description = 'Make Up The CRUD From Config';

    public function __construct(Filesystem $file)
    {
        parent::__construct();
        $this->file = $file;
        $this->stub_path = config('instant.stubs.path');
    }

    public function handle()
    {
        $this->brand = $this->option('brand') ? \Str::studly($this->option('brand')) : null;
        if ($this->brand) {
            $brand = app(config('instant.Models.Brand'))->query()->where('name', strtolower($this->brand))->first();
            if (!$brand) {
                $this->error('Brand not found: <info>'.$this->brand.'</info>');

                return '';
            }
            $this->model = $this->brand.(str_replace($this->brand, '', $this->argument('model')));
            $config_file = base_path('brand/'.$this->brand.'/config/instant/'.$this->model.'Config.php');
        } else {
            $this->model = $this->argument('model');
            $config_file = config_path('instant/'.$this->model.'Config.php');
        }

        if (!$this->file->exists($config_file)) {
            $this->error('Config file not found: <info>'.$config_file.'</info>');

            return;
        }
        $this->config = include $config_file;
        if (!$this->config['ready']) {
            if (false == $this->option('force')) {
                $this->error('Config file not ready: <info>'.$config_file.'</info>');

                return;
            }
        }
        $this->line('Config <info>'.$this->model.'</info> Found! Initiating!');
        $this->initReplacer();
        $this->reinstate();

        $config_content = $this->file->get($config_file);
        $config_content = str_replace("'ready' => true,", "'ready' => false,", $config_content);
        $this->file->put($config_file, $config_content);
        $this->line('<info>Since you had done make the CRUD, we will help you set ready to false to prevent accidentally make after you have done all your changes in your flow!</info>');
        $this->line('Config has changed: <info>'.$config_file.'</info>');

        $this->alert("Now remember npm run production\nafter you have done adjusting your crud component\nor business in your controler & model.");
        cache()->flush();
    }

    protected function initReplacer()
    {
        // $this->replaces['{%route_as%}'] = $this->brand? strtolower($this->brand).'.':'';
        $this->replaces['{%route_as%}'] = '';
        $this->replaces['{%controller_namespace%}'] = $this->brand ? 'Brand\\'.$this->brand.'\\Controllers\\Admin' : 'App\Http\Controllers\Admin';
        $this->replaces['{%api_controller_namespace%}'] = $this->brand ? 'Brand\\'.$this->brand.'\\Controllers\\Api' : 'App\Http\Controllers\Api';
        $this->replaces['{%model_namespace%}'] = $this->brand ? 'Brand\\'.$this->brand.'\\Models' : ucfirst(str_replace('/', '\\', 'App\Models'));
        $this->replaces['{%page_path%}'] = $this->brand ? $this->brand.'::admin' : 'admin';
        $this->replaces['{%brand_view_namespace%}'] = $this->brand ? '$config = require(base_path(\'brand/'.$this->brand.'/config/main.php\'));
        \View::addNamespace(\''.$this->brand.'\', $config[\'resources_path\']);' : '';

        $this->replaces['{%model%}'] = $this->model;
        $this->replaces['{%model_class%}'] = $this->replaces['{%model%}'];
        $this->replaces['{%model_string%}'] = trim(preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $this->replaces['{%model%}']));
        $this->replaces['{%model_strings%}'] = str_plural($this->replaces['{%model_string%}']);
        $this->replaces['{%model_variable%}'] = strtolower(str_replace(' ', '_', $this->replaces['{%model_string%}']));
        $this->replaces['{%model_variables%}'] = strtolower(str_replace(' ', '_', $this->replaces['{%model_strings%}']));
        $this->replaces['{%model_%}'] = strtolower(str_replace(' ', '_', $this->replaces['{%model_strings%}']));
        $this->replaces['{%table_name%}'] = $this->replaces['{%model_variables%}'];
        $this->replaces['{%table_declared%}'] = '';
        $this->replaces['{%menu_name%}'] = $this->replaces['{%model_strings%}'];
        $this->replaces['{%menu_icon%}'] = $this->config['menu_icon'];

        $this->replaces['{%permission_string%}'] = strtolower($this->replaces['{%model_string%}']);

        if ($this->brand) {
            $this->replaces['{%table_declared%}'] = "protected \$table = '{$this->replaces['{%table_name%}']}';";
        }

        if ((isset($this->config['table_name']) && '' != $this->config['table_name'])) {
            $this->replaces['{%table_name%}'] = $this->config['table_name'];
            $this->replaces['{%table_declared%}'] = "protected \$table = '{$this->config['table_name']}';";
        }

        if (isset($this->config['menu_name']) && '' != $this->config['menu_name']) {
            $this->replaces['{%menu_name%}'] = $this->config['menu_name'];
        }
    }

    protected function reinstate()
    {
        $this->orderable();

        $config_form = $this->config['form'];
        $upload_strings = $model_keys = $setting_keys = $table_fields = $search_scopes = $search_fields = $settings_options_up = $settings_options_down = $read_fields = $form_fields = $validations = $user_timezones = $fillables = $casts = $appends = $mutators = $relationships = $searchable_fields = $relationships_query = [];

        foreach ($config_form as $field => $options) {
            $this->replaces['{%field_variable%}'] = studly_case($field);
            if (isset($options['migration']) && '' != $options['migration']) {
                $migration = $options['migration'];
                $this->replaces['{%field%}'] = $field;
                $migration_codes[] = $this->replaceholder('$table->'.$this->putInChains($migration).';');
            }
            $fillables[] = "'{$field}'";
            if ('' != $options['casts']) {
                $casts[] = "'{$field}' => '{$options['casts']}'";
            }
            if ($options['list']) {
                $search_boolean_string = $options['search'] ? 'true' : 'false';
                $sort_boolean_string = isset($options['sortable']) && $options['sortable'] ? 'true' : 'false';
                $table_fields[] = "['title' => '{$options['label']}', 'data' => '{$field}', 'sortable' => {$sort_boolean_string}, 'filterable' => {$search_boolean_string}]";
            }

            if (!empty($options['relationship'])) {
                $relationships[] = $this->indent().'public function '.array_keys($options['relationship'])[0].'()';
                $relationships[] = $this->indent().'{';
                $relationships[] = $this->indent().'    return $this->'.$this->putInChains(array_values($options['relationship'])[0]).';';
                $relationships[] = $this->indent().'}'.PHP_EOL;
                $relationships_query[] = array_keys($options['relationship'])[0];
            }

            if (!empty($options['user_timezone'])) {
                $user_timezones[] = $this->indent().'public function get'.studly_case($field).'Attribute($value)';
                $user_timezones[] = $this->indent().'{';
                $user_timezones[] = $this->indent().'    return $this->inUserTimezone($value);';
                $user_timezones[] = $this->indent().'}'.PHP_EOL;
            }

            if (!empty($options['validation'])) {
                foreach ($options['validation'] as $method => $rules) {
                    if (isset($options['input']['type']) && 'file' == $options['input']['type']) {
                        $validations[$method][] = $this->indent(3).'"'.$field.'_file" => "'.$rules.'",';
                    } else {
                        $validations[$method][] = $this->indent(3).'"'.$field.'" => "'.$rules.'",';
                    }
                }
            }

            $replace_for_form['{%option_key%}'] = '';
            $select_options = '';
            if (isset($options['model_options']) && '' != $options['model_options']) {
                $replace_for_form['{%model_option_query%}'] = $select_options = $options['model_options'];
                $model_keys[] = "{$field}";
                $replace_for_form['{%option_key%}'] = "models['{$field}']";
            } else {
                if (isset($options['options']) && is_array($options['options']) && count($options['options'])) {
                    $opts = [];
                    foreach ($options['options'] as $key => $value) {
                        $opts[] = "'{$key}' => '{$value}'";
                    }
                    $setting_keys[] = $setting_key = "{$this->replaces['{%model_variable%}']}_{$field}";
                    $settings_options_up[] = "app(config('instant.Models.Setting'))->create(['created_by' => \$user_id, 'updated_by' => \$user_id, 'key' => '{$setting_key}','value' => [".implode(',', $opts).']]);';
                    $settings_options_down[] = "app(config('instant.Models.Setting'))->where('key','{$setting_key}')->forceDelete();";
                    $replace_for_form['{%option_key%}'] = "settings['{$setting_key}']";
                    $select_options = "settings('{$setting_key}')";
                }
            }

            $replace_for_form['{%label%}'] = $options['label'];
            $replace_for_form['{%field%}'] = $field;
            $replace_for_form['{%model_variable%}'] = $model_variable = $this->replaces['{%model_variable%}'];
            $replace_for_form['{%attributes_tag%}'] = '';
            $isMultiple = false;
            if (count($options['attributes'])) {
                $temp_attrs = [];
                foreach ($options['attributes'] as $attr_key => $attr_val) {
                    $temp_attrs[] = "'{$attr_key}'=\"{$attr_val}\"";
                    if ('multiple' == $attr_key) {
                        $isMultiple = true;
                    }
                }
                $replace_for_form['{%attributes_tag%}'] = implode(' ', $temp_attrs);
            }
            $replace_for_form['{%class_tag%}'] = $options['class'];

            $form_stub = '';

            switch ($options['type']) {
                case 'email':
                case 'number':
                case 'password':
                case 'text':
                case 'url':
                    $form_stub = '<x-instant::input-field type="'.$options['type'].'" name="{%field%}" id="{%field%}" label="{%label%}" class="{%class_tag%}" {%attributes_tag%} :value="$model->{%field%} ?? \'\'"/>';
                    $read_stub = '<x-instant::display-field name="{%field%}" id="{%field%}" label="{%label%}" :value="$model->{%field%}" type="text"/>';

                    break;

                case 'time':
                    $form_stub = '<x-instant::time-field name="{%field%}" id="{%field%}" label="{%label%}" class="{%class_tag%}" {%attributes_tag%} :value="$model->{%field%} ?? \'\'"/>';
                    $read_stub = '<x-instant::display-field name="{%field%}" id="{%field%}" label="{%label%}" :value="$model->{%field%}" type="text"/>';

                    break;

                case 'date':
                    $form_stub = '<x-instant::date-field name="{%field%}" id="{%field%}" label="{%label%}" class="{%class_tag%}" {%attributes_tag%} :value="$model->{%field%} ?? \'\'"/>';
                    $read_stub = '<x-instant::display-field name="{%field%}" id="{%field%}" label="{%label%}" :value="$model->{%field%}" type="date"/>';

                    break;

                case 'datetime':
                    $form_stub = '<x-instant::datetime-field name="{%field%}" id="{%field%}" label="{%label%}" class="{%class_tag%}" {%attributes_tag%} :value="$model->{%field%} ?? \'\'"/>';
                    $read_stub = '<x-instant::display-field name="{%field%}" id="{%field%}" label="{%label%}" :value="$model->{%field%}" type="datetime"/>';

                    break;

                case 'image':
                    $form_stub = '<x-instant::image-field type="'.$options['type'].'" name="{%field%}" id="{%field%}" label="{%label%}" class="{%class_tag%}" {%attributes_tag%} :value="$model->{%field%} ?? \'\'"/>';
                    $read_stub = '<x-instant::display-field name="{%field%}" id="{%field%}" label="{%label%}" :value="$model->{%field%}" type="image"/>';

                    break;

                case 'file':
                    $form_stub = '<x-instant::file-field type="'.$options['type'].'" name="{%field%}" id="{%field%}" label="{%label%}" class="{%class_tag%}" {%attributes_tag%} :value="$model->{%field%} ?? \'\'"/>';
                    $read_stub = '<x-instant::display-field name="{%field%}" id="{%field%}" label="{%label%}" :value="$model->{%field%}" type="file"/>';

                    break;

                case 'textarea':
                    $form_stub = '<x-instant::textarea-field name="{%field%}" id="{%field%}" label="{%label%}" class="{%class_tag%}" {%attributes_tag%} :value="$model->{%field%} ?? \'\'"/>';
                    $read_stub = '<x-instant::display-field name="{%field%}" id="{%field%}" label="{%label%}" :value="$model->{%field%}" type="text"/>';

                    break;

                case 'select':
                    $form_stub = '<x-instant::select-field name="{%field%}" id="{%field%}" label="{%label%}" class="{%class_tag%}" {%attributes_tag%} :data="[\'style\'=>\'border bg-white\',\'live-search\'=>false]" :options="'.$select_options.'" :selected="$model->{%field%} ?? []"/>';
                    $type = $isMultiple ? 'list' : 'text';
                    $read_stub = '<x-instant::display-field name="{%field%}" id="{%field%}" label="{%label%}" :value="$model->{%field%}" type="'.$type.'"/>';

                    break;

                case 'datalist':
                    $form_stub = '<x-instant::datalist-field name="{%field%}" id="{%field%}" label="{%label%}" class="{%class_tag%}" {%attributes_tag%} :data="[\'style\'=>\'border bg-white\',\'live-search\'=>false]" :options="'.$select_options.'" :selected="$model->{%field%} ?? []"/>';
                    $read_stub = '<x-instant::display-field name="{%field%}" id="{%field%}" label="{%label%}" :value="$model->{%field%}" type="text"/>';

                    break;

                case 'radio':
                    $form_stub = '<x-instant::radios-field name="{%field%}" id="{%field%}" label="{%label%}" :options="'.$select_options.'" :checked="$model->{%field%} ?? []" :isGroup="false" :stacked="'.($options['stacked'] ? 1 : 0).'"/>';
                    $read_stub = '<x-instant::display-field name="{%field%}" id="{%field%}" label="{%label%}" :value="$model->{%field%}" type="text"/>';

                    break;

                case 'checkbox':
                    $form_stub = '<x-instant::checkboxes-field name="{%field%}" id="{%field%}" label="{%label%}" :options="'.$select_options.'" :checked="$model->{%field%} ?? []" :isGroup="false" class="{%class_tag%}" :stacked="'.($options['stacked'] ? 1 : 0).'"/>';
                    $read_stub = '<x-instant::display-field name="{%field%}" id="{%field%}" label="{%label%}" :value="$model->{%field%}" type="list"/>';

                    break;

                case 'editor':
                    $form_stub = '<x-instant::editor-field name="{%field%}" id="{%field%}" label="{%label%}" class="{%class_tag%}" {%attributes_tag%} :value="$model->{%field%} ?? \'\'"/>';
                    $read_stub = '<x-instant::display-field name="{%field%}" id="{%field%}" label="{%label%}" value="{!! $model->{%field%} !!}" type="editor"/>';

                    break;

                case 'markdown':
                    $form_stub = '<x-instant::markdown-field name="{%field%}" id="{%field%}" label="{%label%}" class="{%class_tag%}" {%attributes_tag%} :value="$model->{%field%} ?? \'\'"/>';
                    $read_stub = '<x-instant::display-field name="{%field%}" id="{%field%}" label="{%label%}" value="{!! $model->{%field%} !!}" type="markdown"/>';

                    break;

                default:
                    $this->error('Input Type not supported: <info>'.$field.':'.$options['type'].'</info>');

                    break;
            }
            $form_fields[] = str_replace(array_keys($replace_for_form), $replace_for_form, $form_stub);
            $read_fields[] = str_replace(array_keys($replace_for_form), $replace_for_form, $read_stub);

            if (in_array($options['type'], ['file', 'image'])) {
                if (isset($options['attributes']['multiple']) && 'multiple' == $options['attributes']['multiple']) {
                    $upload_strings[] = <<<EOT
                                \$uploaded_files = [];
                                if (\$request->hasFile('{$field}')) {
                                    foreach(\$request->file('{$field}') as \$key => \$file)
                                    {
                                        // \$uploaded_files[] = str_replace('public', 'storage', \$request->file('{$field}.'.\$key)->store('public/{$model_variable}/{$field}'));
                                        \$uploaded_files[] = str_replace('public', 'storage', Storage::disk('public')->putFile('{$model_variable}/{$field}', \$request->file('{$field}.'.\$key)));
                                    }
                                    unset(\$request['{$field}']);
                                    \$request->merge([
                                        '{$field}' => \$uploaded_files,
                                    ]);
                                }
                        EOT;
                } else {
                    $upload_strings[] = <<<EOT
                                if (\$request->hasFile('{$field}')) {
                                    // \$path = str_replace('public', 'storage', \$request->file('{$field}')->store('public/{$model_variable}/{$field}'));
                                    \$path = str_replace('public', 'storage', Storage::disk('public')->putFile('{$model_variable}/{$field}', \$request->file('{$field}')));
                                    unset(\$request['{$field}']);
                                    \$request->merge([
                                        '{$field}' => \$path,
                                    ]);
                                }
                        EOT;
                }
            }

            $scopes = [];
            $searches = [];
            if ($options['search']) {
                $scopes[] = 'public function scopeFilter'.studly_case($field).'($query, $search)';
                $scopes[] = $this->indent().'{';

                $searches[] = '<div class="form-group">';
                $searches[] = $this->indent().'<label for="'.$field.'">'.$options['label'].'</label>';

                switch ($options['type']) {
                    case 'date':
                        $scopes[] = $this->indent().'$date = $this->getDateFilter($search);';
                        $scopes[] = $this->indent().'return $query->whereBetween(\''.$field.'\', [ $this->inUserTimezone($date[\'start_at\']), $this->inUserTimezone($date[\'stop_at\'])]);';
                        $searches[] = $this->indent().'<x-instant::search-date-field type="text" name="'.$field.'" id="'.$field.'"/>';

                        break;

                    case 'select':
                    case 'datalist':
                    case 'radio':
                    case 'checkbox':
                        $scopes[] = $this->indent().'    return $query->whereIn(\''.$field.'\', $search);';
                        $searches[] = $this->indent().'<x-instant::search-select-field name="'.$field.'" id="'.$field.'" :options="'.$select_options.'"/>';

                        break;

                    case 'text':
                    case 'textarea':
                        $scopes[] = $this->indent().'return $query->where(\''.$field.'\', \'like\', "%{$search}%");';
                        $searches[] = $this->indent().'<x-instant::search-input-field type="text" name="'.$field.'" id="'.$field.'"/>';
                        $searchable_fields[] = "'".$field."'";

                        break;
                }

                $searches[] = $this->indent().'</div>';
                $search_fields[] = implode(PHP_EOL, $searches).PHP_EOL;

                $scopes[] = $this->indent().'}';
                $search_scopes[] = implode(PHP_EOL, $scopes).PHP_EOL;
            }
        } // end foreach
        foreach ($this->config['appends'] as $key => $value) {
            $appends[] = "'{$key}'";
            $mutator = [];
            $key_name = studly_case($key);
            $mutator[] = 'public function get'.$key_name.'Attribute($value)'." {\n";
            $mutator[] = $this->indent(2).$this->replaceholder($value)."\n".$this->indent(1).'}';
            $mutators[] = implode('', $mutator);
        }
        $appends[] = "'readUrl'";

        $this->replaces['{%fillable_array%}'] = implode(",\n".$this->indent(2), $fillables);
        $this->replaces['{%mutators%}'] = implode(",\n".$this->indent(2), $mutators);
        $this->replaces['{%model_casts%}'] = "protected \$casts = [\n".$this->indent(2).implode(",\n".$this->indent(2), $casts)."\n".$this->indent(1).'];';
        $this->replaces['{%model_appends%}'] = "protected \$appends = [\n".$this->indent(2).implode(",\n".$this->indent(2), $appends)."\n".$this->indent(1).'];';
        $this->replaces['{%searchable_fields%}'] = "protected \$searchableFields = [\n".$this->indent(2).implode(",\n".$this->indent(2), $searchable_fields)."\n".$this->indent(1).'];';
        $this->replaces['{%relationships%}'] = $relationships ? trim(implode(PHP_EOL, $relationships)) : '';
        $this->replaces['{%relationships_query%}'] = $relationships_query ? "->with('".implode("', '", $relationships_query)."')" : '';
        $this->replaces['{%user_timezones%}'] = $user_timezones ? trim(implode(PHP_EOL, $user_timezones)) : '';
        $this->replaces['{%validations_create%}'] = isset($validations['create']) ? trim(implode(PHP_EOL, $validations['create'])) : '';
        $this->replaces['{%validations_update%}'] = isset($validations['update']) ? trim(implode(PHP_EOL, $validations['update'])) : '';
        $this->replaces['{%form_fields%}'] = isset($form_fields) ? trim(implode(PHP_EOL.$this->indent(4), $form_fields)) : '';
        $this->replaces['{%read_fields%}'] = isset($read_fields) ? trim(implode(PHP_EOL.$this->indent(4), $read_fields)) : '';
        $this->replaces['{%settings_options_up%}'] = isset($settings_options_up) ? trim(implode(PHP_EOL.$this->indent(2), $settings_options_up)) : '';
        $this->replaces['{%settings_options_down%}'] = isset($settings_options_down) ? trim(implode(PHP_EOL.$this->indent(2), $settings_options_down)) : '';
        $this->replaces['{%search_scopes%}'] = isset($search_scopes) ? trim(implode(PHP_EOL.$this->indent(1), $search_scopes)) : '';
        $this->replaces['{%search_fields%}'] = isset($search_fields) ? trim(implode(PHP_EOL.$this->indent(1), $search_fields)) : '';
        $this->replaces['{%table_fields%}'] = isset($table_fields) ? trim(implode(','.PHP_EOL.$this->indent(3).'  ', $table_fields)).',' : '';
        $this->replaces['{%upload_strings%}'] = isset($upload_strings) ? trim(implode(PHP_EOL.'  ', $upload_strings)) : '';

        $this->model();
        $this->route();
        $this->api_route();
        $this->controller();
        $this->api_controller();
        $this->menu();
        $this->views();

        if ($this->config['migration']) {
            if (isset($migration_codes) && count($migration_codes)) {
                $this->migration_codes = implode("\n".$this->indent(3), $migration_codes);
            }
            $this->migration();
        }
    }

    protected function route()
    {
        if ($this->brand) {
            $this->file->ensureDirectoryExists(base_path('brand/'.$this->brand.'/routes/instant'));
            $route_file = base_path('brand/'.$this->brand.'/routes/instant/'.$this->replaces['{%model_variable%}'].'Routes.php');
        } else {
            $this->file->ensureDirectoryExists(base_path('routes/instant'));
            $route_file = base_path('routes/instant').'/'.$this->replaces['{%model_variable%}'].'Routes.php';
        }
        $route_stub = $this->stub_path.'/route.stub';
        if (!$this->file->exists($route_stub)) {
            $this->error('API Route stub file not found: <info>'.$route_stub.'</info>');

            return;
        }
        $route_stub = $this->file->get($route_stub);
        if ($this->brand) {
            $route_stub = str_replace("'App\Http\Controllers\Admin'", "'\\Brand\\".$this->brand."\\Controllers\\Admin'", $route_stub);
        }
        $this->file->put($route_file, $this->replaceholder($route_stub));
        $this->line('Route file created: <info>'.$route_file.'</info>');
    }

    protected function api_route()
    {
        if ($this->brand) {
            $this->file->ensureDirectoryExists(base_path('brand/'.$this->brand.'/routes/instant/api'));
            $route_file = base_path('brand/'.$this->brand.'/routes/instant/api/'.$this->replaces['{%model_variable%}'].'Routes.php');
        } else {
            $this->file->ensureDirectoryExists(base_path('routes/instant/api'));
            $route_file = base_path('routes/instant/api').'/'.$this->replaces['{%model_variable%}'].'Routes.php';
        }
        $route_stub = $this->stub_path.'/api_route.stub';
        if (!$this->file->exists($route_stub)) {
            $this->error('API Route stub file not found: <info>'.$route_stub.'</info>');

            return;
        }
        $route_stub = $this->file->get($route_stub);
        if ($this->brand) {
            $route_stub = str_replace("'App\Http\Controllers\Api'", "'\\Brand\\".$this->brand."\\Controllers\\Api'", $route_stub);
        }
        $this->file->put($route_file, $this->replaceholder($route_stub));
        $this->line('API Route file created: <info>'.$route_file.'</info>');
    }

    protected function menu()
    {
        $menu_stub = $this->stub_path.'/menu.stub';
        if (!$this->file->exists($menu_stub)) {
            $this->error('Menu stub file not found: <info>'.$menu_stub.'</info>');

            return;
        }
        $menu_stub = $this->file->get($menu_stub);
        if ($this->brand) {
            $toWriteInFile = base_path('brand/'.$this->brand.'/resources/views/admin/menu.blade.php');
        } else {
            $toWriteInFile = resource_path('views/vendor/instant/components/custom-admin-menu.blade.php');
        }

        $toWriteInFileContent = $this->file->get($toWriteInFile);
        $replaceContent = $this->replaceholder($menu_stub);
        if (false === strpos($toWriteInFileContent, $replaceContent)) {
            $replaceContent = str_replace('<!--DoNotRemoveMe-->', $replaceContent."\n".$this->indent(0).'<!--DoNotRemoveMe-->', $toWriteInFileContent);
            $this->file->put($toWriteInFile, $replaceContent);
            $this->line('Menu included: <info>'.config('instant.routes_dir').'</info>');
        }
    }

    protected function model()
    {
        $model_stub = $this->stub_path.'/model.stub';
        if (!$this->file->exists($model_stub)) {
            $this->error('Model stub file not found: <info>'.$model_stub.'</info>');

            return;
        }
        if ($this->brand) {
            $model_file = base_path('brand/'.$this->brand.'/Models/'.$this->replaces['{%model%}'].'.php');
        } else {
            $model_file = app_path('Models/'.$this->replaces['{%model%}'].'.php');
        }

        $model_stub = $this->file->get($model_stub);
        $this->file->put($model_file, $this->replaceholder($model_stub));
        $this->line('Model file created: <info>'.$model_file.'</info>');
    }

    protected function controller()
    {
        if ($this->brand) {
            $controller_dir = base_path('brand/'.$this->brand.'/Controllers/Admin');
        } else {
            $controller_dir = app_path('Http/Controllers/Admin');
        }
        $this->file->ensureDirectoryExists($controller_dir);
        $controller_stub = $this->stub_path.'/controller.stub';
        if (!$this->file->exists($controller_stub)) {
            $this->error('Controller stub file not found: <info>'.$controller_stub.'</info>');

            return;
        }
        $controller_file = $controller_dir.'/'.$this->replaces['{%model%}'].'Controller.php';

        $controller_stub = $this->file->get($controller_stub);
        $this->file->put($controller_file, $this->replaceholder($controller_stub));
        $this->line('Controller file created: <info>'.$controller_file.'</info>');
    }

    protected function api_controller()
    {
        if ($this->brand) {
            $controller_dir = base_path('brand/'.$this->brand.'/Controllers/Api');
        } else {
            $controller_dir = app_path('Http/Controllers/Api');
        }
        $this->file->ensureDirectoryExists($controller_dir);
        $controller_stub = $this->stub_path.'/api_controller.stub';
        if (!$this->file->exists($controller_stub)) {
            $this->error('Api Controller stub file not found: <info>'.$controller_stub.'</info>');

            return;
        }
        $controller_file = $controller_dir.'/'.$this->replaces['{%model%}'].'Controller.php';
        $controller_stub = $this->file->get($controller_stub);
        $this->file->put($controller_file, $this->replaceholder($controller_stub));
        $this->line('Api Controller file created: <info>'.$controller_file.'</info>');
    }

    protected function views()
    {
        $view_files = ['search', 'index', 'edit', 'create', 'show', 'actions'];
        if (false !== $this->config['orderable']) {
            $view_files[] = 'orderable';
        }

        if ($this->brand) {
            $view_path = base_path('brand/'.$this->brand.'/resources/views/admin/'.$this->replaces['{%model_variable%}']);
        } else {
            $view_path = resource_path('views/admin/'.$this->replaces['{%model_variable%}']);
        }

        if (!$this->file->exists($view_path)) {
            $this->file->makeDirectory($view_path, 0755, true);
        }

        foreach ($view_files as $mode) {
            $view_stub = $this->stub_path.'/views/'.$mode.'.stub';
            if (!$this->file->exists($view_stub)) {
                $this->error('View stub file not found: <info>'.$view_stub.'</info>');

                return;
            }

            $view_file = $view_path.'/'.$mode.'.blade.php';
            $view_stub = $this->file->get($view_stub);

            $this->file->put($view_file, $this->replaceholder($view_stub));
            $this->line('View file created: <info>'.$view_file.'</info>');
        }
    }

    protected function migration()
    {
        $msg = 'Migration file created';
        $migration_stub = $this->stub_path.'/migration.stub';
        if (!$this->file->exists($migration_stub)) {
            $this->error('Migration stub file not found: <info>'.$migration_stub.'</info>');

            return;
        }
        $filename = "instant{$this->model}Table.php";
        if ($this->brand) {
            $db_path = base_path('brand/'.$this->brand.'/database/migrations');
            $migration_file = base_path('brand/'.$this->brand.'/database/migrations/'.date('Y_m_d_000000_').$filename);
        } else {
            $db_path = database_path('migrations/');
            $migration_file = database_path('migrations/'.date('Y_m_d_000000_').$filename);
        }
        foreach ($this->file->files($db_path) as $file) {
            if (str_contains($file->getPathname(), $filename)) {
                $migration_file = $file->getPathname();
                $msg = 'Migration file overwritten';
            }
        }

        $migrations_stub = $this->file->get($migration_stub);
        $this->file->put($migration_file, $this->replaceholder($migrations_stub));
        $this->line($msg.': <info>'.$migration_file.'</info>');
    }

    protected function orderable()
    {
        $orderable = $this->config['orderable'];
        if (false === $orderable) {
            $this->replaces['{%orderable_migration%}'] = $this->replaces['{%orderable_link%}'] = $this->replaces['{%orderable_field%}'] = $this->replaces['{%orderable_label%}'] = $this->replaces['{%orderable_routes%}'] = $this->replaces['{%orderable_controller%}'] = '';

            return;
        }
        $this->replaces['{%orderable_field%}'] = $orderable;
        $this->replaces['{%orderable_label%}'] = $this->config['form'][$orderable]['label'];
        $orderable_routes = [];
        $orderable_routes[] = "Route::match(['get', 'head'], 'orderable/{orderable?}', '{$this->replaces['{%model_class%}']}Controller@orderable')->name('{$this->replaces['{%model_variable%}']}.orderable');";
        $orderable_routes[] = "Route::match(['post'], 'orderable/{orderable?}', '{$this->replaces['{%model_class%}']}Controller@orderableUpdate')->name('{$this->replaces['{%model_variable%}']}.orderableUpdate');";
        $this->replaces['{%orderable_routes%}'] = isset($orderable_routes) ? trim(implode(PHP_EOL.$this->indent(2), $orderable_routes)) : '';

        $this->replaces['{%orderable_controller%}'] = $this->replaceholder($this->file->get($this->stub_path.'/orderable_controller.stub'));

        $this->replaces['{%orderable_link%}'] = "<a href=\"{{ route('{$this->replaces['{%model_variable%}']}.orderable',\$model->{$orderable}) }}\" class=\"btn btn-link text-secondary p-1\" title=\"Reorder List\"><i class=\"fas fa-sort-numeric-up\"></i></a>";
        $this->replaces['{%orderable_migration%}'] = '$table->integer(\'seq\')->nullable()->default(1);';
    }

    protected function replaceholder($content)
    {
        if (isset($this->migration_codes)) {
            $this->replaces['{%migration_codes%}'] = $this->migration_codes;
        }
        foreach ($this->replaces as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        return $content;
    }

    protected function putInChains($value)
    {
        $chains = [];
        foreach (explode('|', $value) as $chain) {
            $method_params = explode(':', $chain);
            $method = $method_params[0];
            $params_typed = [];
            if (isset($method_params[1])) {
                foreach (explode(',', $method_params[1]) as $param) {
                    $params_typed[] = (in_array($param, ['true', 'false']) || is_numeric($param)) ? $param : "'{$param}'";
                }
            }
            $chains[] = $method.'('.implode(', ', $params_typed).')';
        }

        return implode('->', $chains);
    }

    protected function indent($multiplier = 1)
    {
        // add indents to line
        return str_repeat('    ', $multiplier);
    }
}
