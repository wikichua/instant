<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read', 'preview']);
        $this->middleware('can:upload-files')->only(['upload']);
        $this->middleware('can:rename-files')->only(['rename']);
        $this->middleware('can:delete-files')->only('destroy');
        $this->middleware('can:copy-files')->only('duplicate');

        $this->middleware('can:create-folders')->only(['make']);
        $this->middleware('can:rename-folders')->only(['change']);
        $this->middleware('can:delete-folders')->only('remove');
        $this->middleware('can:copy-folders')->only('clone');

        // $brand_id = 1;
        $brand_id = auth()->user()->brand_id ?? 0;
        if ($brand_id) {
            $this->storagePath = storage_path('app/public/brand/'.$brand_id);
            File::ensureDirectoryExists($this->storagePath);
        } else {
            $this->storagePath = storage_path('app/public');
        }
    }

    public function index(Request $request, $path = '')
    {
        if ($request->ajax()) {
            $filters = json_decode($request->get('filters'), 1);
            $path = $this->storagePath;
            if (isset($filters['path'])) {
                $path .= '/'.str_replace(':', '/', $filters['path']);
            }
            $files = [];
            $storagePath = str_replace(storage_path('app'), '', $this->storagePath);
            foreach (File::files($path) as $file) {
                $filename = $file->getBasename();
                $temp_filepath = ltrim(str_replace($this->storagePath, '', $file->getPathname()), '/');
                $filepath = ltrim(str_replace('/', ':', $temp_filepath), '/');
                $files[] = [
                    'path' => '<a href="'.str_replace('public/', '', Storage::disk('public')->url($storagePath.'/'.$temp_filepath)).'" target="_blank">'.$temp_filepath.'</a>',
                    'last_modified' => \Carbon\Carbon::parse(Storage::lastModified($storagePath.'/'.$temp_filepath), auth()->user()->timezone)->toDateTimeString(),
                    'extension' => $file->getExtension(),
                    'size' => Storage::size($storagePath.'/'.$temp_filepath).' kb',
                    'actionsView' => view('dashing::admin.file.actions', compact('filepath', 'filename'))->render(),
                ];
            }
            $paginated = $this->paginate($files, $request->get('take', 25));
            if ('' != $request->get('filters', '')) {
                $paginated->appends(['filters' => $request->get('filters', '')]);
            }
            $links = $paginated->onEachSide(5)->links()->render();
            $currentUrl = $request->fullUrl();

            return compact('paginated', 'links', 'currentUrl');
        }
        $getUrl = route('file');
        $html = [
            ['title' => 'Path', 'data' => 'path', 'sortable' => false],
            ['title' => 'Type', 'data' => 'extension', 'sortable' => false],
            ['title' => 'Size', 'data' => 'size', 'sortable' => false],
            ['title' => 'Modified', 'data' => 'last_modified', 'sortable' => false],
            ['title' => '', 'data' => 'actionsView'],
        ];

        return view('dashing::admin.file.index', compact('html', 'getUrl', 'path'));
    }

    public function directories(Request $request)
    {
        $directories = [];
        $path = str_replace(':', '/', $request->input('path'));
        $pathArray = explode('/', $path);
        $pathCount = count($pathArray);
        if ($pathCount >= 1) {
            $currentDirectory = '' != $pathArray[$pathCount - 1] ? $pathArray[$pathCount - 1] : 'Top';
            if ('Top' != $currentDirectory) {
                unset($pathArray[$pathCount - 1]);
                $data = [
                    'path' => implode(':', $pathArray),
                    'label' => 1 == count($pathArray) ? 'Top' : last($pathArray),
                    'title' => 'Back',
                ];
                $directories[] = [
                    'view' => '<button data-href="'.$data['path'].'" class="list-group-item list-group-item-action goToDirectory" id="goToTopDirectory" data-title="'.$data['label'].'">'.$data['title'].'</button>',
                ];
            }

            $data = [
                'path' => str_replace('/', ':', $path),
                'label' => $currentDirectory,
                'title' => 'Current directory <strong>'.$currentDirectory.'</strong>',
                'dirname' => basename($path),
                'dom_id' => 'currentPathId',
            ];
            $directories[] = [
                'view' => view('dashing::admin.file.directories', compact('data'))->render(),
            ];
        }
        foreach (File::directories($this->storagePath.'/'.$path) as $directory) {
            $directory = str_replace($this->storagePath.'/', '', $directory);
            $data = [
                'path' => str_replace('/', ':', $directory),
                'label' => basename($directory),
                'title' => basename($directory),
                'dirname' => basename($directory),
            ];
            $directories[] = [
                'view' => view('dashing::admin.file.directories', compact('data'))->render(),
            ];
        }

        return $directories;
    }

    public function upload(Request $request, $path = '')
    {
        $request->validate([
            'files' => 'required',
        ]);
        $storagePath = str_replace(storage_path('app'), '', $this->storagePath);
        if ('' != $path) {
            $path = $storagePath.'/'.str_replace(':', '/', $path);
        } else {
            $path = $storagePath;
        }
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $key => $file) {
                $name = $request->file('files.'.$key)->getClientOriginalName();
                $upload_path = $request->file('files.'.$key)->storeAs($path, $name);
            }
        }

        return response()->json([
            'status' => 'success',
            'flash' => 'File Uploaded.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
            'currentUrl' => $request->fullUrl(),
            // 'redirect' => route('page.show', [$model->id]),
        ]);
    }

    /*public function show($id)
    {
        $model = app(config('instant.Models.Page'))->query()->with(['creator','modifier'])->findOrFail($id);
        return view('dashing::admin.page.show', compact('model'));
    }

    public function preview($id)
    {
        $model = app(config('instant.Models.Page'))->query()->with(['creator','modifier'])->findOrFail($id);
        $brandName = strtolower($model->brand->name);
        \View::addNamespace($brandName, base_path('brand/'.$brandName));
        return view($brandName.'::pages.page', compact('model'));
    }*/

    public function duplicate(Request $request, $path = '')
    {
        $path = $this->storagePath.'/'.str_replace(':', '/', $path);
        $request->validate([
            'name' => 'required',
        ]);
        $pathArray = explode('/', $path);
        unset($pathArray[count($pathArray) - 1]);
        $pathArray[] = $request->get('name');
        $newPath = implode('/', $pathArray);
        File::copy($path, $newPath);
        $newPath = str_replace('/', ':', $newPath);

        return response()->json([
            'status' => 'success',
            'flash' => 'File Duplicated.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
            'currentUrl' => $request->fullUrl(),
            // 'redirect' => route('page.show', [$model->id]),
        ]);
    }

    public function rename(Request $request, $path = '')
    {
        $path = $this->storagePath.'/'.str_replace(':', '/', $path);
        $request->validate([
            'name' => 'required',
        ]);
        $pathArray = explode('/', $path);
        unset($pathArray[count($pathArray) - 1]);
        $pathArray[] = $request->get('name');
        $newPath = implode('/', $pathArray);
        File::move($path, $newPath);
        $newPath = str_replace('/', ':', $newPath);

        return response()->json([
            'status' => 'success',
            'flash' => 'File Renamed.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
            'currentUrl' => $request->fullUrl(),
            // 'redirect' => route('page.show', [$model->id]),
        ]);
    }

    public function change(Request $request, $path = '')
    {
        $path = $this->storagePath.'/'.str_replace(':', '/', $path);
        $request->validate([
            'name' => 'required',
        ]);
        $pathArray = explode('/', $path);
        unset($pathArray[count($pathArray) - 1]);
        $pathArray[] = $request->get('name');
        $newPath = implode('/', $pathArray);
        File::move($path, $newPath);
        $newPath = str_replace('/', ':', $newPath);

        return response()->json([
            'status' => 'success',
            'flash' => 'Folder Renamed.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
        ]);
    }

    public function clone(Request $request, $path = '')
    {
        $path = $this->storagePath.'/'.str_replace(':', '/', $path);
        $request->validate([
            'name' => 'required',
        ]);
        $pathArray = explode('/', $path);
        unset($pathArray[count($pathArray) - 1]);
        $pathArray[] = $request->get('name');
        $newPath = implode('/', $pathArray);
        File::copyDirectory($path, $newPath);
        $newPath = str_replace('/', ':', $newPath);

        return response()->json([
            'status' => 'success',
            'flash' => 'Folder Duplicated.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
        ]);
    }

    public function make(Request $request, $path = '')
    {
        $path = $this->storagePath.'/'.str_replace(':', '/', $path);
        $request->validate([
            'name' => 'required',
        ]);
        $pathArray = explode('/', $path);
        $pathArray[] = $request->get('name');
        $newPath = implode('/', $pathArray);
        File::makeDirectory($newPath);

        return response()->json([
            'status' => 'success',
            'flash' => 'Folder Created.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
        ]);
    }

    public function destroy($path)
    {
        File::delete($this->storagePath.'/'.str_replace(':', '/', $path));

        return response()->json([
            'status' => 'success',
            'flash' => 'File Deleted.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
        ]);
    }

    public function remove($path)
    {
        $path = $this->storagePath.'/'.str_replace(':', '/', $path);
        File::cleanDirectory($path);
        File::deleteDirectory($path);

        return response()->json([
            'status' => 'success',
            'flash' => 'Folder Deleted.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
        ]);
    }

    protected function paginate($items, $perPage = 25, $page = null)
    {
        $pageName = 'page';
        $page = $page ?: (Paginator::resolveCurrentPage($pageName) ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
    }
}
