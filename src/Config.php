<?php

namespace slelorrain\Aushowmatic;

use Dotenv;

class Config
{
    public function __construct()
    {
        $this->initialiseConfig();
    }

    public function initialiseConfig()
    {
        $dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
        $dotenv->load();
        $dotenv->required(['FEED_CLASS', 'SYSTEM_CMDS_ENABLED', 'PREFERRED_FORMAT']);

        date_default_timezone_set($_ENV['TIMEZONE'] ?? 'UTC');
    }
}