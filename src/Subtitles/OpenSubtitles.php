<?php

namespace slelorrain\Aushowmatic\Subtitles;

use slelorrain\Aushowmatic\Core;

define('SEARCH_PATH', 'https://www.opensubtitles.org/en/search/sublanguageid-#LANG#/moviename-');
define('USER_AGENT', 'Aushowmatic');

class OpenSubtitles extends Core\Subtitle
{

    const ZIP_FILE = 'tmp.zip';

    public static function searchAndDownload($path_parts)
    {
        $download_url = self::getDownloadUrl($path_parts['filename']);

        if (isset($download_url)) {
            $result = Core\Curl::getPage($download_url, USER_AGENT, SEARCH_PATH);

            if ($result && substr($result, 0, strlen('<!DOCTYPE')) !== '<!DOCTYPE') {
                $result = file_put_contents(self::TMP_PATH . self::ZIP_FILE, $result);

                if ($result) {
                    $result = Core\System::unzip(self::TMP_PATH . self::ZIP_FILE, self::TMP_PATH);
                    unlink(self::TMP_PATH . self::ZIP_FILE);

                    if ($result) {
                        return self::move($path_parts);
                    }
                }
            }
        }

        return false;
    }

    public static function getDownloadUrl($search)
    {
        $search = str_replace($_ENV['PREFERRED_FORMAT'], '', $search);
        $search_url = str_replace('#LANG#', self::getLanguage(), SEARCH_PATH) . $search . '/simplexml';
        $search_page = Core\Curl::getPage($search_url);

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($search_page);
        libxml_clear_errors();

        if ($xml && is_object($xml->results->subtitle)) {
            return $xml->results->subtitle->download;
        }

        return false;
    }

    public static function getLanguage()
    {
        return $_ENV['SUBTITLES_LANGUAGE'];
    }

}
