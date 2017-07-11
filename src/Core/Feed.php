<?php

namespace slelorrain\Aushowmatic\Core;

abstract class Feed implements FeedInterface
{

    public static function launchDownloads($preview = false, $show = null)
    {
        $added = array();
        $could_be_added = array();

        if (isset($show) && !empty($show)) {
            // Retrieve content of show page
            $page = Curl::getPage(static::getShowFeed($show));
            static::parsePage($page, $could_be_added, false);
        } else {
            // Retrieve content of pages
            $pages = Curl::getPages(Utils::getShowList());
            // Parse pages and retrieve links that could be added
            foreach ($pages as $page) {
                static::parsePage($page, $could_be_added);
            }
        }

        foreach ($could_be_added as $ep) {
            $tmp = Utils::downloadTorrent($ep, $preview);
            if (isset($tmp)) {
                $added[] = $tmp;
            }
        }

        return $added;
    }

}
