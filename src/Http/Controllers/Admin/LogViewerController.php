<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;

class LogViewerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:read-log-viewer')->only(['index', 'read']);
        $this->log_viewer = new LaravelLogViewer();
        $this->request = app('request');
        inertia()->share('moduleName', 'Log viewer');
    }

    public function index($folder = 'root', $file = '')
    {
        $can = [
            'read' => auth()->user()->can('read-log-viewer'),
            'delete' => auth()->user()->can('delete-log-viewer'),
            'download' => auth()->user()->can('download-log-viewer'),
        ];

        $folderFiles = [];
        if ($folder != 'root' && $folder != '') {
            $this->log_viewer->setFolder($folder);
            $folderFiles = $this->log_viewer->getFolderFiles(true);
        }
        if ($file != '') {
            $this->log_viewer->setFile($file);
        }

        $logs = $this->log_viewer->all();
        $colors = [
            'error' => 'red',
            'info' => 'blue',
            'warning' => 'yellow',
        ];
        foreach ($logs as $key => &$log) {
            if (isset($log['level']) && $log['level'] != '') {
                $log['level'] = '<div class="text-'.$colors[$log['level']].'-800 capitalize">'.$log['level'].'</div>';
                $log['content'] = $log['text'];
                $log['subcontent'] = nl2br($log['stack']);
            } else {
                unset($logs[$key]);
            }
        }
        $data = [
            'logs' => ['data' => $logs],
            'folders' => $this->log_viewer->getFolders(),
            'current_folder' => $this->log_viewer->getFolderName(),
            'folder_files' => $folderFiles,
            'files' => $this->log_viewer->getFiles(true),
            'current_file' => $this->log_viewer->getFileName(),
            'standardFormat' => true,
        ];

        $columns = [
            ['title' => 'Level', 'data' => 'level', 'class' => 'align-top'],
            ['title' => 'Date', 'data' => 'date', 'class' => 'align-top whitespace-nowrap'],
            ['title' => 'Content', 'data' => 'content', 'class' => 'align-top text-left'],
        ];
        return inertia('Admin/LogViewer/Index', compact('can', 'data', 'columns'));
    }
}
