<?php

namespace slelorrain\Aushowmatic\Components;

class SubtitlesList
{

    public static function modal($id = '', $content = [], $extraContent = '')
    {
        $style = 'class="list modal" style="display:none;"';
        $title = 'title="List ' . $id . '"';

        $modalLink = '<a href="#list' . $id . '" ' . $title . ' rel="modal:open">&#9778;</a>';

        return self::build($id, $style, $content, $extraContent, $modalLink);
    }

    public static function normal($id, $content = [], $extraContent = '')
    {
        $style = 'class="list"';

        return self::build($id, $style, $content, $extraContent);
    }

    private static function build($id, $style = '', $content = [], $extraContent = '', $extraHtml = '')
    {
        $id = 'id="list' . $id . '"';
        // Remove useless whitespaces
        $attributes = preg_replace('/\s+/', ' ', trim($id . ' ' . $style));

        $list = '<h3>Subtitles</h3><pre class="subtitles">';
        if (!empty($content)) {
            foreach ($content as $value) {
                $list .= $value . '<br>';
            }
        } else {
            $list .= 'No subtitles<br>';
        }
        $list .= '</pre>';

        return '<div ' . $attributes . '>' . $list . '<br>' . $extraContent . '</div>' . $extraHtml;
    }
}
