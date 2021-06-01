<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PusherController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read', 'preview']);
        $this->middleware('can:create-pushers')->only(['create', 'store']);
        $this->middleware('can:read-pushers')->only(['index', 'read', 'preview']);
        $this->middleware('can:update-pushers')->only(['edit', 'update']);
        $this->middleware('can:delete-pushers')->only('destroy');
        $this->middleware('can:pusher-pushers')->only('push');

        $this->middleware('reauth_admin')->only(['edit', 'destroy']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $models = app(config('instant.Models.Pusher'))->query()
                ->with('brand')
                ->checkBrand()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
            ;
            $paginated = $models->paginate($request->get('take', 25));
            foreach ($paginated as $model) {
                $model->actionsView = view('dashing::admin.pusher.actions', compact('model'))->render();
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
        $getUrl = route('pusher');
        $html = [
            ['title' => 'Brand', 'data' => 'brand.name', 'sortable' => true],
            ['title' => 'Title', 'data' => 'title', 'sortable' => true],
            ['title' => 'Locale', 'data' => 'locale', 'sortable' => true],
            ['title' => 'Event', 'data' => 'event_name', 'sortable' => true],
            ['title' => 'Scheduled Date', 'data' => 'scheduled_at', 'sortable' => false],
            ['title' => 'Status', 'data' => 'status_name', 'sortable' => false],
            ['title' => 'Created Date', 'data' => 'created_at', 'sortable' => false],
            ['title' => '', 'data' => 'actionsView'],
        ];

        return view('dashing::admin.pusher.index', compact('html', 'getUrl'));
    }

    public function create(Request $request)
    {
        return view('dashing::admin.pusher.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'locale' => 'required',
            'event' => 'required',
            'title' => 'required',
            'message' => 'required',
            'timeout' => 'required',
            'scheduled_at' => 'required',
            'status' => 'required',
        ]);

        $request->merge([
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $model = app(config('instant.Models.Pusher'))->create($request->all());

        sendAlert([
            'brand_id' => 0,
            'link' => $model->readUrl,
            'message' => 'New Pusher Added. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-pushers'),
            'icon' => $model->menu_icon,
        ]);

        return response()->json([
            'status' => 'success',
            'flash' => 'Pusher Created.',
            'reload' => false,
            'relist' => false,
            'redirect' => route('pusher'),
            // 'redirect' => route('pusher.show', [$model->id]),
        ]);
    }

    public function show($id)
    {
        $model = app(config('instant.Models.Pusher'))->query()->with(['creator','modifier'])->findOrFail($id);

        return view('dashing::admin.pusher.show', compact('model'));
    }

    public function edit(Request $request, $id)
    {
        $model = app(config('instant.Models.Pusher'))->query()->with(['creator','modifier'])->findOrFail($id);

        return view('dashing::admin.pusher.edit', compact('model'));
    }

    public function update(Request $request, $id)
    {
        $model = app(config('instant.Models.Pusher'))->query()->with(['creator','modifier'])->findOrFail($id);

        $request->validate([
            'locale' => 'required',
            'event' => 'required',
            'title' => 'required',
            'message' => 'required',
            'timeout' => 'required',
            'scheduled_at' => 'required',
            'status' => 'required',
        ]);

        $request->merge([
            'updated_by' => auth()->id(),
        ]);

        $model->update($request->all());

        sendAlert([
            'brand_id' => 0,
            'link' => $model->readUrl,
            'message' => 'Pusher Updated. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-pushers'),
            'icon' => $model->menu_icon,
        ]);

        return response()->json([
            'status' => 'success',
            'flash' => 'Pusher Updated.',
            'reload' => false,
            'relist' => false,
            'redirect' => route('pusher.edit', [$model->id]),
            // 'redirect' => route('pusher.show', [$model->id]),
        ]);
    }

    public function destroy($id)
    {
        $model = app(config('instant.Models.Pusher'))->query()->with(['creator','modifier'])->findOrFail($id);
        sendAlert([
            'brand_id' => 0,
            'link' => null,
            'message' => 'Pusher Deleted. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-pushers'),
            'icon' => $model->menu_icon,
        ]);
        $model->delete();

        return response()->json([
            'status' => 'success',
            'flash' => 'Pusher Deleted.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
        ]);
    }

    public function push($id)
    {
        $model = app(config('instant.Models.Pusher'))->query()->with(['creator','modifier'])->findOrFail($id);
        $channel = '';
        if ($model->brand) {
            $channel = strtolower($model->brand->name);
        }
        pushered($model->toArray(), $channel, $model->event, $model->locale);
        sendAlert([
            'brand_id' => 0,
            'link' => $model->readUrl,
            'message' => 'Pusher Executed. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-pushers'),
            'icon' => $model->menu_icon,
        ]);

        return response()->json([
            'status' => 'success',
            'flash' => 'Pusher Message Pushed.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
        ]);
    }
}
