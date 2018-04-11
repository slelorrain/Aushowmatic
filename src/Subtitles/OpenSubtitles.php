<?php

namespace slelorrain\Aushowmatic\Subtitles;

use slelorrain\Aushowmatic\Core;

define('SEARCH_PATH', 'https://www.opensubtitles.org/en/search/sublanguageid-#LANG#/moviename-');
define('USER_AGENT', 'Aushowmatic');

class OpenSubtitles extends Core\Subtitle
{

    public static function getDownloadUrl($search)
    {
        $search = str_replace($_ENV['PREFERRED_FORMAT'], '', $search);
        $search_url = str_replace('#LANG#', self::getLanguage(), SEARCH_PATH) . $search . '/simplexml';
        $search_page = Core\Curl::getPage($search_url, USER_AGENT);

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($search_page);
        libxml_clear_errors();

        if ($xml && is_object($xml->results->subtitle)) {
            return $xml->results->subtitle->download;
        }
    }

    public static function afterDownload()
    {
        $result = Core\System::unzip(self::TMP_PATH . self::TMP_FILE, self::TMP_PATH);
        unlink(self::TMP_PATH . self::TMP_FILE);
        return $result;
    }

    public static function getLanguage()
    {
        return $_ENV['SUBTITLES_LANGUAGE'];
    }

}
