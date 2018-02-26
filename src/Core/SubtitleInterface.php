<?php

namespace slelorrain\Aushowmatic\Core;

interface SubtitleInterface
{

    static function searchAndDownload($path_parts);

    static function getDownloadUrl($search);

    static function move($path_parts);

    static function getLanguage();

}
