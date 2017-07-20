<?php

use slelorrain\Aushowmatic\Core\Utils;

class TestOfUtils extends UnitTestCase
{

    public function testGetVersion()
    {
        $this->assertPattern('/v\d+(\.\d+)*$/', Utils::getVersion());
    }

}
