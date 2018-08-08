<?php

namespace slelorrain\Aushowmatic\Core;

class Resolution implements ChoosableInterface
{

    public static function getChoices()
    {
        return array('720p', '1080p');
    }
}
