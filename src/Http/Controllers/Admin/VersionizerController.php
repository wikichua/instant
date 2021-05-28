<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class VersionizerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:read-versionizers')->only(['index', 'read']);
        $this->middleware('can:revert-versionizers')->only(['revert']);
        $this->middleware('can:delete-versionizers')->only('destroy');
        if (false == app()->runningInConsole()) {
            \Breadcrumbs::for('home', function ($trail) {
                $trail->push('Versionizers Listing', route('versionizer'));
            });
        }
        inertia()->share('moduleName', 'Versionizer Management');
    }

    public function index(Request $request)
    {
        $can = [
            'read' => auth()->user()->can('read-audit'),
            'revert' => auth()->user()->can('revert-versionizers'),
            'delete' => auth()->user()->can('delete-versionizers'),
        ];
        $models = app(config('instant.Models.Versionizer'))->query()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
                ->paginate($request->get('take', 25));
        foreach ($models as $model) {
            $model->changes = '<pre>'.str_replace('\\', '', json_encode($model->changes)).'</pre>';
        }
        if ('' != $request->get('filters', '')) {
            $paginated->appends(['filters' => $request->get('filters', '')]);
        }
        if ('' != $request->get('sort', '')) {
            $paginated->appends(['sort' => $request->get('sort', ''), 'direction' => $request->get('direction', 'asc')]);
        }
        $columns = [
            ['title' => 'Created At', 'data' => 'created_at', 'sortable' => true, 'class' => 'text-left'],
            ['title' => 'Mode', 'data' => 'mode', 'sortable' => true, 'class' => 'text-left'],
            ['title' => 'Model', 'data' => 'model_class', 'sortable' => true, 'class' => 'text-left'],
            ['title' => 'Model ID', 'data' => 'model_id', 'sortable' => true, 'class' => 'text-left'],
            ['title' => '', 'data' => 'actionsView'],
        ];

        return inertia('Admin/Versionizer/Index', compact('columns', 'models', 'can'));
    }

    public function show($id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Show Versionizer');
        });
        $model = app(config('instant.Models.Versionizer'))->query()->with(['creator','modifier','brand'])->findOrFail($id);

        return inertia('Admin/Versionizer/Show', compact('model'));
    }

    public function revert(Request $request, $id)
    {
        $model = app(config('instant.Models.Versionizer'))->query()->with(['creator','modifier'])->findOrFail($id);
        $revertModel = app($model->model_class)->whereId($model->model_id);
        $checkModel = (clone $revertModel)->first();
        if ($checkModel && 'Updated' == $model->mode) {
            $revertModel->update($model->data);
        } else {
            if (isset($model->data['deleted_at'])) {
                $revertModel->restore();
            } else {
                $revertModel = $revertModel->create($model->data);
                if (isset($model->data['id'])) {
                    $revertModel->id = $model->data['id'];
                    $revertModel->save();
                }
            }
        }
        $model->reverted_at = \Carbon\Carbon::now();
        $model->reverted_by = auth()->user()->id;
        $model->save();
        return Redirect::route('versionizer')->with([
            'status' => 'success',
            'flash' => 'Version Reverted.'
        ]);
    }

    public function destroy($id)
    {
        $model = app(config('instant.Models.Versionizer'))->query()->with(['creator','modifier'])->findOrFail($id);
        $model->delete();

        return Redirect::route('versionizer')->with([
            'status' => 'success',
            'flash' => 'Version Deleted.'
        ]);
    }
}
