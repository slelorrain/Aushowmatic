<?php

namespace slelorrain\Aushowmatic\Components;

class Template
{

    public static function get($templateName)
    {
        ob_start();
        require_once('templates/_' . $templateName . '.php');
        // Remove whitespaces between HTML tags
        $template = preg_replace('/>\s+</', '><', ob_get_clean());
        return $template;
    }
}
