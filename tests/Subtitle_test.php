<?php

use slelorrain\Aushowmatic\Core\Subtitle;

class TestOfSubtitle extends UnitTestCase
{

    public function testRemoveSubtitles()
    {
        $folder = '/tmp';

        // Check initial state (no subtitles)
        $this->assertEqual(glob($folder . '/*.' . $_ENV['SUBTITLES_EXTENSION']), array());

        // Create a subtitles files
        fopen($folder . '/fileToRemove.srt', 'w');
        fopen($folder . '/fileToRemove2.srt', 'w');

        // Check intermediate state (existing subtitles)
        $this->assertNotEqual(glob($folder . '/*.' . $_ENV['SUBTITLES_EXTENSION']), array());

        // Remove subtitles and check final state (no subtitles)
        Subtitle::removeSubtitles($folder);
        $this->assertEqual(glob($folder . '/*.' . $_ENV['SUBTITLES_EXTENSION']), array());
    }

}
