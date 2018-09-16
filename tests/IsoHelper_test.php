<?php

use slelorrain\Aushowmatic\Core\IsoHelper;

class TestOfIsoHelper extends UnitTestCase
{

    public function testEnglishNamesByIso6392Code()
    {
        $this->assertEqual(IsoHelper::getEnglishNamesByIso6392Code('eng'), array('0' => 'English'));

        $this->assertNull(IsoHelper::getEnglishNamesByIso6392Code(''));
    }
}
