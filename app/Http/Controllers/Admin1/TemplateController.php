<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Yajra\DataTables\Facades\DataTables;

class TemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin');
    }

    public function index()
    {
        if (request('type')) {
            $data = [];
            $data['type'] = request('type');
            return view('admin1.pages.templates.index', $data);
        } else {
            abort(404);
        }
    }

    public function store()
    {
        $id = request('id');
        $template = Template::find($id);
        $this->validate(request(), Template::validationRules($template->type));
        $template->update(request()->all());
        $data = [];
        $data['status'] = true;
        return $data;
    }

    public function edit(Template $template)
    {
        $data = [];
        $data['inputs'] = [
            'id'          => ["type" => "hidden", 'value' => $template->id],
            'name'        => ["type" => "text", 'value' => $template->name],
            'key'         => ["type" => "text", 'value' => $template->key],
            'params'      => ["type" => "text", 'value' => $template->params],
            'receivers'   => ["type" => "text", 'value' => $template->receivers],
            'subject'     => ["type" => "text", 'value' => $template->subject],
            'subject_esp' => ["type" => "text", 'value' => $template->subject_esp],
            'subject_pap' => ["type" => "text", 'value' => $template->subject_pap],
        ];
        if ($template->type == 1 || $template->type == 3) {
            $data['inputs'] += [
                'content'     => ["type" => "tinymce", 'value' => $template->content],
                'content_esp' => ["type" => "tinymce", 'value' => $template->content_esp],
                'content_pap' => ["type" => "tinymce", 'value' => $template->content_pap],
            ];
        } else {
            $data['inputs'] += [
                'content'     => ["type" => "textarea", 'value' => $template->content],
                'content_esp' => ["type" => "textarea", 'value' => $template->content_esp],
                'content_pap' => ["type" => "textarea", 'value' => $template->content_pap],
            ];
        }
        $data['type'] = $template->type;
        return $data;
    }

    public function indexDatatable()
    {
        $templates = Template::select('templates.*');
        if (request('type')) {
            if (request('type') == 1) {
                $templates->whereIn('type', [1, 3]);
            } else {
                $templates->where('type', '=', request('type'));
            }
        }
        return DataTables::of($templates)
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' data-toggle='tooltip' data-id='$row->id' class='$iconClass editTemplate'>
                            <i class='fa fa-pencil'></i>
                        </a>";
                $html .= "<a href='javascript:;' title='View' data-toggle='tooltip' class='$iconClass editTemplate' data-id='$row->id' data-type='view'>
                            <i class='fa fa-eye'></i>
                        </a>";
                return $html;
            })
            ->make();
    }
}
