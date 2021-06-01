<?php

namespace Wikichua\Instant\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class MailerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:access-admin-panel']);
        $this->middleware('intend_url')->only(['index', 'read']);
        $this->middleware('can:read-mailers')->only(['index', 'read']);
        $this->middleware('can:update-mailers')->only(['edit', 'update']);
        $this->middleware('can:delete-mailers')->only('destroy');
        inertia()->share('moduleName', 'Mailer Management');
    }

    public function index(Request $request)
    {
        $can = [
            'read' => auth()->user()->can('read-mailers'),
            'update' => auth()->user()->can('update-mailers'),
            'delete' => auth()->user()->can('delete-mailers'),
        ];
        $models = app(config('instant.Models.Mailer'))->query()
                ->checkBrand()
                ->filter($request->get('filters', ''))
                ->sorting($request->get('sort', ''), $request->get('direction', ''))
                ->paginate($request->get('take', 25));
        foreach ($models as $model) {
            $model->parameters = app($model->mailable)->getVariables();
        }
        if ('' != $request->get('filters', '')) {
            $models->appends(['filters' => $request->get('filters', '')]);
        }
        if ('' != $request->get('sort', '')) {
            $models->appends(['sort' => $request->get('sort', ''), 'direction' => $request->get('direction', 'asc')]);
        }
        $columns = [
            ['title' => 'Mailable', 'data' => 'mailable', 'sortable' => false],
            ['title' => 'Subject', 'data' => 'subject', 'sortable' => false],
            ['title' => 'Available Params', 'data' => 'parameters', 'sortable' => false],
            ['title' => 'Created Date', 'data' => 'created_at', 'sortable' => false],
            ['title' => '', 'data' => 'actionsView'],
        ];

        return inertia('Admin/Mailer/Index', compact('columns', 'models', 'can'));
    }

    public function show($id)
    {
        $model = app(config('instant.Models.Mailer'))->query()->with(['creator','modifier'])->findOrFail($id);
        $preview = app($model->mailable);

        return inertia('Admin/Mailer/Show', compact('model'));
    }

    public function edit(Request $request, $id)
    {
        $model = app(config('instant.Models.Mailer'))->query()->with(['creator','modifier'])->findOrFail($id);

        return inertia('Admin/Mailer/Edit', compact('model'));
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
            'receiver_id' => permissionUserIds('read-mailers'),
            'icon' => $model->menu_icon,
        ]);

        return Redirect::route('mailer.edit', [$model->id])->with([
            'status' => 'success',
            'flash' => 'Mailer Updated.',
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
            'receiver_id' => permissionUserIds('read-mailers'),
            'icon' => $model->menu_icon,
        ]);
        $model->delete();

        return Redirect::route('mailer')->with([
            'status' => 'success',
            'flash' => 'Mailer Deleted.',
        ]);
    }

    public function preview(Request $request, $id)
    {
        $model = app(config('instant.Models.Mailer'))->query()->with(['creator','modifier'])->findOrFail($id);
        $params = app($model->mailable)->getVariables();
        if ($request->isMethod('post')) {
            return app($model->mailable)->preview();
        }

        return inertia('Admin/Mailer/Preview', compact('model', 'params'));
    }
}
