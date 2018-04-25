<?php

namespace slelorrain\Aushowmatic\Core;

class IsoHelper
{

    public static function getEnglishNamesByIso6392Code($code)
    {
        $content = file_get_contents(APP_BASE_PATH . 'resources/iso/ISO_639-2.json');
        $iso6392 = json_decode($content, true);

        if (isset($iso6392[$code])) {
           return $iso6392[$code]['english'];
        }
    }

}
