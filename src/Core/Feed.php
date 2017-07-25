<?php

namespace slelorrain\Aushowmatic\Core;

abstract class Feed implements FeedInterface
{

    public static function getAvailableShows()
    {
        if (!isset($_SESSION['available_shows'])) {
            $_SESSION['available_shows'] = $_ENV['FEED_CLASS']::getAvailableShows();
        }
        return $_SESSION['available_shows'];
    }

    public static function launchDownloads($preview = false, $show = null)
    {
        $added = array();
        $could_be_added = array();

        if (isset($show) && !empty($show)) {
            // Retrieve content of show page
            $page = Curl::getPage(static::getShowFeed($show));
            // Parse pages and retrieve links that could be added
            static::parsePage($page, $could_be_added, false);
        } else {
            $urls = [];
            foreach (FeedInfo::getShowList() as $show) {
                $urls[] = static::getShowFeed($show);
            }
            // Retrieve content of show pages
            $pages = Curl::getPages($urls);
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
