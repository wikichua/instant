<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;

class ComponentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:read-components')->only(['index', 'read']);
        if (false == app()->runningInConsole()) {
            \Breadcrumbs::for('home', function ($trail) {
                $trail->push('Component Listing', route('component'));
            });
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $models = app(config('instant.Models.Component'))->query()
                ->with('brand')
                ->checkBrand()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
            ;
            $paginated = $models->paginate($request->get('take', 25));
            foreach ($paginated as $model) {
                $model->actionsView = view('dashing::admin.component.actions', compact('model'))->render();
                $model->usage = "&lt;x-{$model->brand_name}::".\Str::kebab($model->name).">&lt;/x-{$model->brand_name}::".\Str::kebab($model->name).'>';
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
        $getUrl = route('component');
        $html = [
            ['title' => 'Name', 'data' => 'name', 'sortable' => true, 'filterable' => true],
            ['title' => 'Brand', 'data' => 'brand.name', 'sortable' => false, 'filterable' => false],
            ['title' => 'Usage Example', 'data' => 'usage', 'sortable' => false, 'filterable' => false],
            ['title' => '', 'data' => 'actionsView'],
        ];

        return view('dashing::admin.component.index', compact('html', 'getUrl'));
    }

    public function show($id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Show Component');
        });
        $model = app(config('instant.Models.Component'))->query()->with(['creator','modifier'])->findOrFail($id);

        return view('dashing::admin.component.show', compact('model'));
    }

    public function try(Request $request, $id)
    {
        $model = app(config('instant.Models.Component'))->query()->with(['creator','modifier'])->findOrFail($id);
        $request->validate([
            'code' => 'required',
        ]);
        if (0 != $model->brand_id) {
            View::addNamespace(strtolower($model->brand_name), base_path('brand/'.$model->brand->name.'/resources/views'));
            Blade::componentNamespace('\\Brand\\'.$model->brand->name.'\\Components', strtolower($model->brand_name));
        }

        return viewRenderer($request->input('code'));
    }
}
