<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class FailedJobController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:read-failed-jobs')->only(['index', 'read']);
        $this->middleware('can:retry-failed-jobs')->only(['retry']);
        inertia()->share('moduleName', 'Failed Job Management');
    }

    public function summary(Request $request)
    {
        $keys = queue_keys();
        $queues = [];
        foreach ($keys as $key) {
            $queues[$key] = Queue::size($key);
        }
    }

    public function index(Request $request)
    {
        $can = [
            'read' => auth()->user()->can('read-failed-jobs'),
            'rety' => auth()->user()->can('retry-failed-jobs'),
        ];
        $models = app(config('instant.Models.FailedJob'))->query()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
                ->paginate($request->get('take', 25));
        foreach ($models as $model) {
            $model->exception = Str::limit($model->exception, 100);
        }
        if ('' != $request->get('filters', '')) {
            $models->appends(['filters' => $request->get('filters', '')]);
        }
        if ('' != $request->get('sort', '')) {
            $models->appends(['sort' => $request->get('sort', ''), 'direction' => $request->get('direction', 'asc')]);
        }
        $columns = [
            ['title' => 'ID', 'data' => 'id', 'sortable' => true, 'class' => 'text-left'],
            ['title' => 'Failed At', 'data' => 'failed_at', 'sortable' => true, 'class' => 'text-left'],
            ['title' => 'Queue', 'data' => 'queue', 'sortable' => true, 'class' => 'text-left'],
            ['title' => 'Exception', 'data' => 'exception', 'class' => 'text-left'],
            ['title' => '', 'data' => 'actionsView', 'class' => 'text-center'],
        ];

        return inertia('Admin/Failedjob/Index', compact('columns', 'models'));
    }

    public function show($id)
    {
        $model = app(config('instant.Models.FailedJob'))->query()->with(['creator','modifier'])->findOrFail($id);

        return inertia('Admin/Failedjob/Show', compact('model'));
    }

    public function retry($id)
    {
        $model = app(config('instant.Models.FailedJob'))->query()->with(['creator','modifier'])->findOrFail($id);
        Cache::flush();
        Artisan::call('queue:retry', [
            'id' => $model->uuid,
        ]);
        audit('Retry queue: '.$model->id, [], $model);

        return Redirect::route('failedjob')->with([
            'status' => 'success',
            'flash' => 'Retried Queue.',
        ]);
    }
}
