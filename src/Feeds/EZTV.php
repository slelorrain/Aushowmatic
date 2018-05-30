<?php

namespace slelorrain\Aushowmatic\Feeds;

use slelorrain\Aushowmatic\Core\Curl;
use slelorrain\Aushowmatic\Core\Feed;
use slelorrain\Aushowmatic\Core\FeedInfo;

class EZTV extends Feed
{

    const PATH = "https://eztv.ag/";

    public static function getWebsiteLinkToShow($show_id)
    {
        return self::PATH . 'shows/' . $show_id;
    }

    public static function getShowFeed($show)
    {
        return self::PATH . 'shows/' . $show;
    }

    public static function getAvailableShows()
    {
        $page = Curl::getPage(self::PATH . 'showlist');

        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($page);
        libxml_clear_errors();
        $xpath = new \DomXPath($dom);

        // Find lines corresponding to shows
        $nodes = $xpath->query("//*[contains(@class, 'thread_link')]");

        $shows = array();
        foreach ($nodes as $item) {
            $value = str_replace('/shows/', '', $item->getAttribute('href'));
            $text = $item->nodeValue;
            $shows[$value] = $text;
        }
        return $shows;
    }

    // This parsing is crappy because the DOM is :p
    public static function parsePage($page, &$couldBeAdded, $useMinDate = true)
    {
        // Remove line-ending characters
        $page = preg_replace('/\r|\n/', '', $page);
        // Remove white spaces between tags
        $page = preg_replace('~>\\s+<~m', '><', $page);

        $dom = new \DomDocument();
        // Warnings are muted because DOM retrieved is not valid
        libxml_use_internal_errors(true);
        $dom->loadHTML($page);
        libxml_clear_errors();
        $xpath = new \DomXPath($dom);

        // Find lines corresponding to episodes
        $nodes = $xpath->query("//*[contains(@class, 'epinfo')]");

        foreach ($nodes as $item) {
            $released = $item->parentNode->nextSibling->nextSibling->nextSibling->nodeValue;

            if (!$useMinDate || strtotime(self::getReleaseDate($released)) >= strtotime(FeedInfo::getMinDate())) {
                $episodeName = $item->nodeValue;

                $infoLink = $item->parentNode->previousSibling->firstChild;
                if ($infoLink->nodeType == XML_ELEMENT_NODE && $infoLink->hasAttribute('href')) {
                    $infoLink = $infoLink->getAttribute('href');
                } else {
                    $infoLink = $episodeName;
                }

                $downloadLink = $item->parentNode->nextSibling->firstChild->getAttribute('href');

                // If not added or current link contains preferred format
                if (!array_key_exists($infoLink, $couldBeAdded) || strpos($downloadLink, $_ENV['PREFERRED_FORMAT']) !== false) {
                    $couldBeAdded[$infoLink] = $downloadLink;
                }
            }
        }
    }

    private static function getReleaseDate($released)
    {
        $released = preg_replace('/(\d+)s/', '$1 second', $released);
        $released = preg_replace('/(\d+)m/', '$1 minute', $released);
        $released = preg_replace('/(\d+)h/', '$1 hour', $released);
        $released = preg_replace('/(\d+)d/', '$1 day', $released);
        $released = preg_replace('/(\d+) mo/', '$1 month', $released);
        // Add minus in front of every digits
        $released = preg_replace('/(\d+)/', '-$1', $released);

        return date("Y-m-d H:i:s", strtotime($released));
    }

}
