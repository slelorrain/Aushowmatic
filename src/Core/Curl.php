<?php

namespace slelorrain\Aushowmatic\Core;

class Curl
{

    public static function getPage($url, $userAgent = null)
    {
        if (isset($url)) {
            $ch = self::getCurlHandle($url, $userAgent);
            $page = curl_exec($ch);
            curl_close($ch);
            return $page;
        }
    }

    public static function getPages($urls, $userAgent = null)
    {
        if (isset($urls)) {
            return self::doCurlMulti($urls, $userAgent);
        }
    }

    private static function getCurlHandle($url, $userAgent)
    {
        if (isset($url)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            if (isset($userAgent)) {
                curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
            }

            return $ch;
        }
    }

    // Create cURL handles, add them to a multi handle, and then run them in parallel
    // Based on the example of http://php.net/manual/en/function.curl-multi-exec.php
    //
    // On php 5.3.18+, curl_multi_select() may return -1 forever until you call curl_multi_exec()
    // See https://bugs.php.net/bug.php?id=63411 for more information
    private static function doCurlMulti($urls, $userAgent)
    {
        // Create a new cURL multi handle
        $mh = curl_multi_init();

        // Get and add handles
        foreach ($urls as $url) {
            $ch = self::getCurlHandle($url, $userAgent);
            curl_multi_add_handle($mh, $ch);
            $handles[] = $ch;
        }

        // Process each of the handles in the stack
        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        do {
            curl_multi_select($mh); // non-busy wait for state change
            $mrc = curl_multi_exec($mh, $active); // get new state
        } while ($active);

        // Retrieve the content of cURL handles and remove them
        foreach ($handles as $ch) {
            $pages[] = curl_multi_getcontent($ch);
            curl_multi_remove_handle($mh, $ch);
        }

        // Close the cURL multi handle
        curl_multi_close($mh);

        return $pages;
    }

}
