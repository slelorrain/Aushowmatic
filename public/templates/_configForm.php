<?php
use slelorrain\Aushowmatic\Components\Button;
use slelorrain\Aushowmatic\Components\EnvForm;

$subtitlesProviders = array('Addic7ed', 'OpenSubtitles');
?>
<div>Subtitles:</div>
<?= EnvForm::boolean('SUBTITLES_ENABLED') ?>
<br>
<?php if ($_ENV['SUBTITLES_ENABLED'] == 'true') { ?>
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
<div><?= Button::action('Update minimum date', 'updateDate') . Button::action('Empty processed torrents', 'emptyDone', '', '', 'danger') ?></div>
