<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read', 'preview']);
        $this->middleware('can:create-pages')->only(['create', 'store']);
        $this->middleware('can:read-pages')->only(['index', 'read', 'preview']);
        $this->middleware('can:update-pages')->only(['edit', 'update']);
        $this->middleware('can:delete-pages')->only('destroy');
        $this->middleware('can:Migrate Pages')->only('migration');

        $this->middleware('reauth_admin')->only(['edit', 'destroy']);
        if (false == app()->runningInConsole()) {
            \Breadcrumbs::for('home', function ($trail) {
                $trail->push('Page Listing', route('page'));
            });
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $models = app(config('instant.Models.Page'))->query()
                ->with('brand')
                ->checkBrand()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
            ;
            $paginated = $models->paginate($request->get('take', 25));
            foreach ($paginated as $model) {
                $model->actionsView = view('dashing::admin.page.actions', compact('model'))->render();
            }
            if ('' != $request->get('filters', '')) {
                $paginated->appends(['filters' => $request->get('filters', '')]);
            }
            if ('' != $request->get('sort', '')) {
                $paginated->appends(['sort' => $request->get('sort', ''), 'direction' => $request->get('direction', 'asc')]);
            }
            $links = $paginated->onEachSide(5)->links()->render();
            $currentUrl = $request->fullUrl();

            return compact('paginated', 'links', 'currentUrl');
        }
        $getUrl = route('page');
        $html = [
            ['title' => 'Brand', 'data' => 'brand.name', 'sortable' => true],
            ['title' => 'Name', 'data' => 'name', 'sortable' => true],
            ['title' => 'Locale', 'data' => 'locale', 'sortable' => true],
            ['title' => 'Slug', 'data' => 'slug', 'sortable' => true],
            ['title' => 'Template', 'data' => 'template', 'sortable' => true],
            ['title' => 'Published Date', 'data' => 'published_at', 'sortable' => false, 'filterable' => true],
            ['title' => 'Expired Date', 'data' => 'expired_at', 'sortable' => false, 'filterable' => true],
            ['title' => 'Status', 'data' => 'status_name', 'sortable' => false, 'filterable' => true],
            ['title' => 'Created Date', 'data' => 'created_at', 'sortable' => false, 'filterable' => true],
            ['title' => '', 'data' => 'actionsView'],
        ];

        return view('dashing::admin.page.index', compact('html', 'getUrl'));
    }

    public function create(Request $request)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Create Page');
        });

        return view('dashing::admin.page.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand_id' => 'required',
            'locale' => 'required',
            'name' => 'required',
            'slug' => 'required',
            'published_at' => 'required',
            'expired_at' => 'required',
            'status' => 'required',
        ]);

        $request->merge([
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $model = app(config('instant.Models.Page'))->create($request->all());

        sendAlert([
            'brand_id' => $request->input('brand_id', 0),
            'link' => $model->readUrl,
            'message' => 'New Page Added. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-pages', $request->input('brand_id', 0)),
            'icon' => $model->menu_icon,
        ]);

        return response()->json([
            'status' => 'success',
            'flash' => 'Page Created.',
            'reload' => false,
            'relist' => false,
            'redirect' => route('page'),
            // 'redirect' => route('page.show', [$model->id]),
        ]);
    }

    public function show($id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Show Page');
        });
        $model = app(config('instant.Models.Page'))->query()->with(['creator','modifier'])->findOrFail($id);

        return view('dashing::admin.page.show', compact('model'));
    }

    public function replicate($id)
    {
        $model = app(config('instant.Models.Page'))->query()->with(['creator','modifier'])->findOrFail($id);
        $newModel = $model->replicate();
        $newModel->push();
        $newModel->locale = null;
        $newModel->saveQuietly();
        audit('Replicated Page: '.$newModel->id, [], $newModel);

        return response()->json([
            'status' => 'success',
            'flash' => 'Page Replicated.',
            'reload' => false,
            'relist' => false,
            'redirect' => route('page.edit', [$newModel->id]),
        ]);
    }

    public function preview($id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('preview-page');
        });
        $model = app(config('instant.Models.Page'))->query()->with(['creator','modifier'])->findOrFail($id);
        if (0 != $model->brand_id) {
            $brandName = strtolower($model->brand->name);
            \View::addNamespace($brandName, base_path('brand/'.$model->brand->name.'/resources/views'));
            \Blade::componentNamespace('\\Brand\\'.$model->brand->name.'\\Components', $brandName);
            \Config::set('auth.guards', [
                'brand_web' => [
                    'driver' => 'session',
                    'provider' => 'brand_users',
                ],
            ]);
            \Config::set('auth.providers', [
                'brand_users' => [
                    'driver' => 'eloquent',
                    'model' => '\\Brand\\'.$model->brand->name.'\\Models\\User',
                ],
            ]);
        }

        return view($brandName.'::pages.page', compact('model'));
    }

    public function edit(Request $request, $id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Edit Page');
        });
        $model = app(config('instant.Models.Page'))->query()->with(['creator','modifier'])->findOrFail($id);

        return view('dashing::admin.page.edit', compact('model'));
    }

    public function update(Request $request, $id)
    {
        $model = app(config('instant.Models.Page'))->query()->with(['creator','modifier'])->findOrFail($id);

        $request->validate([
            'brand_id' => 'required',
            'locale' => 'required',
            'name' => 'required',
            'slug' => 'required',
            'published_at' => 'required',
            'expired_at' => 'required',
            'status' => 'required',
        ]);

        $request->merge([
            'updated_by' => auth()->id(),
        ]);

        $model->update($request->all());

        return response()->json([
            'status' => 'success',
            'flash' => 'Page Updated.',
            'reload' => false,
            'relist' => false,
            'redirect' => route('page.edit', [$model->id]),
            // 'redirect' => route('page.show', [$model->id]),
        ]);
    }

    public function destroy($id)
    {
        $model = app(config('instant.Models.Page'))->query()->with(['creator','modifier'])->findOrFail($id);
        $model->delete();

        return response()->json([
            'status' => 'success',
            'flash' => 'Page Deleted.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
        ]);
    }

    public function templates($brand_id)
    {
        $templates = [];
        if ('' != $brand_id) {
            $model = app(config('instant.Models.Brand'))->query()->findOrFail($brand_id);
            if ($model) {
                \Config::set('brand', array_merge(
                    require base_path('brand/'.$model->name.'/config/main.php')
                ));
                foreach (File::files(config('brand.template_path', base_path('brand/'.$model->name.'/resources/views/layouts'))) as $file) {
                    $name = str_replace('.blade.php', '', $file->getBasename());
                    $templates[] = [
                        'value' => 'layouts.'.$name,
                        'text' => $file->getBasename()
                    ];
                }
            }
        }

        return response()->json($templates);
    }

    public function migration(Request $request, $id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Migration Script');
        });
        $model = app(config('instant.Models.Page'))->query()->with(['creator','modifier'])->findOrFail($id);
        $brandString = $model->brand->name;
        unset($model->id);
        $model->brand_id = '$brand->id';
        $model->created_by = 1;
        $model->updated_by = 1;
        $code = str_replace('\'$brand->id\'', '$brand->id', var_export($model->getAttributes(), 1));
        $string = <<<EOL
            \$brand = app(config('instant.Models.Brand'))->query()->where('name','{$brandString}')->first();
            app(config('instant.Models.Page'))->query()->create({$code});
            EOL;

        return view('dashing::admin.page.migration', compact('string'));
    }
}
