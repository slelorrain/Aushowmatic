<?php

namespace slelorrain\Aushowmatic\Feeds;

use slelorrain\Aushowmatic\Core\Feed;

class EZTV extends Feed
{

    const PATH = "https://eztv.it/";

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
        return false;
    }

    // This parsing is crappy but the DOM is crappy too :p
    public static function parsePage($page, &$couldBeAdded, $useMinDate = true)
    {
        $dom = new \DomDocument();
        // Warnings are muted because DOM retrieved is not valid
        libxml_use_internal_errors(true);
        $dom->loadHTML($page);
        libxml_clear_errors();
        $finderEpinfo = new \DomXPath($dom);

        // Find lines corresponding to an episode
        $nodes = $finderEpinfo->query("//*[contains(@class, 'epinfo')]");

        foreach ($nodes as $item) {
            // Date of released
            $released = $item->parentNode->nextSibling->nextSibling->nextSibling->nextSibling->nodeValue;

            // At the moment, for EZTV we only handle episodes with a release date below a week
            if (!$useMinDate || $released != ">1 week") {

                $episodeName = $item->nodeValue;

                $infoLink = $item->parentNode->previousSibling->previousSibling->firstChild->nextSibling;
                if ($infoLink->nodeType == XML_ELEMENT_NODE && $infoLink->hasAttribute('href')) {
                    $infoLink = $infoLink->getAttribute('href');
                } else {
                    $infoLink = $episodeName;
                }

                $downloadLink = $item->parentNode->nextSibling->nextSibling->firstChild->getAttribute('href');

                if (!array_key_exists($infoLink, $couldBeAdded)) {
                    $couldBeAdded[$infoLink] = $downloadLink;
                } else {
                    // If current download link contains preferred format replace link previously set in array
                    if (strpos($downloadLink, $_ENV['PREFERRED_FORMAT']) !== false) {
                        $couldBeAdded[$infoLink] = $downloadLink;
                    }
                }
            }
        }
    }

}
