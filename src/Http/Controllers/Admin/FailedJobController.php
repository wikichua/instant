<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class FailedJobController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:read-failed-jobs')->only(['index', 'read']);
        $this->middleware('can:retry-failed-jobs')->only(['retry']);
        if (false == app()->runningInConsole()) {
            \Breadcrumbs::for('home', function ($trail) {
                $trail->push('Failed Jobs Listing', route('failedjob'));
            });
        }
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
        if ($request->ajax()) {
            $models = app(config('instant.Models.FailedJob'))->query()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
            ;
            $paginated = $models->paginate($request->get('take', 25));
            foreach ($paginated as $model) {
                $model->actionsView = view('dashing::admin.failedjob.actions', compact('model'))->render();
                $model->exception = Str::limit($model->exception, 100);
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
        $getUrl = route('failedjob');
        $html = [
            ['title' => 'ID', 'data' => 'id', 'sortable' => true],
            ['title' => 'Failed At', 'data' => 'failed_at', 'sortable' => true],
            ['title' => 'Queue', 'data' => 'queue', 'sortable' => true],
            ['title' => 'Exception', 'data' => 'exception'],
            ['title' => '', 'data' => 'actionsView'],
        ];

        return view('dashing::admin.failedjob.index', compact('html', 'getUrl'));
    }

    public function show($id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Show Failed Job');
        });
        $model = app(config('instant.Models.FailedJob'))->query()->with(['creator','modifier'])->findOrFail($id);

        return view('dashing::admin.failedjob.show', compact('model'));
    }

    public function retry($id)
    {
        $model = app(config('instant.Models.FailedJob'))->query()->with(['creator','modifier'])->findOrFail($id);
        Cache::flush();
        Artisan::call('queue:retry', [
            'id' => $model->uuid,
        ]);
        audit('Retry queue: '.$model->id, [], $model);

        return response()->json([
            'status' => 'success',
            'flash' => 'Retried Queue.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
        ]);
    }
}
