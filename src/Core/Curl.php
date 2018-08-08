<?php

namespace slelorrain\Aushowmatic\Core;

class Curl
{

    const MAX_RETRY = 5;
    const RETRY_DELAY = 1000000;
    const MULTI_BATCH_SIZE = 20;

    public static function getPage($url, $userAgent = null, $referer = null)
    {
        if (!empty($url)) {
            return self::doCurl($url, $userAgent, $referer);
        }
    }

    public static function getPages($urls, $userAgent = null, $referer = null)
    {
        if (!empty($urls)) {
            return self::doCurlMulti($urls, $userAgent, $referer);
        }
    }

    private static function getCurlHandle($url, $userAgent, $referer)
    {
        if (!empty($url)) {
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

            if (isset($referer)) {
                curl_setopt($ch, CURLOPT_REFERER, $referer);
            }

            return $ch;
        }
    }

    private static function doCurl($url, $userAgent, $referer)
    {
        $try = 0;
        $done = false;
        $ch = self::getCurlHandle($url, $userAgent, $referer);

        while (!$done && $try < self::MAX_RETRY) {
            if ($try++ > 1) {
                usleep(self::RETRY_DELAY);
            }

            $page = curl_exec($ch);

            if (curl_getinfo($ch)['http_code'] == 200) {
                $done = true;
            }
        }

        curl_close($ch);

        return $page;
    }

    private static function doCurlMulti($urls, $userAgent, $referer)
    {
        $try = 0;
        $done = false;
        $mh = curl_multi_init();

        while (!$done && $try < self::MAX_RETRY) {
            if ($try++ > 1) {
                usleep(self::RETRY_DELAY);
            }

            // Get and add handles
            $handles = array();
            $extract = array_splice($urls, 0, self::MULTI_BATCH_SIZE);
            foreach ($extract as $url) {
                $ch = self::getCurlHandle($url, $userAgent, $referer);
                curl_multi_add_handle($mh, $ch);
                $handles[] = $ch;
            }

            // Process each of the handles in the stack
            $running = null;
            do {
                curl_multi_exec($mh, $running);
                curl_multi_select($mh);
            } while ($running > 0);

            // Retrieve the content of cURL handles and remove them
            foreach ($handles as $ch) {
                $info = curl_getinfo($ch);
                if ($info['http_code'] != 200 && !in_array($info['url'], $urls)) {
                    array_push($urls, $info['url']);
                } else {
                    $pages[] = curl_multi_getcontent($ch);
                }

                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
            }

            if (empty($urls)) {
                $done = true;
            }
        }

        curl_multi_close($mh);

        return $pages;
    }
}
