<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MailerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:Read Mailers')->only(['index', 'read']);
        $this->middleware('can:Update Mailers')->only(['edit', 'update']);
        $this->middleware('can:Delete Mailers')->only('destroy');
        if (false == app()->runningInConsole()) {
            \Breadcrumbs::for('home', function ($trail) {
                $trail->push('Mailer Listing', route('mailer'));
            });
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $models = app(config('instant.Models.Mailer'))->query()
                ->checkBrand()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
            ;
            $paginated = $models->paginate($request->get('take', 25));
            foreach ($paginated as $model) {
                $model->actionsView = view('dashing::admin.mailer.actions', compact('model'))->render();
                $model->parameters = app($model->mailable)->getVariables();
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
        $getUrl = route('mailer');
        $html = [
            ['title' => 'Mailable', 'data' => 'mailable', 'sortable' => false, 'filterable' => true],
            ['title' => 'Subject', 'data' => 'subject', 'sortable' => false, 'filterable' => true],
            ['title' => 'Available Params', 'data' => 'parameters', 'sortable' => false, 'filterable' => true],
            ['title' => 'Created Date', 'data' => 'created_at', 'sortable' => false, 'filterable' => true],
            ['title' => '', 'data' => 'actionsView'],
        ];

        return view('dashing::admin.mailer.index', compact('html', 'getUrl'));
    }

    public function show($id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Show Mailer');
        });
        $model = app(config('instant.Models.Mailer'))->query()->with(['creator','modifier'])->findOrFail($id);
        $preview = app($model->mailable);

        return view('dashing::admin.mailer.show', compact('model'));
    }

    public function edit(Request $request, $id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Edit Mailer');
        });
        $model = app(config('instant.Models.Mailer'))->query()->with(['creator','modifier'])->findOrFail($id);

        return view('dashing::admin.mailer.edit', compact('model'));
    }

    public function update(Request $request, $id)
    {
        $model = app(config('instant.Models.Mailer'))->query()->with(['creator','modifier'])->findOrFail($id);

        $request->validate([
            'subject' => 'required',
            'html_template' => 'required',
            'text_template' => 'required',
        ]);

        $request->merge([
            'updated_by' => auth()->id(),
        ]);

        $model->update($request->all());
        sendAlert([
            'brand_id' => 0,
            'link' => $model->readUrl,
            'message' => 'Mailer Updated. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('Read Mailers'),
            'icon' => $model->menu_icon,
        ]);

        return response()->json([
            'status' => 'success',
            'flash' => 'Mailer Updated.',
            'reload' => false,
            'relist' => false,
            'redirect' => route('mailer.edit', [$model->id]),
        ]);
    }

    public function destroy($id)
    {
        $model = app(config('instant.Models.Mailer'))->query()->with(['creator','modifier'])->findOrFail($id);
        sendAlert([
            'brand_id' => 0,
            'link' => null,
            'message' => 'Mailer Deleted. ('.$model->name.')',
            'sender_id' => auth()->id(),
            'receiver_id' => permissionUserIds('Read Mailers'),
            'icon' => $model->menu_icon,
        ]);
        $model->delete();

        return response()->json([
            'status' => 'success',
            'flash' => 'Mailer Deleted.',
            'reload' => false,
            'relist' => true,
            'redirect' => false,
        ]);
    }

    public function preview(Request $request, $id)
    {
        \Breadcrumbs::for('breadcrumb', function ($trail) {
            $trail->parent('home');
            $trail->push('Preview Mailer');
        });
        $model = app(config('instant.Models.Mailer'))->query()->with(['creator','modifier'])->findOrFail($id);
        $params = app($model->mailable)->getVariables();
        if ($request->isMethod('post')) {
            return app($model->mailable)->preview();
        }

        return view('dashing::admin.mailer.preview', compact('model', 'params'));
    }
}
