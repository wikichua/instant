<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Wikichua\Instant\Repos\Collection;

class CacheController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:read-caches')->only(['index', 'read']);
        $this->middleware('can:delete-caches')->only('destroy');
        if (false == app()->runningInConsole()) {
            \Breadcrumbs::for('home', function ($trail) {
                $trail->push('Caches Listing', route('cache'));
            });
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = array_map(function ($value) {
                return json_decode($value);
            }, cache()->get('LogKeys'));
            $models = (new Collection(array_values($items)));
            if ('' != $request->get('filters', '')) {
                parse_str(json_decode($request->get('filters', ''), 1)['filter'], $filters);
                if (isset($filters['key']) && $filters['key'] != '') {
                    $models = $models->filter(function ($value) use ($filters) {
                        if (\Str::contains($value->key, $filters['key'])) {
                            return $value;
                        }
                    });
                }
                if (isset($filters['tags']) && is_array($filters['tags']) && count($filters['tags'])) {
                    $models = $models->filter(function ($value) use ($filters) {
                        if (count(array_intersect($value->tags, $filters['tags']))) {
                            return $value;
                        }
                    });
                }
            }
            $paginated = $models->paginate($request->get('take', 25));
            foreach ($paginated as $model) {
                $model->id = md5($model->key);
                $model->value = 'censored';
                $model->actionsView = view('dashing::admin.cache.actions', compact('model'))->render();
                $model->tags = '<code>'.json_encode($model->tags, 1).'</code>';
            }
            if ('' != $request->get('filters', '')) {
                $paginated->appends(['filters' => $request->get('filters', '')]);
            }
            $links = $paginated->onEachSide(5)->links()->render();
            $currentUrl = $request->fullUrl();
            return compact('paginated', 'links', 'currentUrl');
        }
        $getUrl = route('cache');
        $html = [
            ['title' => 'Key', 'data' => 'key', 'sortable' => true],
            ['title' => 'Tags', 'data' => 'tags', 'sortable' => true],
            ['title' => 'Seconds', 'data' => 'seconds', 'sortable' => true],
            ['title' => '', 'data' => 'actionsView'],
        ];

        return view('dashing::admin.cache.index', compact('html', 'getUrl'));
    }

    public function show($id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Show Cache');
        });
        $model = json_decode(cache()->get('LogKeys')[$id]);
        if (count($model->tags)) {
            $model->value = cache()->tags($model->tags)->get($model->key);
        } else {
            $model->value = cache()->get($model->key);
        }
        return view('dashing::admin.cache.show', compact('model'));
    }

    public function destroy($id)
    {
        $cache = json_decode(cache()->get('LogKeys')[$id]);
        if (count($cache->tags)) {
            cache()->tags($cache->tags)->forget($cache->key);
        } else {
            cache()->forget($cache->key);
        }
        return response()->json([
            'status' => 'success',
            'flash' => 'Cache Deleted.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
        ]);
    }
}
