<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

date_default_timezone_set('Europe/Luxembourg');

define('FEED_CLASS', 'EZTV'); // Name of a class implementing Feed - DailyTV or EZTV
define('PREFERRED_FORMAT', '720'); // HD or 720
define('TRANSMISSION_CMD', 'transmission-remote -n login:pass');
define('TRANSMISSION_WEB', 'http://192.168.1.42:9091/transmission/web/'); // Transmission Web Interface path

/** DO NO UPDATE CODE BELOW **/

require_once(dirname(__FILE__).'/core/dispatcher.php');
require_once(dirname(__FILE__).'/core/utils.php');
require_once(dirname(__FILE__).'/core/feed.php');
require_once(dirname(__FILE__).'/core/system.php');
require_once(dirname(__FILE__).'/core/transmission.php');

define('DATE_FILE', dirname(__FILE__).'/files/_'.FEED_CLASS.'_date_min.txt');
define('DL_FILE', dirname(__FILE__).'/files/_'.FEED_CLASS.'_done_list.txt');
define('SL_FILE', dirname(__FILE__).'/files/_'.FEED_CLASS.'_show_list.txt');

function __autoload($class){
    $class = strtolower($class);
    $path = dirname(__FILE__).'/feeds/'.$class.'.php';
    if( is_file($path) ) require_once($path);
}
?>