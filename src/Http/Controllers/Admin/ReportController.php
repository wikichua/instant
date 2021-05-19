<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Rap2hpoutre\FastExcel\SheetCollection;

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
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $models = app(config('instant.Models.Report'))->query()
                ->checkBrand()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
            ;
            $paginated = $models->paginate($request->get('take', 25));
            foreach ($paginated as $model) {
                $model->cache_status = false == Cache::has('report-'.str_slug($model->name)) ? 'Processing' : 'Ready';
                $model->actionsView = view('dashing::admin.report.actions', compact('model'))->render();
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
        $getUrl = route('report');
        $html = [
            ['title' => 'Name', 'data' => 'name', 'sortable' => true],
            ['title' => 'Status', 'data' => 'status_name', 'sortable' => false, 'filterable' => true],
            ['title' => 'Report Status', 'data' => 'cache_status', 'sortable' => false, 'filterable' => true],
            ['title' => 'Last Run', 'data' => 'generated_at', 'sortable' => false, 'filterable' => true],
            ['title' => 'Next Run', 'data' => 'next_generate_at', 'sortable' => false, 'filterable' => true],
            ['title' => '', 'data' => 'actionsView'],
        ];

        return view('dashing::admin.report.index', compact('html', 'getUrl'));
    }

    public function create(Request $request)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Create Report');
        });

        return view('dashing::admin.report.create');
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

        return response()->json([
            'status' => 'success',
            'flash' => 'Report Created.',
            'reload' => false,
            'relist' => false,
            // 'redirect' => route('report'),
            'redirect' => route('report.show', [$model->id]),
        ]);
    }

    public function show($id)
    {
        \Breadcrumbs::for('home', function ($trail) {
            $trail->parent('home');
            $trail->push('Show Report');
        });
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
        $report = $model;
        return view('dashing::admin.report.show', compact('model', 'models', 'report'));
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

        return fastexcel()->data($sheets)->download(\Str::studly($model->name).'.xlsx');
    }

    public function edit(Request $request, $id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Edit Report');
        });
        $model = app(config('instant.Models.Report'))->query()->with(['creator','modifier'])->findOrFail($id);

        return view('dashing::admin.report.edit', compact('model'));
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

        return response()->json([
            'status' => 'success',
            'flash' => 'Report Updated.',
            'reload' => false,
            'relist' => false,
            // 'redirect' => route('report.edit', [$model->id]),
            'redirect' => route('report.show', [$model->id]),
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

        return response()->json([
            'status' => 'success',
            'flash' => 'Report Deleted.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
        ]);
    }
}
