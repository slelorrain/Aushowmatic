<?php
$autoload = '../vendor/autoload.php';
if (!file_exists($autoload)) die('You must install dependencies');
require_once $autoload;

use slelorrain\Aushowmatic\Config;
use slelorrain\Aushowmatic\Core\Dispatcher;
use slelorrain\Aushowmatic\Core\FeedInfo;
use slelorrain\Aushowmatic\Core\System;
use slelorrain\Aushowmatic\Core\Transmission;
use slelorrain\Aushowmatic\Core\Utils;
use slelorrain\Aushowmatic\Components\Button;
use slelorrain\Aushowmatic\Components\Link;

try {
    new Config();
    Dispatcher::dispatch();

    $isTurtleActivated = Transmission::isTurtleActivated();
    $isKodiStarted = System::isKodiStarted();
    $subtitlesEnabled = ($_ENV['SUBTITLES_ENABLED'] == 'true');
    $subtitlesLanguage = $_ENV['SUBTITLES_CLASS']::getLanguage();
    $subtitlesEnabledAndLanguageSet = $subtitlesEnabled && isset($subtitlesLanguage);
    $showSystemCommands = ($_ENV['SYSTEM_CMDS_ENABLED'] == 'true');
} catch (Exception $e) {
    die('Exception:<br>' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1"/>
    <title>Aushowmatic</title>
    <link rel="shortcut icon" type="image/png" href="./assets/favicon.png"/>
    <link rel="apple-touch-icon" href="./assets/favicon-touch.png"/>
    <link rel="stylesheet" type="text/css" href="./assets/css/all.css"/>
    <meta name="viewport" content="width=device-width, height=device-height, minimum-scale=1, maximum-scale=1"/>
</head>
<body>

<header>
    <h1>Aushowmatic</h1>
</header>

<div id="remote">
    <ul class="yt-button-group">
        <li><?= Button::action('&#9632;', 'transmission', 'stop', 'Stop all torrents') ?></li>
        <li><?= Button::action('&#9658;', 'transmission', 'start', 'Start all torrents') ?></li>
    </ul>
    <ul class="yt-button-group">
        <li><?= Button::action('Turtle', 'transmission', 'altSpeedOn', 'Turtle ON', $isTurtleActivated ? 'active forced' : '') ?></li>
        <li><?= Button::action('&infin;', 'transmission', 'altSpeedOff', 'Turtle OFF', !$isTurtleActivated ? 'active forced' : '') ?></li>
    </ul>
    <?php if(!$isKodiStarted) { ?>
        <ul class="yt-button-group">
            <li><?= Button::action('Start Kodi', 'startKodi', '', '', 'primary') ?></li>
        </ul>
    <?php } ?>
</div>

<div id="main_container" class="auto">

    <?php if (!is_writable(FEED_INFO)) { ?>
        <div class="alert">The feed file is not writable. Please update permissions of <?= FEED_INFO ?>.</div>
    <?php } ?>

    <?php if ($subtitlesEnabled && !isset($subtitlesLanguage)) { ?>
        <div class="alert">The subtitles language configuration is incorrect. Please update SUBTITLES_LANGUAGE.</div>
    <?php } ?>

    <nav>
        <ul class="yt-button-group left">
            <li><?= Button::action('Torrents', 'transmission', 'listFiles') ?></li>
        </ul>
        <ul class="yt-button-group left">
            <li><?= Button::action('Shows', 'shows') ?></li>
        </ul>
        <ul class="yt-button-group right">
            <li><?= Button::action('Preview downloads', 'preview') ?></li>
            <li><?= Button::action('Launch downloads', 'launch', '', '', 'primary') ?></li>
        </ul>
        <?php if ($subtitlesEnabledAndLanguageSet) { ?>
            <ul class="yt-button-group right">
                <li><?= Button::action('Download subtitles', 'subtitles') ?></li>
            </ul>
        <?php } ?>
        <div class="clear"></div>
    </nav>

    <pre id="response"><?= isset($_SESSION['result']) ? $_SESSION['result'] : '' ?></pre>

    <div id="bottom_links">
        <div class="left">
            <?= Button::show('Info' , 'hidden_actions_left') ?>
            <div id="hidden_actions_left" class="showable">
                <ul class="yt-button-group">
                    <li><?= Button::out('TWI', $_ENV['TRANSMISSION_WEB'], 'Transmission Web Interface') ?></li>
                    <li><?= Button::action('Transmission information', 'transmission', 'sessionInfo') ?></li>
                </ul>
                <?= Button::action('Processed links', 'done') ?>
                <?= Button::action('Disk space usage', 'diskUsage') ?>
            </div>
        </div>
        <?php if ($showSystemCommands) { ?>
            <div class="right">
                <?= Button::show('&#9660;', 'hidden_actions_right') ?>
                <div id="hidden_actions_right" class="showable">
                    <?= Button::action('Reboot', 'reboot', '', '', 'danger') ?>
                    <?= Button::action('Shutdown', 'shutdown', '', '', 'danger') ?>
                </div>
            </div>
        <?php } ?>
        <div class="clear"></div>
    </div>

</div>

<footer>
    Minimum date: <?= FeedInfo::getMinDate() ?>
    <?php if ($subtitlesEnabledAndLanguageSet) { ?>
        / Subtitles language: <?= $_ENV['SUBTITLES_LANGUAGE'] ?>
    <?php } ?>
    / Generated in <?= $_SESSION['generated_in'] ?>s
    / <?= Utils::getVersion() ?>
    <?php if (Utils::hasUpdateAvailable()) { ?>
        <?= Link::out('Update available', 'https://github.com/slelorrain/Aushowmatic/releases', 'GitHub') ?>
    <?php } ?>
</footer>

<script src="./assets/js/main.js"></script>

</body>
</html>
