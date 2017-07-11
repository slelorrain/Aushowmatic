<?php
$autoload = '../vendor/autoload.php';
if (!file_exists($autoload)) die('You must install dependencies');

require_once $autoload;

use slelorrain\Aushowmatic;
use slelorrain\Aushowmatic\Core;
use slelorrain\Aushowmatic\Core\Utils;

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
            <a href="?a=transmission&param=stop"
               class="yt-button" title="Stop all torrents">&#9632;</a>
        </li>
        <li>
            <a href="?a=transmission&param=start"
               class="yt-button" title="Start all torrents">&#9658;</a>
        </li>
    </ul>
    <ul class="yt-button-group">
        <li>
            <a href="?a=transmission&param=altSpeedOn"
               class="yt-button <?= $isTurtleActivated ? 'active forced' : '' ?>" title="Turtle ON">Turtle</a>
        <li>
            <a href="?a=transmission&param=altSpeedOff"
               class="yt-button  <?= !$isTurtleActivated ? 'active forced' : '' ?>" title="Turtle OFF">&infin;</a>
        </li>
    </ul>
    <ul class="yt-button-group">
        <li>
            <a href="?a=transmission&param=listFiles"
               class="yt-button" title="List torrents">&equiv;</a>
        </li>
        <li>
            <a href="?a=transmission&param=info"
               class="yt-button" title="Info">&iexcl;</a>
        </li>
        <li>
            <a href="<?= $_ENV['TRANSMISSION_WEB'] ?>" target="_blank"
               class="yt-button" title="Transmission Web Interface">TWI</a>
        </li>
    </ul>
</div>

<div id="main_container" class="auto">

    <?php if (!is_writable($_ENV['FEED_INFO'])) { ?>
        <div class="alert">The feed file is not writable. Please update permissions.</div>
    <?php } ?>

    <nav>
        <ul class="yt-button-group left">
            <li>
                <a href="?a=done"
                   class="yt-button">Processed links</a>
            </li>
            <li>
                <a href="?a=shows"
                   class="yt-button">Added shows</a>
            </li>
        </ul>
        <ul class="yt-button-group left">
            <li>
                <a id="show_add_show" href="#add_show" class="yt-button showSomething">Add a show</a>
            </li>
            <li>
                <a id="show_add_torrent" href="#add_torrent" class="yt-button showSomething">Add a torrent</a>
            </li>
        </ul>

        <ul class="yt-button-group right">
            <li>
                <a href="?a=preview"
                   class="yt-button">Preview downloads</a>
            </li>
            <li>
                <a href="?a=launch"
                   class="yt-button primary">Launch downloads</a>
            </li>
        </ul>
        <div class="clear"></div>

        <form id="add_show" class="showable" method="post" action="?a=addShow">
            <input id="show_name" name="show_name" type="text" placeholder="Show name or ID"/>
            <input id="show_label" name="show_label" type="text" placeholder="Show label (optional)"/>
            <input id="sumbit_add_show" class="yt-button" type="submit" value="Add"/>
        </form>

        <form id="add_torrent" class="showable" method="post" action="?a=addTorrent">
            <input id="torrent_link" name="torrent_link" type="text" placeholder="Torrent link"/>
            <input id="sumbit_add_torrent" class="yt-button" type="submit" value="Add"/>
        </form>
    </nav>

    <pre id="response"><?= isset($_SESSION['result']) ? $_SESSION['result'] : '' ?></pre>

    <div id="bottom_links">
        <div class="left">
            <a href="?a=updateDate" class="yt-button">Update min. date</a>
            <a href="?a=emptyDone" class="yt-button danger">Empty processed links</a>
        </div>
        <?php if ($_ENV['SYSTEM_CMDS_ENABLED'] == 'true') { ?>
            <div class="right">
                <a href="?a=diskUsage" class="yt-button">Disk space usage</a>
                <ul class="yt-button-group">
                    <li><a href="?a=statusKodi" class="yt-button">Kodi Status</a></li>
                    <li><a href="?a=startKodi" class="yt-button primary">Start Kodi</a></li>
                </ul>
                <a id="show_hidden_actions" href="#hidden_actions" class="yt-button showSomething">&#9660;</a>

                <div id="hidden_actions" class="showable">
                    <a href="?a=killKodi" class="yt-button danger big">Kill Kodi</a>
                    <a href="?a=reboot" class="yt-button danger big">Reboot</a>
                    <a href="?a=shutdown" class="yt-button danger big">Shutdown</a>
                </div>
            </div>
        <?php } ?>
        <div class="clear"></div>
    </div>

</div>

<footer>
    <span>Min. date : <?= Utils::getMinDate() ?> / Generated in <?= $_SESSION['generated_in'] ?>s</span>
</footer>

<script src="./assets/js/main.js"></script>

</body>
</html>
