<?php
$autoload = '../vendor/autoload.php';
if (!file_exists($autoload)) die('You must install dependencies');
require_once $autoload;

use slelorrain\Aushowmatic\Config;
use slelorrain\Aushowmatic\Core\Dispatcher;
use slelorrain\Aushowmatic\Core\FeedInfo;
use slelorrain\Aushowmatic\Core\Utils;
use slelorrain\Aushowmatic\Components\Button;
use slelorrain\Aushowmatic\Components\Link;
use slelorrain\Aushowmatic\Components\Template;

try {
    new Config();
    Dispatcher::dispatch();

    $subtitlesEnabled = Config::isEnabled('SUBTITLES_ENABLED');
    $subtitlesLanguage = $_ENV['SUBTITLES_CLASS']::getLanguage();
    $subtitlesEnabledAndLanguageSet = $subtitlesEnabled && isset($subtitlesLanguage);
    $showSystemCommands = Config::isEnabled('SYSTEM_CMDS_ENABLED');
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
    <link rel="manifest" href="./manifest.json"/>
    <meta name="theme-color" content="#778899"/>
    <meta name="mobile-web-app-capable" content="yes"/>
    <meta name="viewport" content="width=device-width, height=device-height, minimum-scale=1, maximum-scale=1"/>
</head>
<body>

<header>
    <h1>Aushowmatic</h1>
</header>

<?= Template::get('remote') ?>

<div id="main_container" class="auto">

    <?php if (!is_writable($_ENV['FEED_INFO'])) { ?>
        <div class="alert">The feed file is not writable. Please update permissions of <?= $_ENV['FEED_INFO'] ?>.</div>
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
            <?= Button::show('Info', 'hidden_actions_left') ?>
            <?= Button::action('Config', 'configuration') ?>
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
