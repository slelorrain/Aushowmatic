<?php

namespace slelorrain\Aushowmatic\Subtitles;

use slelorrain\Aushowmatic\Core;

define('SEARCH_PATH', 'http://www.addic7ed.com/search.php?search=');
define('USER_AGENT', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:55.0) Gecko/20100101 Firefox/55.0');

class Addic7ed extends Core\Subtitle
{

    public static function getDownloadUrl($search)
    {
        $search = str_replace($_ENV['PREFERRED_FORMAT'], '', $search);
        $search_url = SEARCH_PATH . $search;
        $search_page = Core\Curl::getPage($search_url, USER_AGENT);

        $dom = new \DomDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($search_page);
        libxml_clear_errors();
        $finder = new \DomXPath($dom);

        $nodes = $finder->query("//*[contains(@class, 'language')]");

        foreach ($nodes as $item) {
            $language = explode(" ", trim($item->nodeValue))[0];
            $is_desired_language = in_array($language, self::getLanguage());
            $is_completed = (trim($item->nextSibling->nextSibling->nodeValue) == 'Completed');

            if ($is_desired_language && $is_completed) {
                // Use "." in front of the query because button is not a direct child
                $download_button = $finder->query(".//*[contains(@class, 'buttonDownload')]", $item->parentNode)->item(0);

                if ($download_button->nodeType == XML_ELEMENT_NODE && $download_button->hasAttribute('href')) {
                    return 'http://www.addic7ed.com' . $download_button->getAttribute('href');
                }
            }
        }
    }

    public static function afterDownload()
    {
        return rename(self::TMP_PATH . self::TMP_FILE, self::TMP_PATH . self::TMP_FILE . '.' . $_ENV['SUBTITLES_EXTENSION']);
    }

    public static function getLanguage()
    {
        return Core\IsoHelper::getEnglishNamesByIso6392Code($_ENV['SUBTITLES_LANGUAGE']);
    }

}
