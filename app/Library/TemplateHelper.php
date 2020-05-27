<?php

namespace App\Library;


class TemplateHelper
{
    public static function replaceNotificationTemplateTag($templateStr, $data)
    {
        foreach ($data as $key => $value) {
            if ($value == null && $value == '') {
                $value = 'N/A';
            }
            $templateStr = str_replace('{{{' . $key . '}}}', $value, $templateStr);
        }
        return $templateStr;
    }
}