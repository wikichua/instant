<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

class LogViewerController extends \Rap2hpoutre\LaravelLogViewer\LogViewerController
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:read-log-viewer')->only(['index', 'read']);
        parent::__construct();
        $this->view_log = 'dashing::admin.log.viewer';
    }

    public function index()
    {
        return parent::index();
    }
}
