<?php

use slelorrain\Aushowmatic\Core\FeedInfo;

class TestOfFeedInfo extends UnitTestCase
{

    public function testAddShowRemoveShowAndGetShowList()
    {
        // Check initial state
        $this->assertEqual((array) FeedInfo::getShowList(), array('Game of Thrones' => '262'));

        // Check add with name and label
        FeedInfo::addShow('123', 'Foo');
        $this->assertEqual((array) FeedInfo::getShowList(), array('Game of Thrones' => '262', 'Foo' => '123'));

        // Check that second add with same name and label is prevented
        FeedInfo::addShow('123', 'Foo');
        $this->assertEqual((array) FeedInfo::getShowList(), array('Game of Thrones' => '262', 'Foo' => '123'));

        // Check remove after add with name and label
        FeedInfo::removeShow('123');
        $this->assertEqual((array) FeedInfo::getShowList(), array('Game of Thrones' => '262'));

        // Check add with name only
        FeedInfo::addShow('456');
        $this->assertEqual(count(array_keys((array) FeedInfo::getShowList(), '456')), 1);

        // Check that second add with same name only is prevented
        FeedInfo::addShow('456');
        $this->assertEqual(count(array_keys((array) FeedInfo::getShowList(), '456')), 1);

        // Check remove after add with name only
        FeedInfo::removeShow('456');
        $this->assertEqual((array) FeedInfo::getShowList(), array('Game of Thrones' => '262'));

        // Check that add with empty name is prevented
        FeedInfo::addShow(' ');
        $this->assertEqual((array) FeedInfo::getShowList(), array('Game of Thrones' => '262'));

        // Check that add with empty name and label is prevented
        FeedInfo::addShow(' ', ' ');
        $this->assertEqual((array) FeedInfo::getShowList(), array('Game of Thrones' => '262'));
    }

    public function testAddUrlDoneRemoveUrlDoneAndGetDoneList()
    {
        // Check initial state
        $this->assertEqual(FeedInfo::getDoneList(), array());

        // Check add
        FeedInfo::addUrlDone('http://foo.torrent');
        $this->assertEqual(FeedInfo::getDoneList(), array('http://foo.torrent'));

        // Check that second add with same value is prevented
        FeedInfo::addUrlDone('http://foo.torrent');
        $this->assertEqual(FeedInfo::getDoneList(), array('http://foo.torrent'));

        // Check remove
        FeedInfo::removeUrlDone('http://foo.torrent');
        $this->assertEqual(FeedInfo::getDoneList(), array());

        // Check that add with empty value is prevented
        FeedInfo::addUrlDone(' ');
        $this->assertEqual(FeedInfo::getDoneList(), array());
    }

    public function testEmptyDoneList()
    {
        // Check initial state
        $this->assertEqual(FeedInfo::getDoneList(), array());

        // Check add
        FeedInfo::addUrlDone('http://foo.torrent');
        $this->assertEqual(FeedInfo::getDoneList(), array('http://foo.torrent'));

        // Check add with another value
        FeedInfo::addUrlDone('http://foobar.torrent');
        $this->assertEqual(FeedInfo::getDoneList(), array('http://foobar.torrent', 'http://foo.torrent'));

        // Check empty
        FeedInfo::emptyDoneList();
        $this->assertEqual(FeedInfo::getDoneList(), array());
    }

    public function testGetMinDateAndUpdateDate()
    {
        // Check initial state
        $this->assertEqual(FeedInfo::getMinDate(), '2016-01-01 00:00:00');

        // Check update
        FeedInfo::updateDate(1483228800);
        $this->assertEqual(FeedInfo::getMinDate(), '2017-01-01 00:00:00');

        // Reset to initial state
        FeedInfo::updateDate(1451606400);
        $this->assertEqual(FeedInfo::getMinDate(), '2016-01-01 00:00:00');
    }

}
