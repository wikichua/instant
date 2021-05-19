<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NavController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read', 'preview']);
        $this->middleware('can:create-navs')->only(['create', 'store']);
        $this->middleware('can:read-navs')->only(['index', 'read', 'preview']);
        $this->middleware('can:update-navs')->only(['edit', 'update']);
        $this->middleware('can:delete-navs')->only('destroy');
        $this->middleware('can:Migrate-navs')->only('migration');
        if (false == app()->runningInConsole()) {
            \Breadcrumbs::for('home', function ($trail) {
                $trail->push('Nav Listing', route('nav'));
            });
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $models = app(config('instant.Models.Nav'))->query()
                ->with('brand')
                ->checkBrand()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
            ;
            $paginated = $models->paginate($request->get('take', 25));
            foreach ($paginated as $model) {
                $model->actionsView = view('dashing::admin.nav.actions', compact('model'))->render();
                // $model->link = '<a href="'.route_slug(strtolower($model->brand->name).'.page', $model->route_slug, $model->route_params, $model->locale).'" target="_blank">'.$model->name.'</a>';
                $model->link = $model->name;
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
        $getUrl = route('nav');
        $html = [
            ['title' => 'Brand', 'data' => 'brand.name', 'sortable' => false],
            ['title' => 'Link', 'data' => 'link', 'sortable' => false],
            ['title' => 'Group Slug', 'data' => 'group_slug', 'sortable' => true],
            ['title' => 'Locale', 'data' => 'locale', 'sortable' => true],
            ['title' => 'Ordering', 'data' => 'seq', 'sortable' => true],
            ['title' => 'Status', 'data' => 'status_name', 'sortable' => false, 'filterable' => true],
            ['title' => 'Created Date', 'data' => 'created_at', 'sortable' => false, 'filterable' => true],
            ['title' => '', 'data' => 'actionsView'],
        ];

        return view('dashing::admin.nav.index', compact('html', 'getUrl'));
    }

    public function create(Request $request)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Create Nav');
        });

        return view('dashing::admin.nav.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand_id' => 'required',
            'name' => 'required',
            'locale' => 'required',
            'group_slug' => 'required',
            'route_slug' => 'required',
            'status' => 'required',
            'seq' => 'required',
        ]);

        $request->merge([
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $model = app(config('instant.Models.Nav'))->create($request->all());
        sendAlert([
            'brand_id' => $request->input('brand_id', 0),
            'link' => $model->readUrl,
            'message' => 'New Nav Added. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-navs', $request->input('brand_id', 0)),
            'icon' => $model->menu_icon,
        ]);

        return response()->json([
            'status' => 'success',
            'flash' => 'Nav Created.',
            'reload' => false,
            'relist' => false,
            'redirect' => route('nav'),
            // 'redirect' => route('nav.show', [$model->id]),
        ]);
    }

    public function show($id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Show Nav');
        });
        $model = app(config('instant.Models.Nav'))->query()->with(['creator','modifier'])->findOrFail($id);

        return view('dashing::admin.nav.show', compact('model'));
    }

    public function replicate($id)
    {
        $model = app(config('instant.Models.Nav'))->query()->with(['creator','modifier'])->findOrFail($id);
        $newModel = $model->replicate();
        $newModel->push();
        $newModel->locale = null;
        $newModel->save();
        sendAlert([
            'brand_id' => $request->input('brand_id', 0),
            'link' => $newModel->readUrl,
            'message' => 'New Nav Replicated. ('.$newModel->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-navs', $request->input('brand_id', 0)),
            'icon' => $newModel->menu_icon,
        ]);

        return response()->json([
            'status' => 'success',
            'flash' => 'Nav Replicated.',
            'reload' => false,
            'relist' => false,
            'redirect' => route('nav.edit', [$newModel->id]),
        ]);
    }

    public function edit(Request $request, $id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Edit Nav');
        });
        $model = app(config('instant.Models.Nav'))->query()->with(['creator','modifier'])->findOrFail($id);

        return view('dashing::admin.nav.edit', compact('model'));
    }

    public function update(Request $request, $id)
    {
        $model = app(config('instant.Models.Nav'))->query()->with(['creator','modifier'])->findOrFail($id);

        $request->validate([
            'brand_id' => 'required',
            'name' => 'required',
            'locale' => 'required',
            'group_slug' => 'required',
            'route_slug' => 'required',
            'status' => 'required',
            'seq' => 'required',
        ]);

        $request->merge([
            'updated_by' => auth()->id(),
        ]);

        $model->update($request->all());
        sendAlert([
            'brand_id' => $request->input('brand_id', 0),
            'link' => $model->readUrl,
            'message' => 'Nav Updated. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-navs', $request->input('brand_id', 0)),
            'icon' => $model->menu_icon,
        ]);

        return response()->json([
            'status' => 'success',
            'flash' => 'Nav Updated.',
            'reload' => false,
            'relist' => false,
            'redirect' => route('nav.edit', [$model->id]),
            // 'redirect' => route('nav.show', [$model->id]),
        ]);
    }

    public function destroy($id)
    {
        $model = app(config('instant.Models.Nav'))->query()->with(['creator','modifier'])->findOrFail($id);
        sendAlert([
            'brand_id' => $request->input('brand_id', 0),
            'link' => null,
            'message' => 'Nav Deleted. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-navs', $request->input('brand_id', 0)),
            'icon' => $model->menu_icon,
        ]);
        $model->delete();

        return response()->json([
            'status' => 'success',
            'flash' => 'Nav Deleted.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
        ]);
    }

    public function pages($brand_id)
    {
        $pages = [];
        if ('' != $brand_id) {
            $slugs = app(config('instant.Models.Page'))->query()
                ->where('brand_id', $brand_id)
                ->where('status', 'A')
                ->pluck('slug', 'name')
            ;
            foreach ($slugs as $name => $slug) {
                $pages[] = ['value'=>$slug,'text'=>$name];
            }
        }

        return response()->json($pages);
    }

    public function orderable(Request $request, $orderable = '', $brand_id = '')
    {
        if ($request->ajax()) {
            $models = app(config('instant.Models.Nav'))->query()
                ->checkBrand()->orderBy('seq');
            if ('' != $orderable) {
                $models->where('group_slug', $orderable);
            }
            if ('' != $brand_id) {
                $models->where('brand_id', $brand_id);
            }
            $paginated['data'] = $models->take(100)->get();

            return compact('paginated');
        }
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Ordering Nav Position');
        });
        $getUrl = route('nav.orderable', [$orderable, $brand_id]);
        $actUrl = route('nav.orderableUpdate', [$orderable, $brand_id]);
        $html = [
            ['title' => 'ID', 'data' => 'id'],
            ['title' => 'Group Slug', 'data' => 'group_slug'],
            ['title' => 'Name', 'data' => 'name'],
        ];

        return view('dashing::admin.nav.orderable', compact('html', 'getUrl', 'actUrl'));
    }

    public function orderableUpdate(Request $request, $orderable = '', $brand_id = '')
    {
        $newRow = $request->get('newRow');
        $models = app(config('instant.Models.Nav'))->query()->select('id')
            ->checkBrand()->orderByRaw('FIELD(id,'.$newRow.')');
        if ('' != $orderable) {
            $models->where('group_slug', $orderable);
        }
        if ('' != $brand_id) {
            $models->where('brand_id', $brand_id);
        }
        $models = $models->whereIn('id', explode(',', $newRow))->take(100)->get();
        foreach ($models as $seq => $model) {
            $model->seq = $seq + 1;
            $model->saveQuietly();
        }
        audit('Updated Nav: '.$model->id, $request->input(), $model, $model);
        sendAlert([
            'brand_id' => $brand_id,
            'link' => $model->readUrl,
            'message' => 'Nav Position Reordered. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('read-navs', $brand_id),
            'icon' => $model->menu_icon,
        ]);

        return response()->json([
            'status' => 'success',
            'flash' => 'Nav Reordered.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
        ]);
    }

    public function migration(Request $request, $id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Migration Script');
        });
        $model = app(config('instant.Models.Nav'))->query()->with(['creator','modifier'])->findOrFail($id);
        $brandString = $model->brand->name;
        unset($model->id);
        $model->brand_id = '$brand->id';
        $model->created_by = 1;
        $model->updated_by = 1;
        $code = str_replace('\'$brand->id\'', '$brand->id', var_export($model->getAttributes(), 1));
        $string = <<<EOL
            \$brand = app(config('instant.Models.Brand'))->query()->where('name','{$brandString}')->first();
            app(config('instant.Models.Nav'))->query()->create({$code});
            EOL;

        return view('dashing::admin.nav.migration', compact('string'));
    }
}
