<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
    }

    public function show(Request $request)
    {
        $model = auth()->user();
        $last_activity = $model->activitylogs()->first();
        $model->last_activity = [
            'datetime' => $last_activity->created_at,
            'message' => $last_activity->message,
            'iplocation' => $last_activity->iplocation,
        ];
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->push('My Profile', route('profile'));
        });
        return view('dashing::admin.profile.show', compact('model'));
    }

    public function edit(Request $request)
    {
        $model = auth()->user();
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->push('Edit My Profile / Change Password', route('profile.edit'));
        });
        return view('dashing::admin.profile.edit', compact('model'));
    }

    public function update(Request $request)
    {
        $model = auth()->user();

        $request->validate([
            'name' => 'required',
            'email' => 'required',
        ]);

        $request->merge([
            'updated_by' => auth()->id(),
        ]);

        $model->update($request->all());

        return response()->json([
            'status' => 'success',
            'flash' => 'Profile Updated.',
            'reload' => false,
            'relist' => false,
            'redirect' => route('profile.edit', [$model->id]),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $model = auth()->user();
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ]);

        $request->merge([
            'password' => bcrypt($request->get('password')),
            'updated_by' => auth()->id(),
        ]);

        $model->update($request->all());

        return response()->json([
            'status' => 'success',
            'flash' => 'Password Updated.',
            'reload' => true,
            'relist' => false,
            'redirect' => false,
        ]);
    }
}
