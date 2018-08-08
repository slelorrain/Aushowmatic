<?php

namespace slelorrain\Aushowmatic\Core;

interface FeedInterface
{

    static function getWebsiteLinkToShow($show_id);

    static function getShowFeed($show);

    static function getAvailableShows();

    static function parsePage($page, &$couldBeAdded, $useMinDate = true);

    static function launchDownloads($preview = false, $show = null);
}
