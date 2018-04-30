<?php

namespace slelorrain\Aushowmatic\Components;

class Upload
{

    public static function modal($fileName = '', $action = '', $parameter = '')
    {
        $style = 'class="upload modal" style="display:none;"';
        $title = 'title="Upload ' . $fileName . '"';

        $modalLink = '<a href="#upload-form' . $parameter . '" ' . $title . ' rel="modal:open">&#8679;</a>';

        return self::build($fileName, $action, $parameter, $style, $modalLink);
    }

    public static function normal($fileName = '', $action = '', $parameter = '')
    {
        $style = 'class="upload"';

        return self::build($fileName, $action, $parameter, $style);
    }

    private static function build($fileName = '', $action = '', $parameter = '', $style = '', $extraHtml = '')
    {
        $id = 'id="upload-form' . $parameter . '"';
        $action = 'action="?action=' . $action . (!empty($parameter) ? '&parameter=' . $parameter : '') . '"';
        $specificAttributes = 'method="post" enctype="multipart/form-data"';

        $inputFile = '<input type="file" name="' . $fileName . '"/>';
        $submit = '<input type="submit" class="yt-button" value="Upload"/>';

        // Remove useless whitespaces
        $attributes = preg_replace('/\s+/', ' ', trim($id . ' ' . $action . ' ' . $style . ' ' . $specificAttributes));

        return '<form ' . $attributes . '>' . $inputFile . $submit . '</form>' . $extraHtml;
    }

}
