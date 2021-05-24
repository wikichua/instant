<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Rap2hpoutre\FastExcel\SheetCollection;
use Illuminate\Support\Facades\Redirect;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:create-reports')->only(['create', 'store']);
        $this->middleware('can:read-reports')->only(['index', 'read']);
        $this->middleware('can:update-reports')->only(['edit', 'update']);
        $this->middleware('can:delete-reports')->only('destroy');
        $this->middleware('can:export-reports')->only('export');

        $this->middleware('reauth_admin')->only(['edit', 'destroy']);
        if (false == app()->runningInConsole()) {
            \Breadcrumbs::for('home', function ($trail) {
                $trail->push('Report Listing', route('report'));
            });
        }
        inertia()->share('moduleName', 'Report Management');
    }

    public function index(Request $request)
    {
        $can = [
            'create' => auth()->user()->can('create-reports'),
            'read' => auth()->user()->can('read-reports'),
            'update' => auth()->user()->can('update-reports'),
            'delete' => auth()->user()->can('delete-reports'),
            'export' => auth()->user()->can('export-reports'),
        ];
        $models = app(config('instant.Models.Report'))->query()
                ->checkBrand()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
                ->paginate($request->get('take', 25));
        foreach ($models as $model) {
            $model->cache_status = 'Ready';
            if (config('cache.default') != 'array') {
                $model->cache_status = false == Cache::has('report-'.str_slug($model->name)) ? 'Processing' : 'Ready';
            }
        }
        if ('' != $request->get('filters', '')) {
            $models->appends(['filters' => $request->get('filters', '')]);
        }
        if ('' != $request->get('sort', '')) {
            $models->appends(['sort' => $request->get('sort', ''), 'direction' => $request->get('direction', 'asc')]);
        }
        $columns = [
            ['title' => 'Name', 'data' => 'name', 'sortable' => true],
            ['title' => 'Status', 'data' => 'status_name', 'sortable' => false, 'filterable' => true],
            ['title' => 'Report Status', 'data' => 'cache_status', 'sortable' => false, 'filterable' => true],
            ['title' => 'Last Run', 'data' => 'generated_at', 'sortable' => false, 'filterable' => true],
            ['title' => 'Next Run', 'data' => 'next_generate_at', 'sortable' => false, 'filterable' => true],
            ['title' => '', 'data' => 'actionsView'],
        ];

        return inertia('Admin/Report/Index', compact('columns', 'models', 'can'));
    }

    public function create(Request $request)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Create Report');
        });
        $this->shareData();
        return inertia('Admin/Report/Create', compact('status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $request->merge([
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'name' => str_slug($request->input('name')),
        ]);

        $model = app(config('instant.Models.Report'))->create($request->all());
        sendAlert([
            'brand_id' => 0,
            'link' => $model->readUrl,
            'message' => 'New Report Added. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-reports'),
            'icon' => $model->menu_icon,
        ]);

        return Redirect::route('report.show', [$model->id])->with([
            'status' => 'success',
            'flash' => 'Report Created.',
        ]);
    }

    public function show($id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Show Report');
        });
        $models = [];
        $model = app(config('instant.Models.Report'))->query()->with(['creator','modifier'])->findOrFail($id);
        $models = Cache::get('report-'.str_slug($model->name), function () use ($model, $models) {
            $results = [];
            foreach ($model->queries as $key => $sql) {
                $results[$key]['sql'] = $sql;
                $results[$key]['data'] = array_map(function ($value) {
                    return (array) $value;
                }, \DB::select($sql));
                $columns = array_keys($results[$key]['data'][0]);
                $results[$key]['columns'] = array_map(function ($value) {
                    return ['title' => $value, 'data' => $value];
                }, array_keys($results[$key]['data'][0]));
            }
            return $results;
        });
        return inertia('Admin/Report/Show', compact('model', 'models'));
    }

    public function export($id)
    {
        $models = [];
        $model = app(config('instant.Models.Report'))->query()->with(['creator','modifier'])->findOrFail($id);
        $models = Cache::get('report-'.str_slug($model->name), function () use ($model, $models) {
            foreach ($model->queries as $sql) {
                $models[] = array_map(function ($value) {
                    return (array) $value;
                }, \DB::select($sql));
            }

            return $models;
        });
        $sheets = new SheetCollection($models);

        return fastexcel()->data($sheets)->download(\Str::slug($model->name).'.xlsx');
    }

    public function edit(Request $request, $id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Edit Report');
        });
        $model = app(config('instant.Models.Report'))->query()->with(['creator','modifier'])->findOrFail($id);
        $this->shareData();
        return inertia('Admin/Report/Edit', compact('model'));
    }

    public function update(Request $request, $id)
    {
        $model = app(config('instant.Models.Report'))->query()->with(['creator','modifier'])->findOrFail($id);

        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $request->merge([
            'updated_by' => auth()->id(),
            'name' => str_slug($request->input('name')),
        ]);

        $model->update($request->all());
        sendAlert([
            'brand_id' => 0,
            'link' => $model->readUrl,
            'message' => 'Report Updated. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-reports'),
            'icon' => $model->menu_icon,
        ]);

        return Redirect::route('report.edit', [$id])->with([
            'status' => 'success',
            'flash' => 'Report Updated.',
        ]);
    }

    public function destroy($id)
    {
        $model = app(config('instant.Models.Report'))->query()->with(['creator','modifier'])->findOrFail($id);
        sendAlert([
            'brand_id' => 0,
            'link' => null,
            'message' => 'Report Deleted. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-reports'),
            'icon' => $model->menu_icon,
        ]);
        $model->delete();

        return Redirect::route('report')->with([
            'status' => 'success',
            'flash' => 'Report Deleted.',
        ]);
    }

    protected function shareData()
    {
        $status = [];
        foreach (settings('report_status') as $value => $label) {
            $status[] = compact('value', 'label');
        }
        return inertia()->share(compact('status'));
    }
}
