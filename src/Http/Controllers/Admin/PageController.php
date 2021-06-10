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
        $this->middleware('can:migrate-pages')->only('migration');
        $this->middleware('reauth_admin')->only(['edit', 'destroy']);
        inertia()->share('moduleName', 'Brand Management');
    }

    public function index(Request $request)
    {
        $can = [
            'create' => auth()->user()->can('create-pages'),
            'read' => auth()->user()->can('read-pages'),
            'update' => auth()->user()->can('update-pages'),
            'delete' => auth()->user()->can('delete-pages'),
            'migrate' => auth()->user()->can('migrate-pages'),
        ];
        $models = app(config('instant.Models.Page'))->query()
                ->with('brand')
                ->checkBrand()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
                ->paginate($request->get('take', 25));
        // foreach ($models as $model) {
        // }
        if ('' != $request->get('filters', '')) {
            $models->appends(['filters' => $request->get('filters', '')]);
        }
        if ('' != $request->get('sort', '')) {
            $models->appends(['sort' => $request->get('sort', ''), 'direction' => $request->get('direction', 'asc')]);
        }
        $columns = [
            ['title' => 'Brand', 'data' => 'brand.name', 'sortable' => true],
            ['title' => 'Name', 'data' => 'name', 'sortable' => true],
            ['title' => 'Locale', 'data' => 'locale', 'sortable' => true],
            ['title' => 'Slug', 'data' => 'slug', 'sortable' => true],
            ['title' => 'Template', 'data' => 'template', 'sortable' => true],
            ['title' => 'Published Date', 'data' => 'published_at', 'sortable' => false],
            ['title' => 'Expired Date', 'data' => 'expired_at', 'sortable' => false],
            ['title' => 'Status', 'data' => 'status_name', 'sortable' => false],
            ['title' => 'Created Date', 'data' => 'created_at', 'sortable' => false],
            ['title' => '', 'data' => 'actionsView'],
        ];

        return inertia('Admin/Page.index', compact('columns', 'models', 'can'));
    }

    public function create(Request $request)
    {
        return inertia('Admin/Page.create');
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

        return Redirect::route('page')->with([
            'status' => 'success',
            'flash' => 'Page Created.',
        ]);
    }

    public function show($id)
    {
        $model = app(config('instant.Models.Page'))->query()->with(['creator','modifier'])->findOrFail($id);

        return inertia('Admin/Page.show', compact('model'));
    }

    public function replicate($id)
    {
        $model = app(config('instant.Models.Page'))->query()->with(['creator','modifier'])->findOrFail($id);
        $newModel = $model->replicate();
        $newModel->push();
        $newModel->locale = null;
        $newModel->saveQuietly();
        audit('Replicated Page: '.$newModel->id, [], $newModel);

        return Redirect::route('page.edit', [$newModel->id])->with([
            'status' => 'success',
            'flash' => 'Page Replicated.',
        ]);
    }

    public function preview($id)
    {
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
        $model = app(config('instant.Models.Page'))->query()->with(['creator','modifier'])->findOrFail($id);

        return inertia('Admin/Page.edit', compact('model'));
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

        return Redirect::route('page.edit', [$model->id])->with([
            'status' => 'success',
            'flash' => 'Page Updated.',
        ]);
    }

    public function destroy($id)
    {
        $model = app(config('instant.Models.Page'))->query()->with(['creator','modifier'])->findOrFail($id);
        $model->delete();

        return Redirect::back()->with([
            'status' => 'success',
            'flash' => 'Page Deleted.',
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

        return inertia('Admin/Page.migration', compact('string'));
    }
}
