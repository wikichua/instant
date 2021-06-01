<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:create-settings')->only(['create', 'store']);
        $this->middleware('can:read-settings')->only(['index', 'read']);
        $this->middleware('can:update-settings')->only(['edit', 'update']);
        $this->middleware('can:delete-settings')->only('destroy');

        $this->middleware('reauth_admin')->only(['edit', 'destroy']);
        inertia()->share('moduleName', 'Setting Management');
    }

    public function index(Request $request)
    {
        $can = [
            'create' => auth()->user()->can('create-settings'),
            'read' => auth()->user()->can('read-settings'),
            'update' => auth()->user()->can('update-settings'),
            'delete' => auth()->user()->can('delete-settings'),
        ];
        $models = app(config('instant.Models.Setting'))->query()
                ->checkBrand()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
                ->paginate($request->get('take', 25));
        foreach ($models as $model) {
            $model->valueString = is_array($model->value) ? implode('<br>', $model->value) : $model->value;
        }
        if ('' != $request->get('filters', '')) {
            $models->appends(['filters' => $request->get('filters', '')]);
        }
        if ('' != $request->get('sort', '')) {
            $models->appends(['sort' => $request->get('sort', ''), 'direction' => $request->get('direction', 'asc')]);
        }

        $columns = [
            ['title' => 'Key', 'data' => 'key', 'sortable' => true, 'class' => 'text-left'],
            ['title' => 'Value', 'data' => 'valueString', 'sortable' => true, 'class' => 'text-left'],
            ['title' => '', 'data' => 'actionsView', 'class' => 'text-center'],
        ];

        return inertia('Admin/Setting/Index', compact('columns', 'models', 'can'));
    }

    public function create(Request $request)
    {
        return inertia('Admin/Setting/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required',
        ]);

        if (true == $request->get('multipleTypes', false)) {
            $values = [];
            foreach ($request->input('values') as $vals) {
                $values[$vals['key']] = $vals['value'];
            }
            $request->merge(['value' => $values]);
        }

        if (false == $request->has('protected')) {
            $request->merge(['protected' => 0]);
        }

        $model = app(config('instant.Models.Setting'))->create($request->all());

        cache()->forget('setting-'.$model->key);

        sendAlert([
            'brand_id' => 0,
            'link' => null,
            'message' => 'New Setting Added. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-settings'),
            'icon' => $model->menu_icon,
        ]);

        return Redirect::route('setting')->with([
            'status' => 'success',
            'flash' => 'Setting Created.',
        ]);
    }

    public function show($id)
    {
        $model = app(config('instant.Models.Setting'))->query()->with(['creator','modifier'])->findOrFail($id);
        $value = $model->value;
        $values = ['key'=>'','value'=>''];
        $model->multipleTypes = false;
        if (is_array($model->value)) {
            $model->multipleTypes = true;
            $value = '';
            $values = [];
            foreach ($model->value as $key => $val) {
                $values[] = ['key' => $key, 'value' => $val];
            }
        }
        return inertia('Admin/Setting/Show', compact('model', 'value', 'values'));
    }

    public function edit(Request $request, $id)
    {
        $model = app(config('instant.Models.Setting'))->query()->with(['creator','modifier'])->findOrFail($id);
        $value = $model->value;
        $values = ['key'=>'','value'=>''];
        $model->multipleTypes = false;
        if (is_array($model->value)) {
            $model->multipleTypes = true;
            $value = '';
            $values = [];
            foreach ($model->value as $key => $val) {
                $values[] = ['key' => $key, 'value' => $val];
            }
        }
        return inertia('Admin/Setting/Edit', compact('model', 'value', 'values'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'key' => 'required',
        ]);

        $model = app(config('instant.Models.Setting'))->query()->with(['creator','modifier'])->findOrFail($id);

        if (true == $request->get('multipleTypes', false)) {
            $values = [];
            foreach ($request->input('values') as $vals) {
                $values[$vals['key']] = $vals['value'];
            }
            $request->merge(['value' => $values]);
        }

        if (false == $request->has('protected')) {
            $request->merge(['protected' => 0]);
        }

        $model->update($request->all());

        cache()->forget('setting-'.$model->key);

        sendAlert([
            'brand_id' => 0,
            'link' => null,
            'message' => 'Setting Updated. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-settings'),
            'icon' => $model->menu_icon,
        ]);

        return Redirect::route('setting.edit', [$id])->with([
            'status' => 'success',
            'flash' => 'Setting Updated.',
        ]);
    }

    public function destroy($id)
    {
        $model = app(config('instant.Models.Setting'))->query()->with(['creator','modifier'])->findOrFail($id);
        sendAlert([
            'brand_id' => 0,
            'link' => null,
            'message' => 'Setting Deleted. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-settings'),
            'icon' => $model->menu_icon,
        ]);
        $model->delete();

        return Redirect::route('setting')->with([
            'status' => 'success',
            'flash' => 'Setting Deleted.',
        ]);
    }
}
