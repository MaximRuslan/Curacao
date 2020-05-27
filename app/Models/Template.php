<?php

namespace App\Models;

use App\Library\TemplateHelper;
use Illuminate\Database\Eloquent\Model;

class Template extends BaseModel
{
    protected $fillable = [
        'key',
        'name',
        'type',
        'receivers',
        'params',
        'subject',
        'subject_esp',
        'subject_pap',
        'content',
        'content_esp',
        'content_pap',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public static function validationRules($type = 1)
    {
        $rules = [
            'name'        => 'required',
            'content'     => 'required',
            'content_esp' => 'required',
            'content_pap' => 'required',
        ];

        if ($type != 3) {
            $rules += [
                'subject'     => 'required',
                'subject_esp' => 'required',
                'subject_pap' => 'required',
            ];
        }

        return $rules;
    }

    public static function findFromKey($key, $type, $lang, $data)
    {
        $template = self::where('key', '=', $key)->where('type', '=', $type)->first();

        if ($lang == 'esp') {
            $template->subject = TemplateHelper::replaceNotificationTemplateTag($template->subject_esp, $data);
            $template->content = TemplateHelper::replaceNotificationTemplateTag($template->content_esp, $data);
        } else if ($lang == 'pap') {
            $template->subject = TemplateHelper::replaceNotificationTemplateTag($template->subject_pap, $data);
            $template->content = TemplateHelper::replaceNotificationTemplateTag($template->content_pap, $data);
        } else {
            $template->subject = TemplateHelper::replaceNotificationTemplateTag($template->subject, $data);
            $template->content = TemplateHelper::replaceNotificationTemplateTag($template->content, $data);
        }

        return $template;
    }
}
