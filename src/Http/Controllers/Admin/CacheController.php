<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Wikichua\Instant\Repos\Collection;

class CacheController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:read-caches')->only(['index', 'read']);
        $this->middleware('can:delete-caches')->only('destroy');
        inertia()->share(['moduleName' => 'Cache Management']);
    }

    public function index(Request $request)
    {
        $can = [
            'read' => auth()->user()->can('read-caches'),
            'delete' => auth()->user()->can('delete-caches'),
        ];
        $items = array_map(function ($value) {
            return json_decode($value);
        }, cache()->get('LogKeys', []));
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
        $models = $models->paginate($request->get('take', 25));
        foreach ($models as $model) {
            $model->id = md5($model->key);
            $model->value = 'censored';
            $model->tags = '<code>'.json_encode($model->tags, 1).'</code>';
        }
        if ('' != $request->get('filters', '')) {
            $models->appends(['filters' => $request->get('filters', '')]);
        }
        $columns = [
            ['title' => 'Key', 'data' => 'key', 'sortable' => true],
            ['title' => 'Tags', 'data' => 'tags', 'sortable' => true],
            ['title' => 'Seconds', 'data' => 'seconds', 'sortable' => true],
            ['title' => '', 'data' => 'actionsView'],
        ];

        return inertia('Admin/Cache/Index', compact('columns', 'models', 'can'));
    }

    public function show($id)
    {
        $model = json_decode(cache()->get('LogKeys')[$id]);
        if (count($model->tags)) {
            $model->value = cache()->tags($model->tags)->get($model->key);
        } else {
            $model->value = cache()->get($model->key);
        }
        return inertia('Admin/Cache/Show', compact('model'));
    }

    public function destroy($id)
    {
        $cache = json_decode(cache()->get('LogKeys')[$id]);
        if (count($cache->tags)) {
            cache()->tags($cache->tags)->forget($cache->key);
        } else {
            cache()->forget($cache->key);
        }
        return Redirect::route('cache')->with([
            'status' => 'success',
            'flash' => 'Cache Deleted.'
        ]);
    }
}
