<?php

namespace slelorrain\Aushowmatic\Components;

class Link
{

    const DEFAULT_CLASS = '';

    public static function action($content = '', $action = '', $parameter = '', $title = '', $class = '', $confirm = false)
    {
        $href = '?action=' . $action;
        $href .= (!empty($parameter)) ? '&parameter=' . $parameter : '';

        return self::link($content, $href, $title, $class, '', false, $confirm);
    }

    public static function show($content = '', $href = '', $title = '', $class = '')
    {
        $id = 'show_' . $href;
        $href = '#' . $href;
        $class = 'showSomething ' . $class;

        return self::link($content, $href, $title, $class, $id, false, false);
    }

    public static function out($content = '', $href = '', $title = '', $class = '')
    {
        return self::link($content, $href, $title, $class, '', true, false);
    }

    private static function link($content = '', $href = '', $title = '', $class = '', $id = '', $newWindow = true, $confirm = false)
    {
        $id = trim($id);
        $class = trim(static::DEFAULT_CLASS . ' ' . $class);
        $href = trim($href);
        $title = trim($title);

        $id = (!empty($id)) ? 'id="' . $id . '"' : '';
        $class = (!empty($class)) ? 'class="' . $class . '"' : '';
        $href = (!empty($href)) ? 'href="' . $href . '"' : '';
        $title = (!empty($title)) ? 'title="' . $title . '"' : '';
        $target = ($newWindow) ? 'target="_blank"' : '';
        $confirm = ($confirm) ? 'onclick="return confirm(\'Are you sure?\')"' :  '';

        // Remove useless whitespaces
        $attributes = preg_replace('/\s+/', ' ', trim($id . ' ' . $class . ' ' . $href . ' ' . $title . ' ' . $target . ' ' . $confirm));

        return '<a ' . $attributes . '>' . $content . '</a>';
    }

}
