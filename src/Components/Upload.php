<?php

namespace slelorrain\Aushowmatic\Components;

class Upload
{

    public static function modal($file_name = '', $action = '', $parameter = '')
    {
        $style = 'class="upload modal" style="display:none;"';
        $title = 'title="Upload ' . $file_name . '"';

        $modal_link = '<a href="#upload-form' . $parameter . '" ' . $title . ' rel="modal:open">&#8679;</a>';

        return self::form($file_name, $action, $parameter, $style, $modal_link);
    }

    public static function normal($file_name = '', $action = '', $parameter = '')
    {
        $style = 'class="upload"';

        return self::form($file_name, $action, $parameter, $style);
    }

    private static function form($file_name = '', $action = '', $parameter = '', $style = '', $extraHtml = '')
    {
        $id = 'id="upload-form' . $parameter . '"';
        $action = 'action="?action=' . $action . (!empty($parameter) ? '&parameter=' . $parameter : '') . '"';
        $specific_attributes = 'method="post" enctype="multipart/form-data"';

        $input_file = '<input type="file" name="' . $file_name . '"/>';
        $submit = '<input type="submit" class="yt-button" value="Upload"/>';

        // Remove useless whitespaces
        $attributes = preg_replace('/\s+/', ' ', trim($id . ' ' . $action . ' ' . $style . ' ' . $specific_attributes));

        return '<form ' . $attributes . '>' . $input_file . $submit . '</form>' . $extraHtml;
    }

}
