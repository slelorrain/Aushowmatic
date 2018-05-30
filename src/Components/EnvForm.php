<?php

namespace slelorrain\Aushowmatic\Components;

use slelorrain\Aushowmatic\Config;

class EnvForm
{

    public static function boolean($name = '')
    {
        $values = array('true' => 'Enabled', 'false' => 'Disabled');
        $select = self::select($values, $_ENV[$name]);
        return self::build($name, $select);
    }

    public static function normal($name = '', $values = array())
    {
        $select = self::select($values, $_ENV[$name]);
        return self::build($name, $select);
    }

    private static function select($values, $selected)
    {
        $attributes = 'name="value" class="flexAuto"';

        $options = '<option disabled selected value>--- Select a value ---</option>';
        foreach ($values as $key => $name) {
            $value = is_int($key) ? $name : $key;
            $display =  ($key != $name && !is_int($key) && !Config::isBoolean($key) ? $key . ' (' . $name . ')' : $name);
            $options .= '<option value="' . $value . '" ' . ($value == $selected ? 'selected' : '') . '>' . $display . '</option>';
        }

        return '<select ' . $attributes . '>' . $options . '</select>';
    }

    private static function build($name = '', $select)
    {
        $attributes = 'class="flex" method="post" action="?action=updateEnv"';
        $inputName = '<input type="hidden" name="name" value="' . $name . '"/>';
        $submit = '<input type="submit" class="yt-button" value="Update"/>';

        return '<form ' . $attributes . '>' . $inputName . $select . $submit . '</form>';
    }

}
