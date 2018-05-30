<?php
use slelorrain\Aushowmatic\Config;
use slelorrain\Aushowmatic\Components\Button;
use slelorrain\Aushowmatic\Components\EnvForm;
use slelorrain\Aushowmatic\Core\Feed;
use slelorrain\Aushowmatic\Core\Resolution;
use slelorrain\Aushowmatic\Core\Subtitle;

$feeds = Feed::getChoices();
$preferredFormats = Resolution::getChoices();
$subtitlesProviders = Subtitle::getChoices();
?>
<div>Feed:</div>
<?= EnvForm::normal('FEED_NAME', $feeds) ?>
<br>
<div>Preferred format:</div>
<?= EnvForm::normal('PREFERRED_FORMAT', $preferredFormats) ?>
<br>
<div>Subtitles:</div>
<?= EnvForm::boolean('SUBTITLES_ENABLED') ?>
<br>
<?php if (Config::isEnabled('SUBTITLES_ENABLED')) { ?>
    <div>Subtitles provider:</div>
    <?= EnvForm::normal('SUBTITLES_NAME', $subtitlesProviders) ?>
    <br>
<?php } ?>
<div>System commands:</div>
<?= EnvForm::boolean('SYSTEM_CMDS_ENABLED') ?>
<br>
<div>Debug:</div>
<?= EnvForm::boolean('DEBUG') ?>
<br>
<hr>
<br>
<div><?= Button::action('Update minimum date', 'updateDate') . Button::action('Empty processed torrents', 'emptyDone', '', '', 'danger') ?></div>
