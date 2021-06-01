<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

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
        inertia()->share('moduleName', 'Cronjob Management');
    }

    public function index(Request $request)
    {
        $can = [
            'create' => auth()->user()->can('create-cronjobs'),
            'read' => auth()->user()->can('read-cronjobs'),
            'update' => auth()->user()->can('update-cronjobs'),
            'delete' => auth()->user()->can('delete-cronjobs'),
        ];
        $models = app(config('instant.Models.Cronjob'))->query()
                ->checkBrand()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
                ->paginate($request->get('take', 25));
        foreach ($models as $model) {
            $model->last_run_date = collect(array_keys($model->output))->first();
        }
        if ('' != $request->get('filters', '')) {
            $models->appends(['filters' => $request->get('filters', '')]);
        }
        if ('' != $request->get('sort', '')) {
            $models->appends(['sort' => $request->get('sort', ''), 'direction' => $request->get('direction', 'asc')]);
        }
        $columns = [
            ['title' => 'Name', 'data' => 'name', 'sortable' => true, 'class' => 'text-left'],
            ['title' => 'Timezone', 'data' => 'timezone', 'sortable' => false, 'class' => 'text-left'],
            ['title' => 'Frequency', 'data' => 'frequency', 'sortable' => false, 'class' => 'text-left'],
            ['title' => 'Status', 'data' => 'status_name', 'sortable' => false, 'class' => 'text-left'],
            ['title' => 'Created Date', 'data' => 'created_at', 'sortable' => false, 'class' => 'text-left'],
            ['title' => 'Last Run Date', 'data' => 'last_run_date', 'sortable' => false, 'class' => 'text-left'],
            ['title' => '', 'data' => 'actionsView', 'class' => 'text-center'],
        ];

        return inertia('Admin/Cronjob/Index', compact('columns', 'models', 'can'));
    }

    public function create(Request $request)
    {
        $this->shareData();
        return inertia('Admin/Cronjob/Create');
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

        return Redirect::route('cronjob')->with([
            'status' => 'success',
            'flash' => 'Cronjob Created.',
        ]);
    }

    public function show($id)
    {
        $model = app(config('instant.Models.Cronjob'))->query()->with(['creator','modifier'])->findOrFail($id);
        $this->shareData();
        return inertia('Admin/Cronjob/Show', compact('model'));
    }

    public function edit(Request $request, $id)
    {
        $model = app(config('instant.Models.Cronjob'))->query()->with(['creator','modifier'])->findOrFail($id);
        $this->shareData();
        return inertia('Admin/Cronjob/Edit', compact('model'));
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

        return Redirect::route('cronjob.edit', [$model->id])->with([
            'status' => 'success',
            'flash' => 'Cronjob Updated.',
        ]);
    }

    public function destroy($id)
    {
        $model = app(config('instant.Models.Cronjob'))->query()->with(['creator','modifier'])->findOrFail($id);
        $model->delete();

        return Redirect::route('cronjob')->with([
            'status' => 'success',
            'flash' => 'Cronjob Deleted.'
        ]);
    }

    protected function shareData()
    {
        $timezones = [];
        foreach (timezones() as $value => $label) {
            $timezones[] = compact('value', 'label');
        }
        $cronjob_frequencies = [];
        foreach (cronjob_frequencies() as $value => $label) {
            $cronjob_frequencies[] = compact('value', 'label');
        }
        $cronjob_status = [];
        foreach (settings('cronjob_status') as $value => $label) {
            $cronjob_status[] = compact('value', 'label');
        }
        return inertia()->share(compact('timezones', 'cronjob_frequencies', 'cronjob_status'));
    }
}
