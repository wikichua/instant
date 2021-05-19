<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:create-users')->only(['create', 'store']);
        $this->middleware('can:read-users')->only(['index', 'read']);
        $this->middleware('can:update-users')->only(['edit', 'update']);
        $this->middleware('can:update-users-password')->only(['editPassword', 'updatePassword']);
        $this->middleware('can:delete-users')->only('destroy');

        $this->middleware('reauth_admin')->only(['edit', 'destroy', 'editPassword']);
        if (false == app()->runningInConsole()) {
            \Breadcrumbs::for('home', function ($trail) {
                $trail->push('User Listing', route('user'));
            });
        }
        inertia()->share('moduleName', 'User Management');
    }

    public function index(Request $request)
    {
        $can = [
            'create' => auth()->user()->can('create-users'),
            'impersonate' => auth()->user()->can('impersonate-users'),
            'readPersonalAccessToken' => auth()->user()->can('read-personal-access-token'),
            'read' => auth()->user()->can('read-users'),
            'update' => auth()->user()->can('update-users'),
            'delete' => auth()->user()->can('delete-users'),
            'updatePassword' => auth()->user()->can('update-users-password'),
        ];
        $models = app(config('instant.Models.User'))->query()
                ->where('id', '!=', 1)
                ->checkBrand()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', '')) // be treated as default sorting rules
                ->with('roles')
                ->paginate($request->get('take', 25));
        foreach ($models as $model) {
            $model->can = [
                'onlyDeleteOtherUser' => $model->id != auth()->user()->id,
            ];
            $model->brand_name = $model->brand->name ?? '';
        }
        if ('' != $request->get('filters', '')) {
            $models->appends(['filters' => $request->get('filters', '')]);
        }
        if ('' != $request->get('sort', '')) {
            $models->appends(['sort' => $request->get('sort', ''), 'direction' => $request->get('direction', 'asc')]);
        }
        $columns = [
            ['title' => 'Name', 'data' => 'name', 'sortable' => true],
            ['title' => 'Email', 'data' => 'email', 'sortable' => true],
            ['title' => 'Type', 'data' => 'type', 'sortable' => true],
            ['title' => 'Brand', 'data' => 'brand_name', 'sortable' => false],
            ['title' => 'Timezone', 'data' => 'timezone', 'sortable' => true],
            ['title' => 'Roles', 'data' => 'roles_string'],
            ['title' => '', 'data' => 'actionsView'],
        ];
        return inertia('Admin/User/Index', compact('columns', 'models', 'can'));
    }

    public function create(Request $request)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Create User');
        });
        return inertia('Admin/User/Create', $this->getPropsData());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'type' => 'required',
            'timezone' => 'required',
            'password_confirmation' => 'required',
            'password' => ['required', 'confirmed'],
        ]);

        $request->merge([
            'password' => bcrypt($request->get('password')),
            'roles' => array_keys($request->get('roles', [])),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);
        $model = app(config('instant.Models.User'))->create($request->input());
        $model->roles()->sync($request->get('roles'));

        sendAlert([
            'brand_id' => $request->input('brand_id', 0),
            'link' => null,
            'message' => 'New User Added. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-users', $request->input('brand_id', 0)),
            'icon' => $model->menu_icon,
        ]);

        return Redirect::route('user')->with([
            'status'=>'success',
            'flash' => 'User Created.',
        ]);
    }

    public function show($id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Show User');
        });
        $model = app(config('instant.Models.User'))->query()->with(['creator','modifier'])->findOrFail($id);
        $last_activity = $model->activitylogs()->first();
        $model->last_activity = [
            'datetime' => $last_activity->created_at ?? '',
            'message' => $last_activity->message ?? '',
            'iplocation' => $last_activity->iplocation ?? '',
        ];
        return inertia('Admin/User/Show', compact('model') + $this->getPropsData($model));
    }

    public function edit(Request $request, $id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Edit User');
        });
        $model = app(config('instant.Models.User'))->query()->with(['creator','modifier'])->findOrFail($id);
        return inertia('Admin/User/Edit', compact('model') + $this->getPropsData($model));
    }

    public function update(Request $request, $id)
    {
        $model = app(config('instant.Models.User'))->query()->with(['creator','modifier'])->findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'timezone' => 'required',
            'type' => 'required',
        ]);
        $roles = collect($request->get('roles', []))->filter(function ($value, $key) {
            return (bool) $value != false;
        })->toArray();
        $request->merge([
            'roles' => array_keys($roles),
            'updated_by' => auth()->id(),
        ]);

        $model->update($request->input());
        $model->roles()->sync($request->get('roles'));

        sendAlert([
            'brand_id' => $request->input('brand_id', 0),
            'link' => null,
            'message' => 'User Updated. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-users', $request->input('brand_id', 0)),
            'icon' => $model->menu_icon,
        ]);

        return Redirect::route('user.edit', [$model->id])->with([
            'status'=>'success',
            'flash' => 'User Updated.',
        ]);
    }

    public function editPassword(Request $request, $id)
    {
        $model = app(config('instant.Models.User'))->query()->with(['creator','modifier'])->findOrFail($id);
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Edit User Password');
        });

        return inertia('Admin/User/EditPassword', compact('model'));
    }

    public function updatePassword(Request $request, $id)
    {
        $model = app(config('instant.Models.User'))->query()->with(['creator','modifier'])->findOrFail($id);
        $request->validate([
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ]);

        $request->merge([
            'password' => bcrypt($request->get('password')),
            'updated_by' => auth()->id(),
        ]);

        $model->update($request->all());

        return Redirect::route('user.editPassword', [$model->id])->with([
            'status'=>'success',
            'flash' => 'User Updated.',
        ]);
    }

    public function destroy($id)
    {
        $model = app(config('instant.Models.User'))->query()->with(['creator','modifier'])->findOrFail($id);
        $model->roles()->sync([]);
        sendAlert([
            'brand_id' => $request->input('brand_id', 0),
            'link' => null,
            'message' => 'User Deleted. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-users', $request->input('brand_id', 0)),
            'icon' => $model->menu_icon,
        ]);
        $model->delete();

        return Redirect::route('user')->with([
            'status'=>'success',
            'flash' => 'User Deleted.',
        ]);
    }

    private function getPropsData($model = null)
    {
        $user_roles = null;
        if ($model) {
            $user_roles = $model->roles()->select(['roles.id', \DB::Raw('\'true\' as `selected`')])->pluck('selected', 'id')->toArray();
        }
        $roles = app(config('instant.Models.Role'))->select(['name as label','id as value'])->orderBy('label')->get()->toArray();
        $brands = [['label' => 'System', 'value' => 0]] + app(config('instant.Models.Brand'))->select(['name as label','id as value'])->get()->toArray();
        $timezones = timezones();
        $user_types = settings('user_types');
        return compact('roles', 'brands', 'timezones', 'user_types', 'user_roles');
    }
}
