<?php

namespace slelorrain\Aushowmatic\Core;

use slelorrain\Aushowmatic\Core\Backupable;

class FeedInfo extends Backupable
{

    public static function getBackupableFile()
    {
        return $_ENV['FEED_INFO'];
    }

    public static function getShowList()
    {
        $info = self::getFeedInfo();
        return $info->shows;
    }

    public static function getDoneList()
    {
        $info = self::getFeedInfo();
        return $info->done;
    }

    public static function getMinDate()
    {
        $info = self::getFeedInfo();
        return $info->min_date;
    }

    public static function addShow($name, $label = '')
    {
        $info = self::getFeedInfo(true);
        $name = trim($name);
        $pos = array_search($name, $info['shows']);
        if (!empty($name) && $pos === false) {
            $label = trim($label);
            if (!empty($label) && !isset($info['shows'][$label])) {
                $info['shows'][$label] = $name;
            } else {
                $info['shows'][] = $name;
            }
            self::setFeedInfo($info);
        }
    }

    public static function removeShow($name)
    {
        $info = self::getFeedInfo(true);
        $name = trim($name);
        $pos = array_search($name, $info['shows']);
        if (!empty($name) && $pos !== false) {
            unset($info['shows'][$pos]);
            self::setFeedInfo($info);
        }
    }

    public static function addUrlDone($url)
    {
        $info = self::getFeedInfo(true);
        $url = trim($url);
        $pos = array_search($url, $info['done']);
        if (!empty($url) && $pos === false) {
            array_unshift($info['done'], (string)$url);
            self::setFeedInfo($info);
        }
    }

    public static function removeUrlDone($url)
    {
        $info = self::getFeedInfo(true);
        $url = trim($url);
        $pos = array_search($url, $info['done']);
        if (!empty($url) && $pos !== false) {
            unset($info['done'][$pos]);
            $info['done'] = array_values($info['done']);
            self::setFeedInfo($info);
        }
    }

    public static function updateDate($date)
    {
        $info = self::getFeedInfo();
        $info->min_date = date("Y-m-d H:i:s", $date);
        self::setFeedInfo($info);
    }

    public static function emptyDoneList()
    {
        $info = self::getFeedInfo();
        $info->done = array();
        self::setFeedInfo($info);
    }

    private static function getFeedInfo($assoc = false)
    {
        return json_decode(file_get_contents($_ENV['FEED_INFO']), $assoc);
    }

    private static function setFeedInfo($info)
    {
        if (is_writable($_ENV['FEED_INFO'])) {
            file_put_contents($_ENV['FEED_INFO'], json_encode($info));
        }
    }

}
