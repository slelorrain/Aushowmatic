<?php

namespace slelorrain\Aushowmatic\Core;

interface SubtitleInterface
{

    static function getDownloadUrl($search);

    static function afterDownload();

    static function getLanguage();

}
