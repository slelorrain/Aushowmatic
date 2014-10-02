<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Europe/Luxembourg');

// User configuration
$user_config_path = dirname(__FILE__) . '/user_config.json';
if( file_exists($user_config_path) ){
    $user_config = json_decode(file_get_contents($user_config_path));
    foreach( $user_config as $key => $val ){
        define($key, $val);
    }
}

// Default configuration
$default_config_path = dirname(__FILE__) . '/default_config.json';
if( file_exists($default_config_path) ){
    $default_config = json_decode(file_get_contents($default_config_path));
    foreach( $default_config as $key => $val ){
        if( !defined($key) ) define($key, $val);
    }
}
?>
