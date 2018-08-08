<?php

namespace slelorrain\Aushowmatic\Subtitles;

use slelorrain\Aushowmatic\Core\Curl;
use slelorrain\Aushowmatic\Core\IsoHelper;
use slelorrain\Aushowmatic\Core\Subtitle;

if (!defined('SEARCH_PATH')) define('SEARCH_PATH', 'http://www.addic7ed.com/search.php?search=');
if (!defined('USER_AGENT')) define('USER_AGENT', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:55.0) Gecko/20100101 Firefox/55.0');

class Addic7ed extends Subtitle
{

    public static function getDownloadUrl($search)
    {
        $search = str_replace($_ENV['PREFERRED_FORMAT'], '', $search);
        $searchUrl = SEARCH_PATH . $search;
        $searchPage = Curl::getPage($searchUrl, USER_AGENT);

        $dom = new \DomDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($searchPage);
        libxml_clear_errors();
        $finder = new \DomXPath($dom);

        $nodes = $finder->query("//*[contains(@class, 'language')]");

        foreach ($nodes as $item) {
            $language = explode(" ", trim($item->nodeValue))[0];
            $isDesiredLanguage = in_array($language, self::getLanguage());
            $isCompleted = (trim($item->nextSibling->nextSibling->nodeValue) == 'Completed');

            if ($isDesiredLanguage && $isCompleted) {
                // Use "." in front of the query because button is not a direct child
                $downloadButton = $finder->query(".//*[contains(@class, 'buttonDownload')]", $item->parentNode)->item(0);

                if ($downloadButton->nodeType == XML_ELEMENT_NODE && $downloadButton->hasAttribute('href')) {
                    return 'http://www.addic7ed.com' . $downloadButton->getAttribute('href');
                }
            }
        }
    }

    public static function afterDownload()
    {
        $file = self::TMP_PATH . self::TMP_FILE;
        return rename($file, $file . '.' . $_ENV['SUBTITLES_EXTENSION']);
    }

    public static function getLanguage()
    {
        return IsoHelper::getEnglishNamesByIso6392Code($_ENV['SUBTITLES_LANGUAGE']);
    }
}
