<?php

require_once dirname(__FILE__) . '/../vendor/autoload.php';
require_once dirname(__FILE__) . '/../vendor/simpletest/simpletest/autorun.php';

use slelorrain\Aushowmatic;

new Aushowmatic\Config(true);

class AllTests extends TestSuite
{

    public function __construct()
    {
        parent::__construct('All tests for Aushowmatic ' . Aushowmatic\Core\Utils::getVersion());

        $this->addFile(dirname(__FILE__) . '/Button_test.php');
        $this->addFile(dirname(__FILE__) . '/FeedInfo_test.php');
        $this->addFile(dirname(__FILE__) . '/Link_test.php');
        $this->addFile(dirname(__FILE__) . '/Subtitle_test.php');
        $this->addFile(dirname(__FILE__) . '/Utils_test.php');
    }
}
