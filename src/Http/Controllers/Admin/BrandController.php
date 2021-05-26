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
        if (false == app()->runningInConsole()) {
            \Breadcrumbs::for('home', function ($trail) {
                $trail->push('Brand Listing', route('brand'));
            });
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $models = app(config('instant.Models.Brand'))->query()
                ->checkBrand()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
            ;
            $paginated = $models->paginate($request->get('take', 25));
            foreach ($paginated as $model) {
                $model->actionsView = view('dashing::admin.brand.actions', compact('model'))->render();
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
        $getUrl = route('brand');
        $html = [
            ['title' => 'Brand Name', 'data' => 'name', 'sortable' => true],
            ['title' => 'Domain', 'data' => 'domain', 'sortable' => true],
            ['title' => 'Published Date', 'data' => 'published_at', 'sortable' => false],
            ['title' => 'Expired Date', 'data' => 'expired_at', 'sortable' => false],
            ['title' => 'Status', 'data' => 'status_name', 'sortable' => false],
            ['title' => '', 'data' => 'actionsView'],
        ];

        return view('dashing::admin.brand.index', compact('html', 'getUrl'));
    }

    public function show($id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Show Brand');
        });
        $model = app(config('instant.Models.Brand'))->query()->with(['creator','modifier'])->findOrFail($id);

        return view('dashing::admin.brand.show', compact('model'));
    }

    public function edit(Request $request, $id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Edit Brand');
        });
        $model = app(config('instant.Models.Brand'))->query()->with(['creator','modifier'])->findOrFail($id);

        return view('dashing::admin.brand.edit', compact('model'));
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

        return response()->json([
            'status' => 'success',
            'flash' => 'Brand Updated.',
            'reload' => false,
            'relist' => false,
            'redirect' => route('brand.edit', [$model->id]),
        ]);
    }
}
