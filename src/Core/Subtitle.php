<?php

namespace slelorrain\Aushowmatic\Core;

abstract class Subtitle implements SubtitleInterface, ChoosableInterface
{

    const TMP_PATH = '/tmp/';
    const TMP_FILE = 'tmp_subtitles';

    const SUBTITLES_PATH = APP_BASE_PATH . 'src/Subtitles/';

    public static function getChoices()
    {
        $subtitles = array();
        $files = array_diff(scandir(self::SUBTITLES_PATH), array('.', '..'));

        foreach ($files as $file) {
            $info = pathinfo(self::SUBTITLES_PATH . $file);
            $isSubclass = is_subclass_of(SUBTITLES_NAMESPACE . $info['filename'], get_class());

            if (is_file(self::SUBTITLES_PATH . $file) && $isSubclass) {
                $subtitles[] = $info['filename'];
            }
        }

        return $subtitles;
    }

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
            $pathParts = pathinfo($video);
            $destination = self::TMP_PATH . basename($file['name']);
            $moved = move_uploaded_file($file['tmp_name'], $destination);

            if ($moved) {
                if (self::move($pathParts)) {
                    return 'File uploaded';
                } else {
                    unlink($destination);
                    return 'Error: File not uploaded';
                }
            } else {
                return 'Error: File is not valid or cannot be moved';
            }
        } else {
            return 'Error: No file provided';
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
                $pathParts = pathinfo($video);
                $subtitle = substr_replace($video , $_ENV['SUBTITLES_EXTENSION'], strrpos($video, '.') + 1);

                if (!in_array($subtitle, $subtitles)) {
                    $results[$video] = self::searchAndDownload($pathParts);
                }
            }
        }

        return $results;
    }

    private static function searchAndDownload($pathParts)
    {
        $downloadUrl = static::getDownloadUrl($pathParts['filename']);

        if (self::getContent($downloadUrl) && static::afterDownload()) {
            return self::move($pathParts);
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

    private static function move($pathParts)
    {
        $currentSubtitle = glob(self::TMP_PATH . '*.' . $_ENV['SUBTITLES_EXTENSION'])[0];
        $newSubtitle = $pathParts['dirname'] . '/' . $pathParts['filename'] . '.' . $_ENV['SUBTITLES_EXTENSION'];
        return rename($currentSubtitle, $newSubtitle);
    }

    private static function printResults($results)
    {
        if (count($results)) {
            $toEcho = '';

            foreach ($results as $path => $found) {
                $toEcho .= basename($path) . ' => ' . (($found) ? 'Found' : 'Not found') . PHP_EOL;
            }

            $toEcho = 'Subtitles results:' . PHP_EOL . $toEcho;
        } else {
            $toEcho = 'No subtitle to search';
        }

        return $toEcho;
    }

}
