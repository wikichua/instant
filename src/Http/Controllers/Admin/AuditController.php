<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuditController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:read-audit')->only(['index', 'read']);
        inertia()->share('moduleName', 'Audit Management');
    }

    public function index(Request $request)
    {
        $can = [
            'read' => auth()->user()->can('read-audit'),
        ];
        $models = app(config('instant.Models.Audit'))->query()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
                ->with(['user'])
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
            ['title' => 'Created At', 'data' => 'created_at', 'sortable' => true, 'class' => 'text-left'],
            ['title' => 'User', 'data' => 'user.name', 'sortable' => true, 'class' => 'text-left'],
            ['title' => 'Model ID', 'data' => 'model_id', 'sortable' => true, 'class' => 'text-left'],
            ['title' => 'Model', 'data' => 'model_class', 'sortable' => true, 'class' => 'text-left'],
            ['title' => 'Message', 'data' => 'message', 'class' => 'text-left'],
            ['title' => '', 'data' => 'actionsView', 'class' => 'text-center'],
        ];
        return inertia('Admin/Audit/Index', compact('columns', 'can', 'models'));
    }

    public function show($id)
    {
        $model = app(config('instant.Models.Audit'))->query()->with(['user','brand','creator','modifier'])->findOrFail($id);

        return inertia('Admin/Audit/Show', compact('model'));
    }

    public function setRead($id)
    {
        $model = app(config('instant.Models.Alert'))->query()->with(['creator','modifier'])->findOrFail($id);
        $model->update(['status' => 'r']);

        return $model->link;
    }
}
