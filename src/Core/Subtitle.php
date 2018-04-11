<?php

namespace slelorrain\Aushowmatic\Core;

abstract class Subtitle implements SubtitleInterface
{

    const TMP_PATH = '/tmp/';
    const TMP_FILE = 'tmp_subtitles';

    public static function download($directories = null)
    {
        if (!file_exists(self::TMP_PATH)) {
            return 'Error: ' . self::TMP_PATH . ' does not exist';
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

    public static function uploadSubtitle($video, $file = null)
    {
        if (isset($file['name'])) {
            $path_parts = pathinfo($video);
            $destination = self::TMP_PATH . basename($file['name']);
            $moved = move_uploaded_file($file['tmp_name'], $destination);

            if ($moved) {
                if (self::move($path_parts)) {
                    return 'File uploaded.';
                } else {
                    unlink($destination);
                    return 'Error: File not uploaded.';
                }
            } else {
                return 'Error: File is not valid or cannot be moved.';
            }
        } else {
            return 'Error: No file provided.';
        }
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
        $download_url = static::getDownloadUrl($path_parts['filename']);

        if (self::getContent($download_url) && static::afterDownload()) {
            return self::move($path_parts);
        }

        return false;
    }

    private static function getContent($download_url)
    {
        if (isset($download_url)) {
            $content = Curl::getPage($download_url, USER_AGENT, SEARCH_PATH);

            if ($content && self::isContentValid($content)) {
                return file_put_contents(self::TMP_PATH . self::TMP_FILE, $content);
            }
        }

        return false;
    }

    private static function isContentValid($content)
    {
        $test = '<!DOCTYPE';
        return (substr($content, 0, strlen($test)) !== $test);
    }

    private static function move($path_parts)
    {
        $current_subtitle = glob(self::TMP_PATH . '*.' . $_ENV['SUBTITLES_EXTENSION'])[0];
        $new_subtitle = $path_parts['dirname'] . '/' . $path_parts['filename'] . '.' . $_ENV['SUBTITLES_EXTENSION'];
        return rename($current_subtitle, $new_subtitle);
    }

    private static function printResults($results)
    {
        if (count($results)) {
            $to_echo = '';

            foreach ($results as $path => $found) {
                $to_echo .= basename($path) . ' => ' . (($found) ? 'Found' : 'Not found') . PHP_EOL;
            }

            $to_echo = 'Subtitles results:' . PHP_EOL . $to_echo;
        } else {
            $to_echo = 'No subtitle to search.';
        }

        return $to_echo;
    }

}
