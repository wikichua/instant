<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CronjobController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:create-cronjobs')->only(['create', 'store']);
        $this->middleware('can:read-cronjobs')->only(['index', 'read']);
        $this->middleware('can:update-cronjobs')->only(['edit', 'update']);
        $this->middleware('can:delete-cronjobs')->only('destroy');

        $this->middleware('reauth_admin')->only(['edit', 'destroy']);
        if (false == app()->runningInConsole()) {
            \Breadcrumbs::for('home', function ($trail) {
                $trail->push('Cron Jobs Listing', route('cronjob'));
            });
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $models = app(config('instant.Models.Cronjob'))->query()
                ->checkBrand()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
            ;
            $paginated = $models->paginate($request->get('take', 25));
            foreach ($paginated as $model) {
                $model->actionsView = view('dashing::admin.cronjob.actions', compact('model'))->render();
                $model->last_run_date = collect(array_keys($model->output))->first();
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
        $getUrl = route('cronjob');
        $html = [
            ['title' => 'Name', 'data' => 'name', 'sortable' => true],
            ['title' => 'Timezone', 'data' => 'timezone', 'sortable' => false, 'filterable' => true],
            ['title' => 'Frequency', 'data' => 'frequency', 'sortable' => false, 'filterable' => true],
            ['title' => 'Status', 'data' => 'status_name', 'sortable' => false, 'filterable' => true],
            ['title' => 'Created Date', 'data' => 'created_at', 'sortable' => false, 'filterable' => true],
            ['title' => 'Last Run Date', 'data' => 'last_run_date', 'sortable' => false, 'filterable' => true],
            ['title' => '', 'data' => 'actionsView'],
        ];

        return view('dashing::admin.cronjob.index', compact('html', 'getUrl'));
    }

    public function create(Request $request)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Create Cron Job');
        });

        return view('dashing::admin.cronjob.create');
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
        ]);

        $model = app(config('instant.Models.Cronjob'))->create($request->all());

        return response()->json([
            'status' => 'success',
            'flash' => 'Cronjob Created.',
            'reload' => false,
            'relist' => false,
            'redirect' => route('cronjob'),
        ]);
    }

    public function show($id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Show Cron Job');
        });
        $model = app(config('instant.Models.Cronjob'))->query()->with(['creator','modifier'])->findOrFail($id);

        return view('dashing::admin.cronjob.show', compact('model'));
    }

    public function edit(Request $request, $id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Edit Cron Job');
        });
        $model = app(config('instant.Models.Cronjob'))->query()->with(['creator','modifier'])->findOrFail($id);

        return view('dashing::admin.cronjob.edit', compact('model'));
    }

    public function update(Request $request, $id)
    {
        $model = app(config('instant.Models.Cronjob'))->query()->with(['creator','modifier'])->findOrFail($id);

        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $request->merge([
            'updated_by' => auth()->id(),
        ]);

        $model->update($request->all());

        return response()->json([
            'status' => 'success',
            'flash' => 'Cronjob Updated.',
            'reload' => false,
            'relist' => false,
            'redirect' => route('cronjob.edit', [$model->id]),
        ]);
    }

    public function destroy($id)
    {
        $model = app(config('instant.Models.Cronjob'))->query()->with(['creator','modifier'])->findOrFail($id);
        $model->delete();

        return response()->json([
            'status' => 'success',
            'flash' => 'Cronjob Deleted.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
        ]);
    }
}
