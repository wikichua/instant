<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:read-brands')->only(['index', 'read']);
        $this->middleware('can:update-brands')->only(['edit', 'update']);
        inertia()->share('moduleName', 'Brand Management');
    }

    public function index(Request $request)
    {
        $can = [
            'read' => auth()->user()->can('read-brands'),
            'update' => auth()->user()->can('update-brands'),
        ];
        $models = app(config('instant.Models.Brand'))->query()
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
            ['title' => 'Brand Name', 'data' => 'name', 'sortable' => true],
            ['title' => 'Domain', 'data' => 'domain', 'sortable' => true],
            ['title' => 'Published Date', 'data' => 'published_at', 'sortable' => false],
            ['title' => 'Expired Date', 'data' => 'expired_at', 'sortable' => false],
            ['title' => 'Status', 'data' => 'status_name', 'sortable' => false],
            ['title' => '', 'data' => 'actionsView'],
        ];
        $this->shareData();
        return inertia('Admin/Brand/Index', compact('columns', 'models', 'can'));
    }

    public function show($id)
    {
        $model = app(config('instant.Models.Brand'))->query()->with(['creator','modifier'])->findOrFail($id);
        $this->shareData($model);
        return inertia('Admin/Brand/Show', compact('model'));
    }

    public function edit(Request $request, $id)
    {
        $model = app(config('instant.Models.Brand'))->query()->with(['creator','modifier'])->findOrFail($id);
        $this->shareData($model);
        return inertia('Admin/Brand/Edit', compact('model'));
    }

    public function update(Request $request, $id)
    {
        $model = app(config('instant.Models.Brand'))->query()->with(['creator','modifier'])->findOrFail($id);
        $request->validate([
            'domain' => 'required',
            'published_at' => 'required',
            'expired_at' => 'required',
            'status' => 'required',
        ]);

        $request->merge([
            'updated_by' => auth()->id(),
        ]);

        $model->update($request->input());

        \Cache::forget('brand-'.$model->name);
        sendAlert([
            'brand_id' => 0,
            'link' => $model->readUrl,
            'message' => 'Brand Added. ('.$model->slug.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-brands'),
            'icon' => $model->menu_icon,
        ]);

        return Redirect::route('brand.edit', [$model->id])->with([
            'status' => 'success',
            'flash' => 'Brand Updated.',
        ]);
    }
    private function shareData($model = null)
    {
        foreach (settings('brand_status') as $value => $label) {
            $status[] = compact('value', 'label');
        }
        return inertia()->share(compact('status'));
    }
}
