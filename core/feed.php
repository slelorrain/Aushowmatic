<?php
require_once(dirname(__FILE__) . '/feed_interface.php');

abstract class Feed implements FeedInterface{

    public static function launchDownloads( $preview = false ){
        $added = array();
        $could_be_added = array();

        // Retrieve content of pages
        $pages = self::doCurlMulti();

        // Parse pages and retrieve links that could be added
        foreach( $pages as $page ){
            static::parsePage($page, $could_be_added);
        }

        foreach( $could_be_added as $ep ){
            $tmp = Utils::downloadTorrent($ep, $preview);
            if( isset($tmp) ) $added[] = $tmp;
        }

        return $added;
    }

    private static function getCurlHandle( $show ){
        if( isset($show) ){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, static::getShowFeed($show));
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            return $ch;
        }
    }

    // Create cURL handles, add them to a multi handle, and then run them in parallel
    // Based on the example of http://php.net/manual/en/function.curl-multi-exec.php
    //
    // On php 5.3.18+, curl_multi_select() may return -1 forever until you call curl_multi_exec()
    // See https://bugs.php.net/bug.php?id=63411 for more information
    private static function doCurlMulti(){

        // Create a new cURL multi handle
        $mh = curl_multi_init();

        // Get and add handles
        foreach( Utils::getShowList() as $show ){
            if( !empty($show) ){
                $ch = self::getCurlHandle($show);
                curl_multi_add_handle($mh, $ch);
                $handles[] = $ch;
            }
        }

        // Process each of the handles in the stack
        $active = null;
        do{
            $mrc = curl_multi_exec($mh, $active);
        }while( $mrc == CURLM_CALL_MULTI_PERFORM );

        do{
            curl_multi_select($mh); // non-busy wait for state change
            $mrc = curl_multi_exec($mh, $active); // get new state
        }while( $active );

        // Retrieve the content of cURL handles and remove them
        foreach( $handles as $ch ){
            $pages[] = curl_multi_getcontent($ch);
            curl_multi_remove_handle($mh, $ch);
        }

        // Close the cURL multi handle
        curl_multi_close($mh);

        return $pages;
    }

}

?>