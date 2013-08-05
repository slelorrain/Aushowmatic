<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

date_default_timezone_set('Europe/Luxembourg');

define('FEED_CLASS', 'EZTV'); // Name of a class implementing Feed - DailyTV or EZTV
define('PREFERRED_FORMAT', '720'); // HD or 720
define('TRANSMISSION_CMD', 'transmission-remote -n login:pass');
define('TRANSMISSION_WEB', 'http://192.168.1.42:9091/transmission/web/'); // Transmission Web Interface path
?>