<?php

namespace slelorrain\Aushowmatic\Core;

define('SEARCH_PATH', 'https://www.opensubtitles.org/en/search/sublanguageid-' . $_ENV['SUBTITLES_LANGUAGE'] . '/moviename-');

class Subtitle
{

    const TMP_PATH = '/tmp/';
    const ZIP_FILE = 'tmp.zip';

    public static function download($directories = null)
    {
        if (!file_exists(self::TMP_PATH)) {
            return self::TMP_PATH . ' does not exist';
        }

        if (is_null($directories)) {
            $directories = glob($_ENV['SUBTITLES_SEARCH_PATH'] . '/*', GLOB_ONLYDIR);
        }

        $results = self::searchAndDownloadAll($directories);

        return self::printResults($results);
    }

    public static function removeSubtitles($directory)
    {
        $directory = str_replace('[', '\[', $directory);
        $subtitles = glob($directory . '/*.' . $_ENV['SUBTITLES_EXTENSION']);
        $results = array_map('unlink', $subtitles);

        foreach ($results as $result) {
            if (!$result) return false;
        }

        return true;
    }

    private static function searchAndDownloadAll($directories)
    {
        $results = [];
        
        foreach ($directories as $directory) {
            $directory = str_replace('[', '\[', $directory);
            $videos = glob($directory . '/*.{' . $_ENV['SUBTITLES_SEARCH_EXTENSIONS'] . '}', GLOB_BRACE);
            $subtitles = glob($directory . '/*.' . $_ENV['SUBTITLES_EXTENSION']);

            foreach ($videos as $video) {
                $path_parts = pathinfo($video);
                $subtitle = substr_replace($video , $_ENV['SUBTITLES_EXTENSION'], strrpos($video, '.') + 1);

                if (!in_array($subtitle, $subtitles)) {
                    $results[$video] = self::searchAndDownload($path_parts);
                }
            }
        }

        return $results;
    }

    private static function searchAndDownload($path_parts)
    {
        $download_url = self::getDownloadUrl($path_parts['filename']);

        if (isset($download_url)) {
            $result = copy($download_url, self::TMP_PATH . self::ZIP_FILE);

            if ($result) {
                $result = self::extractZip();

                if ($result) {
                    return self::moveAndClean($path_parts);
                }
            }
        }

        return false;
    }

    private static function getDownloadUrl($search)
    {
        $search = str_replace($_ENV['PREFERRED_FORMAT'], '', $search);
        $search_url = SEARCH_PATH . $search . '/simplexml';
        $search_page = Curl::getPage($search_url);

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($search_page);
        libxml_clear_errors();

        if ($xml && is_object($xml->results->subtitle)) {
            return $xml->results->subtitle->download;
        }
    }

    private static function extractZip()
    {
        return System::unzip(self::TMP_PATH . self::ZIP_FILE, self::TMP_PATH);
    }

    private static function moveAndClean($path_parts)
    {
        $current_subtitle = glob(self::TMP_PATH . '*.' . $_ENV['SUBTITLES_EXTENSION'])[0];
        $new_subtitle = $path_parts['dirname'] . '/' . $path_parts['filename'] . '.' . $_ENV['SUBTITLES_EXTENSION'];
        $result = rename($current_subtitle, $new_subtitle);

        unlink(self::TMP_PATH . self::ZIP_FILE);

        return $result;
    }

    private static function printResults($results)
    {
        if (count($results)) {
            $to_echo = '';

            foreach ($results as $path => $found) {
                $to_echo .= basename($path) . ' => ' . (($found) ? 'Found' : 'Not found') . '<br>';
            }

            $to_echo = 'Subtitles results:<br>' . $to_echo;
        } else {
            $to_echo = 'No subtitle to search.';
        }

        return $to_echo;
    }

}
