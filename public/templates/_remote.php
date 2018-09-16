<?php
use slelorrain\Aushowmatic\Components\Button;
use slelorrain\Aushowmatic\Core\System;
use slelorrain\Aushowmatic\Core\Transmission;

$isTurtleActivated = Transmission::isTurtleActivated();
$isKodiStarted = System::isKodiStarted();
?>
<div id="remote">
    <ul class="yt-button-group">
        <li><?= Button::action('&#9632;', 'transmission', 'stop', 'Stop all torrents') ?></li>
        <li><?= Button::action('&#9658;', 'transmission', 'start', 'Start all torrents') ?></li>
    </ul>
    <ul class="yt-button-group">
        <li><?= Button::action('Turtle', 'transmission', 'altSpeedOn', 'Turtle ON', $isTurtleActivated ? 'active forced' : '') ?></li>
        <li><?= Button::action('&infin;', 'transmission', 'altSpeedOff', 'Turtle OFF', !$isTurtleActivated ? 'active forced' : '') ?></li>
    </ul>
    <?php if (!$isKodiStarted) { ?>
        <ul class="yt-button-group">
            <li><?= Button::action('Start Kodi', 'startKodi', '', '', 'primary') ?></li>
        </ul>
    <?php } ?>
</div>
