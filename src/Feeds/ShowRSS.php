<?php

namespace slelorrain\Aushowmatic\Feeds;

use slelorrain\Aushowmatic\Core;

class ShowRSS extends Core\Feed
{

    const PATH = "http://showrss.info/";

    public static function getWebsiteLinkToShow($show_id)
    {
        return self::PATH . 'browse/' . $show_id;
    }

    public static function getShowFeed($show_id)
    {
        return self::PATH . 'show/' . $show_id . '.rss';
    }

    public static function getAvailableShows()
    {
        $shows = array();

        $page = Core\Curl::getPage(self::PATH . 'browse');

        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($page);
        libxml_clear_errors();

        $selector = $dom->getElementById('showselector');

        if ($selector) {
            $options = $selector->getElementsByTagName('option');

            foreach ($options as $option) {
                $value = $option->getAttribute('value');
                $text = $option->textContent;

                if (is_numeric($value)) {
                    $shows[$value] = $text;
                }
            }
        }

        return $shows;
    }

    public static function parsePage($page, &$could_be_added, $use_min_date = true)
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($page);
        libxml_clear_errors();
        if ($xml) {
            foreach ($xml->channel->item as $item) {
                if (!$use_min_date || strtotime($item->pubDate) >= strtotime(Core\FeedInfo::getMinDate())) {

                    $epId = "";
                    $epTitle = $item->title;
                    $downloadLink = $item->link;

                    if (preg_match("(.+\s[0-9]+x[0-9]+)", $epTitle, $n)) {
                        // 2x09
                        $epId = $n[0];
                    } else {
                        if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $epTitle, $n)) {
                            // 2014-10-20
                            $epId = $n[0];
                        }
                    }

                    if (!array_key_exists($epId, $could_be_added)) {
                        $could_be_added[$epId] = $downloadLink;
                    } else {
                        // If current download link contains preferred format replace link previously set in array
                        if (strpos($downloadLink, $_ENV['PREFERRED_FORMAT']) !== false) {
                            $could_be_added[$epId] = $downloadLink;
                        }
                    }
                }
            }
        }
    }

}
