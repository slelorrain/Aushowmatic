<?php
interface Feed{

    static function getFeedStats();

    static function getFeedUsageInPercentage();

    static function getFeedTimeRemaining();

    static function getWebsiteLinkToShow( $show_id );

    static function getShowFeed( $show );

    static function launchDownloads( $preview = false );

}

?>