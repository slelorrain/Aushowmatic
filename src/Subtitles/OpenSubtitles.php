<?php

namespace slelorrain\Aushowmatic\Subtitles;

use slelorrain\Aushowmatic\Core\Curl;
use slelorrain\Aushowmatic\Core\Subtitle;
use slelorrain\Aushowmatic\Core\System;

if (!defined('SEARCH_PATH')) define('SEARCH_PATH', 'https://www.opensubtitles.org/en/search/sublanguageid-#LANG#/moviename-');
if (!defined('USER_AGENT')) define('USER_AGENT', 'Aushowmatic');

class OpenSubtitles extends Subtitle
{

    public static function getDownloadUrl($search)
    {
        $search = str_replace($_ENV['PREFERRED_FORMAT'], '', $search);
        $searchUrl = str_replace('#LANG#', self::getLanguage(), SEARCH_PATH) . $search . '/simplexml';
        $searchPage = Curl::getPage($searchUrl, USER_AGENT);

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($searchPage);
        libxml_clear_errors();

        if ($xml && is_object($xml->results->subtitle)) {
            return $xml->results->subtitle->download;
        }
    }

    public static function afterDownload()
    {
        $file = self::TMP_PATH . self::TMP_FILE;
        $result = System::unzip($file, self::TMP_PATH);
        unlink($file);
        return $result;
    }

    public static function getLanguage()
    {
        return $_ENV['SUBTITLES_LANGUAGE'];
    }

}
