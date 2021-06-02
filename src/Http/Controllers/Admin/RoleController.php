<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:create-roles')->only(['create', 'store']);
        $this->middleware('can:read-roles')->only(['index', 'read']);
        $this->middleware('can:update-roles')->only(['edit', 'update']);
        $this->middleware('can:delete-roles')->only('destroy');

        $this->middleware('reauth_admin')->only(['edit', 'destroy']);
        inertia()->share('moduleName', 'Role Management');
    }

    public function index(Request $request)
    {
        $can = [
            'create' => auth()->user()->can('create-roles'),
            'read' => auth()->user()->can('read-roles'),
            'update' => auth()->user()->can('update-roles'),
            'delete' => auth()->user()->can('delete-roles'),
        ];
        $models = app(config('instant.Models.Role'))->query()
                ->checkBrand()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
                ->paginate($request->get('take', 25));
        foreach ($models as $model) {
            $model->permissions = implode(', ', $model->permissions()->pluck('name')->toArray());
        }
        if ('' != $request->get('filters', '')) {
            $models->appends(['filters' => $request->get('filters', '')]);
        }
        if ('' != $request->get('sort', '')) {
            $models->appends(['sort' => $request->get('sort', ''), 'direction' => $request->get('direction', 'asc')]);
        }
        $columns = [
            ['title' => 'Name', 'data' => 'name', 'sortable' => true, 'class' => 'text-left'],
            ['title' => 'Is Admin', 'data' => 'isAdmin', 'class' => 'text-left'],
            ['title' => 'Permissions', 'data' => 'permissions', 'class' => 'text-left'],
            ['title' => '', 'data' => 'actionsView', 'class' => 'text-center'],
        ];
        return inertia('Admin/Role/Index', compact('models', 'columns', 'can'));
    }

    public function create(Request $request)
    {
        $this->shareData();
        return inertia('Admin/Role/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'admin' => 'required',
            'permissions' => 'required',
        ]);

        $request->merge([
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'permissions' => array_keys($request->get('permissions', [])),
        ]);

        $model = app(config('instant.Models.Role'))->create($request->all());
        $model->permissions()->sync($request->get('permissions'));

        sendAlert([
            'brand_id' => 0,
            'link' => $model->readUrl,
            'message' => 'New Role Added. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-roles'),
            'icon' => $model->menu_icon,
        ]);
        return Redirect::route('role')->with([
            'status' => 'success',
            'flash' => 'Role Created.',
        ]);
    }

    public function show($id)
    {
        $model = app(config('instant.Models.Role'))->query()->with(['creator','modifier'])->findOrFail($id);
        $this->shareData($model);
        return inertia('Admin/Role/Show', compact('model'));
    }

    public function edit(Request $request, $id)
    {
        $model = app(config('instant.Models.Role'))->query()->with(['creator','modifier'])->findOrFail($id);
        $this->shareData($model);
        return inertia('Admin/Role/Edit', compact('model'));
    }

    public function update(Request $request, $id)
    {
        $model = app(config('instant.Models.Role'))->query()->with(['creator','modifier'])->findOrFail($id);

        $request->validate([
            'name' => 'required',
            'admin' => 'required',
        ]);
        $permissions = collect($request->get('permissions', []))->filter(function ($value, $key) {
            return (bool) $value != false;
        })->toArray();
        $request->merge([
            'updated_by' => auth()->id(),
            'permissions' => array_keys($permissions),
        ]);

        $model->update($request->all());
        $model->permissions()->sync($request->get('permissions'));

        sendAlert([
            'brand_id' => 0,
            'link' => $model->readUrl,
            'message' => 'Role Updated. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-roles'),
            'icon' => $model->menu_icon,
        ]);
        return Redirect::route('role.edit', [$model->id])->with([
            'status' => 'success',
            'flash' => 'Role Updated.',
        ]);
    }

    public function destroy($id)
    {
        $model = app(config('instant.Models.Role'))->query()->with(['creator','modifier'])->findOrFail($id);
        $model->permissions()->sync([]);
        sendAlert([
            'brand_id' => 0,
            'link' => null,
            'message' => 'New Role Deleted. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-roles'),
            'icon' => $model->menu_icon,
        ]);
        $model->delete();

        return Redirect::route('role')->with([
            'status' => 'success',
            'flash' => 'Role Deleted.',
        ]);
    }

    private function getGroupPermissions()
    {
        $permissions = app(config('instant.Models.Permission'))->select(['id', 'name', 'group'])->get()->groupBy('group');
        $group_permissions = [];
        foreach ($permissions as $group => $perms) {
            foreach ($perms as $perm) {
                $group_permissions[$group][$perm->id] = [
                    'value' => $perm->id,
                    'label' => $perm->name,
                ];
            }
        }
        return $group_permissions;
    }

    private function getSelectedPermissions($model)
    {
        return $model->permissions()->select(['permissions.id', \DB::Raw('\'true\' as `selected`')])->pluck('selected', 'id')->toArray();
    }

    protected function shareData($model = null)
    {
        $selected_permissions = $model ? $this->getSelectedPermissions($model) : [];
        $selected_permissions_list = $model ? $model->permissions()->pluck('name')->toArray() : [];
        $group_permissions = $this->getGroupPermissions();
        return inertia()->share(compact('selected_permissions', 'group_permissions', 'selected_permissions_list'));
    }
}
