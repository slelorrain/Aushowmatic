<?php

namespace slelorrain\Aushowmatic\Feeds;

use slelorrain\Aushowmatic\Core\Curl;
use slelorrain\Aushowmatic\Core\Feed;
use slelorrain\Aushowmatic\Core\FeedInfo;

class ShowRSS extends Feed
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

        $page = Curl::getPage(self::PATH . 'browse');

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

    public static function parsePage($page, &$couldBeAdded, $useMinDate = true)
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($page);
        libxml_clear_errors();
        if ($xml) {
            foreach ($xml->channel->item as $item) {
                if (!$useMinDate || strtotime($item->pubDate) >= strtotime(FeedInfo::getMinDate())) {

                    $epId = "";
                    $epTitle = $item->title;
                    $downloadLink = $item->link;

                    if (preg_match("(.+\s[0-9]+x[0-9]+)", $epTitle, $matches)) {
                        // 2x09
                        $epId = $matches[0];
                    } else {
                        if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $epTitle, $matches)) {
                            // 2014-10-20
                            $epId = $matches[0];
                        }
                    }

                    if (!array_key_exists($epId, $couldBeAdded)) {
                        $couldBeAdded[$epId] = $downloadLink;
                    } else {
                        // If current download link contains preferred format replace link previously set in array
                        if (strpos($downloadLink, $_ENV['PREFERRED_FORMAT']) !== false) {
                            $couldBeAdded[$epId] = $downloadLink;
                        }
                    }
                }
            }
        }
    }

}
