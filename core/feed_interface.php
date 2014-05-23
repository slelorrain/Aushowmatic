<?php

interface FeedInterface{

    static function getFeedStats();

    static function getFeedUsageInPercentage();

    static function getFeedTimeRemaining();

    static function getWebsiteLinkToShow( $show_id );

    static function getShowFeed( $show );

    static function parsePage( $page, &$could_be_added, $use_min_date = true );

    static function launchDownloads( $preview = false, $show = null );

}

?>