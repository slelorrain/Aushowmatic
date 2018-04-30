<?php
$autoload = '../vendor/autoload.php';
if (!file_exists($autoload)) die('You must install dependencies');
require_once $autoload;

use slelorrain\Aushowmatic;
use slelorrain\Aushowmatic\Core;
use slelorrain\Aushowmatic\Core\Utils;
use slelorrain\Aushowmatic\Components\Button;
use slelorrain\Aushowmatic\Components\Link;

try {
    new Aushowmatic\Config();
} catch (Exception $e) {
    print('Incorrect configuration:<br>' . $e->getMessage());
}

Core\Dispatcher::dispatch();
$isTurtleActivated = Core\Transmission::isTurtleActivated();
$availableShows = Core\Feed::getAvailableShows();
$subtitlesEnabled = ($_ENV['SUBTITLES_ENABLED'] == 'true');
$subtitlesLanguage = $_ENV['SUBTITLES_CLASS']::getLanguage();
$subtitlesEnabledAndLanguageSet = $subtitlesEnabled && isset($subtitlesLanguage);
$showSystemCommands = ($_ENV['SYSTEM_CMDS_ENABLED'] == 'true');
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
        <li>
            <?= Button::action('&#9632;', 'transmission', 'stop', 'Stop all torrents') ?>
        </li>
        <li>
            <?= Button::action('&#9658;', 'transmission', 'start', 'Start all torrents') ?>
        </li>
    </ul>
    <ul class="yt-button-group">
        <li>
            <?= Button::action('Turtle', 'transmission', 'altSpeedOn', 'Turtle ON', $isTurtleActivated ? 'active forced' : '') ?>
        </li>
        <li>
            <?= Button::action('&infin;', 'transmission', 'altSpeedOff', 'Turtle OFF', !$isTurtleActivated ? 'active forced' : '') ?>
        </li>
    </ul>
    <ul class="yt-button-group">
        <li>
            <?= Button::action('&equiv;', 'transmission', 'listFiles', 'List torrents') ?>
        </li>
        <li>
            <?= Button::out('TWI', $_ENV['TRANSMISSION_WEB'], 'Transmission Web Interface') ?>
        </li>
    </ul>
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
            <li>
                <?= Button::action('Processed links', 'done') ?>
            </li>
            <li>
                <?= Button::action('Added shows', 'shows') ?>
            </li>
        </ul>
        <ul class="yt-button-group left">
            <li>
                <?= Button::show('Add a show', 'add_show') ?>
            </li>
            <li>
                <?= Button::show('Add a torrent', 'add_torrent') ?>
            </li>
        </ul>
        <ul class="yt-button-group right">
            <li>
                <?= Button::action('Preview downloads', 'preview') ?>
            </li>
            <li>
                <?= Button::action('Launch downloads', 'launch', '', '', 'primary') ?>
            </li>
        </ul>
        <?php if ($subtitlesEnabledAndLanguageSet) { ?>
        <ul class="yt-button-group right">
            <li>
                <?= Button::action('Download subtitles', 'subtitles') ?>
            </li>
        </ul>
        <?php } ?>
        <div class="clear"></div>
    </nav>

    <pre id="response"><?= isset($_SESSION['result']) ? $_SESSION['result'] : '' ?></pre>

    <div id="bottom_links">
        <div class="left">
            <?= Button::show('Parameters' , 'hidden_actions_left') ?>
            <div id="hidden_actions_left" class="showable">
            	<?= Button::action('Transmission information', 'transmission', 'sessionInfo') ?>
                <?= Button::action('Disk space usage', 'diskUsage') ?>
        		<?= Button::action('Update minimum date', 'updateDate') ?>
        		<?= Button::action('Empty processed links', 'emptyDone', '', '', 'danger') ?>
            </div>
        </div>
        <?php if ($showSystemCommands) { ?>
            <div class="right">
                <ul class="yt-button-group">
                    <li><?= Button::action('Start Kodi', 'startKodi', '', '', 'primary') ?></li>
                </ul>
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
    Minimum date: <?= Core\FeedInfo::getMinDate() ?>
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
