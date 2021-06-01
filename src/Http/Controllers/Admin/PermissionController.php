<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:create-permissions')->only(['create', 'store']);
        $this->middleware('can:read-permissions')->only(['index', 'read']);
        $this->middleware('can:update-permissions')->only(['edit', 'update']);
        $this->middleware('can:delete-permissions')->only('destroy');

        $this->middleware('reauth_admin')->only(['edit', 'destroy']);
        inertia()->share('moduleName', 'Permission Management');
    }

    public function index(Request $request)
    {
        $can = [
            'create' => auth()->user()->can('create-permissions'),
            'read' => auth()->user()->can('read-permissions'),
            'update' => auth()->user()->can('update-permissions'),
            'delete' => auth()->user()->can('delete-permissions'),
        ];
        $models = app(config('instant.Models.Permission'))->query()
                ->checkBrand()
                ->select([
                    'group',
                    \DB::raw('min(`id`) as id'),
                    \DB::raw('GROUP_CONCAT(" ",`name`) as "name"'),
                ])->groupby('group')
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
            ['title' => 'Group', 'data' => 'group', 'sortable' => true, 'class' => 'text-left'],
            ['title' => 'Name', 'data' => 'name', 'sortable' => true, 'class' => 'text-left'],
            ['title' => '', 'data' => 'actionsView', 'class' => 'text-center'],
        ];
        $groups = app(config('instant.Models.Permission'))->query()->select([\DB::raw('`group` as label'),\DB::raw('`group` as value')])->groupBy('label')->get()->toArray();
        return inertia('Admin/Permission/Index', compact('columns', 'models', 'groups', 'can'));
    }

    public function create(Request $request)
    {
        return inertia('Admin/Permission/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'at_least:1',
                'all_filled'
            ],
            'group' => 'required',
        ]);
        foreach ($request->input('name') as $value) {
            $request->merge([
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'name' => str_slug($value),
            ]);

            $model = app(config('instant.Models.Permission'))->create($request->all());
        }

        sendAlert([
            'brand_id' => 0,
            'link' => $model->readUrl,
            'message' => 'New Permission Added. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => 0,
            'icon' => $model->menu_icon,
        ]);

        // return inertia('Admin/Permission/Create', [
        //     'status' => 'success',
        //     'flash' => 'Permission Created.',
        // ]);
        return Redirect::route('permission')->with([
            'status' => 'success',
            'flash' => 'Permission Created.',
        ]);
    }

    public function show($id)
    {
        $model = app(config('instant.Models.Permission'))->query()->with(['creator','modifier'])->findOrFail($id);
        $permissions = app(config('instant.Models.Permission'))->query()->where('group', $model->group)->pluck('name', 'id');
        return inertia('Admin/Permission/Show', compact('model', 'permissions'));
    }

    public function edit(Request $request, $id)
    {
        $model = app(config('instant.Models.Permission'))->query()->with(['creator','modifier'])->findOrFail($id);
        $permissions = app(config('instant.Models.Permission'))->query()->where('group', $model->group)->pluck('name')->toArray();
        return inertia('Admin/Permission/Edit', compact('model', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'at_least:1',
                'all_filled'
            ],
            'group' => 'required',
        ]);
        $model = app(config('instant.Models.Permission'))->find($id);
        $permissions = app(config('instant.Models.Permission'))->where('group', $model->group)->pluck('name')->toArray();
        $group = $request->input('group');
        $input_permissions = collect($request->input('name'))->toArray();
        // delete
        $deleted_permissions = array_diff($permissions, $input_permissions);
        app(config('instant.Models.Permission'))->where('group', $model->group)->whereIn('name', $deleted_permissions)->delete();
        // new
        $new_permissions = array_diff($input_permissions, $permissions);
        foreach ($new_permissions as $permission) {
            app(config('instant.Models.Permission'))->create([
                'group' => $group,
                'name' => str_slug(strtolower($permission)),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }
        // update
        $update_permissions = array_diff($input_permissions, array_merge($deleted_permissions, $new_permissions));
        foreach ($update_permissions as $permission) {
            app(config('instant.Models.Permission'))->update([
                'group' => $group,
                'name' => str_slug(strtolower($permission)),
                'updated_by' => auth()->id(),
            ]);
        }

        sendAlert([
            'brand_id' => 0,
            'link' => $model->readUrl,
            'message' => 'Permission Updated. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => 0,
            'icon' => $model->menu_icon,
        ]);

        return Redirect::route('permission.edit', [$model->id])->with([
            'status' => 'success',
            'flash' => 'Permission Updated.',
        ]);
    }

    public function destroy($id)
    {
        $model = app(config('instant.Models.Permission'))->query()->with(['creator','modifier'])->findOrFail($id);
        sendAlert([
            'brand_id' => 0,
            'link' => null,
            'message' => 'Permission Deleted. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => 0,
            'icon' => $model->menu_icon,
        ]);
        app(config('instant.Models.Permission'))->where('group', $model->group)->delete();

        return Redirect::route('permission')->with([
            'status' => 'success',
            'flash' => 'Permission Deleted.',
        ]);
    }
}
