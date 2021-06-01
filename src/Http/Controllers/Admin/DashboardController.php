<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
    }
    public function index(Request $request)
    {
        return inertia('Dashboard');
    }

    public function chatify(Request $request)
    {
        return view('dashing::admin.dashboard.chatify');
    }

    public function lfm(Request $request)
    {
        return view('dashing::admin.dashboard.lfm');
    }

    public function seo(Request $request)
    {
        return view('dashing::admin.dashboard.seo');
    }

    public function opcache(Request $request)
    {
        return view('dashing::admin.dashboard.opcache');
    }

    public function wiki(Request $request, $file = 'Index.md')
    {
        $md = \File::get(base_path('vendor/wikichua/dashing/wiki/'.$file));
        $search = [
            '(Installation.md)',
            '(Module-Development.md)',
            '(Brand-Development.md)',
            '(Available-Components.md)',
            '(Available-Helper.md)',
        ];
        $replace = [
            '('.route('wiki.home', ['Installation.md']).')',
            '('.route('wiki.home', ['Module-Development.md']).')',
            '('.route('wiki.home', ['Brand-Development.md']).')',
            '('.route('wiki.home', ['Available-Components.md']).')',
            '('.route('wiki.home', ['Available-Helper.md']).')',
        ];
        if (\Str::contains($md, $search)) {
            $md = str_replace($search, $replace, $md);
        }

        return view('dashing::admin.dashboard.wiki', compact('md'));
    }
}
