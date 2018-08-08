<?php

namespace slelorrain\Aushowmatic\Core;

abstract class Feed implements FeedInterface, ChoosableInterface
{

    const FEEDS_PATH = APP_BASE_PATH . 'src/Feeds/';

    public static function getChoices()
    {
        $feeds = array();
        $files = array_diff(scandir(self::FEEDS_PATH), array('.', '..'));

        foreach ($files as $file) {
            $info = pathinfo(self::FEEDS_PATH . $file);
            $isSubclass = is_subclass_of(FEEDS_NAMESPACE . $info['filename'], get_class());

            if (is_file(self::FEEDS_PATH . $file) && $isSubclass) {
                $feeds[] = $info['filename'];
            }
        }

        return $feeds;
    }

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
        $couldBeAdded = array();

        if (isset($show) && !empty($show)) {
            // Retrieve content of show page
            $page = Curl::getPage(static::getShowFeed($show));
            // Parse pages and retrieve links that could be added
            static::parsePage($page, $couldBeAdded, false);
        } else {
            $urls = [];
            foreach (FeedInfo::getShowList() as $show) {
                $urls[] = static::getShowFeed($show);
            }

            // Retrieve content of show pages
            $pages = Curl::getPages($urls);
            // Parse pages and retrieve links that could be added
            foreach ($pages as $page) {
                static::parsePage($page, $couldBeAdded);
            }
        }

        foreach ($couldBeAdded as $ep) {
            $tmp = Utils::downloadTorrent($ep, $preview);
            if (isset($tmp)) {
                $added[] = $tmp;
            }
        }

        return $added;
    }
}
