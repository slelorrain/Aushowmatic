<?php
$autoload = '../vendor/autoload.php';
if (!file_exists($autoload)) die('You must install dependencies');

require_once $autoload;

use slelorrain\Aushowmatic;
use slelorrain\Aushowmatic\Core;
use slelorrain\Aushowmatic\Core\Utils;
use slelorrain\Aushowmatic\Core\Button;
use slelorrain\Aushowmatic\Core\Link;

new Aushowmatic\Config();

Core\Dispatcher::dispatch();
$isTurtleActivated = Core\System::isTurtleActivated();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1"/>
    <title>Aushowmatic</title>
    <link rel="shortcut icon" type="image/png" href="./assets/favicon.png"/>
    <link rel="apple-touch-icon" href="./assets/favicon-touch.png"/>
    <link rel="stylesheet" type="text/css" href="./assets/css/main.css"/>
    <link rel="stylesheet" type="text/css" href="./assets/css/yt-buttons.min.css"/>
    <link rel="stylesheet" type="text/css" media="only screen and (max-width:1080px)" href="./assets/css/handheld.css"/>
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
            <?= Button::action('&iexcl;', 'transmission', 'info', 'Info') ?>
        </li>
        <li>
            <?= Button::out('TWI', $_ENV['TRANSMISSION_WEB'], 'Transmission Web Interface') ?>
        </li>
    </ul>
</div>

<div id="main_container" class="auto">

    <?php if (!is_writable(FEED_INFO)) { ?>
        <div class="alert">The feed file is not writable. Please update permissions.</div>
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
        <div class="clear"></div>

        <form id="add_show" class="showable" method="post" action="?action=addShow">
            <input id="show_name" name="show_name" type="text" placeholder="Show name or ID"/>
            <input id="show_label" name="show_label" type="text" placeholder="Show label (optional)"/>
            <input id="sumbit_add_show" class="yt-button" type="submit" value="Add"/>
        </form>

        <form id="add_torrent" class="showable" method="post" action="?action=addTorrent">
            <input id="torrent_link" name="torrent_link" type="text" placeholder="Torrent link"/>
            <input id="sumbit_add_torrent" class="yt-button" type="submit" value="Add"/>
        </form>
    </nav>

    <pre id="response"><?= isset($_SESSION['result']) ? $_SESSION['result'] : '' ?></pre>

    <div id="bottom_links">
        <div class="left">
            <?= Button::action('Update min. date', 'updateDate') ?>
            <?= Button::action('Empty processed links', 'emptyDone', '', '', 'danger') ?>
        </div>
        <?php if ($_ENV['SYSTEM_CMDS_ENABLED'] == 'true') { ?>
            <div class="right">
                <?= Button::action('Disk space usage', 'diskUsage') ?>
                <ul class="yt-button-group">
                    <li><?= Button::action('Kodi Status', 'statusKodi') ?></li>
                    <li><?= Button::action('Start Kodi', 'startKodi', '', '', 'primary') ?></li>
                </ul>
                <?= Button::show('&#9660;', 'hidden_actions') ?>

                <div id="hidden_actions" class="showable">
                    <?= Button::action('Kill Kodi', 'killKodi', '', '', 'danger big') ?>
                    <?= Button::action('Reboot', 'reboot', '', '', 'danger big') ?>
                    <?= Button::action('Shutdown', 'shutdown', '', '', 'danger big') ?>
                </div>
            </div>
        <?php } ?>
        <div class="clear"></div>
    </div>

</div>

<footer>
    Min. date : <?= Core\FeedInfo::getMinDate() ?>
    / Generated in <?= $_SESSION['generated_in'] ?>s
    / <?= Utils::getVersion() ?>
    <?php if (Utils::hasUpdateAvailable()) { ?>
        <?= Link::out('Update available', 'https://github.com/slelorrain/Aushowmatic/releases', 'GitHub') ?>
    <?php } ?>
</footer>

<script src="./assets/js/main.js"></script>

</body>
</html>
