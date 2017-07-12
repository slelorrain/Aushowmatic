<?php

require_once 'vendor/autoload.php';

use slelorrain\Aushowmatic;

new Aushowmatic\Config();

Aushowmatic\Core\Utils::launchDownloads();
