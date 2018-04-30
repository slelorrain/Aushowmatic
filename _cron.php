<?php
require_once 'vendor/autoload.php';

use slelorrain\Aushowmatic\Config;
use slelorrain\Aushowmatic\Core\Utils;

new Config();
Utils::launchDownloads();
