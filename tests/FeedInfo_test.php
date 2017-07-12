<?php

use slelorrain\Aushowmatic\Core\FeedInfo;

class TestOfFeedInfo extends UnitTestCase
{

    public function testAddShowRemoveShowAndGetShowList()
    {
        $this->assertEqual((array) FeedInfo::getShowList(), array('Game of Thrones' => '262'));

        FeedInfo::addShow('123', 'Foo');
        $this->assertEqual((array) FeedInfo::getShowList(), array('Game of Thrones' => '262', 'Foo' => '123'));

        FeedInfo::removeShow('123');
        $this->assertEqual((array) FeedInfo::getShowList(), array('Game of Thrones' => '262'));

        FeedInfo::addShow('456');
        $this->assertTrue(array_search('456', (array) FeedInfo::getShowList()) !== FALSE);

        FeedInfo::removeShow('456');
        $this->assertEqual((array) FeedInfo::getShowList(), array('Game of Thrones' => '262'));
    }

    public function testAddUrlDoneRemoveUrlDoneAndGetDoneList()
    {
        $this->assertEqual(FeedInfo::getDoneList(), array());

        FeedInfo::addUrlDone('http://foo.torrent');
        $this->assertEqual(FeedInfo::getDoneList(), array('http://foo.torrent'));

        FeedInfo::removeUrlDone('http://foo.torrent');
        $this->assertEqual(FeedInfo::getDoneList(), array());
    }

    public function testEmptyDoneList()
    {
        $this->assertEqual(FeedInfo::getDoneList(), array());

        FeedInfo::addUrlDone('http://foo.torrent');
        $this->assertEqual(FeedInfo::getDoneList(), array('http://foo.torrent'));

        FeedInfo::addUrlDone('http://foobar.torrent');
        $this->assertEqual(FeedInfo::getDoneList(), array('http://foobar.torrent', 'http://foo.torrent'));

        FeedInfo::emptyDoneList();
        $this->assertEqual(FeedInfo::getDoneList(), array());
    }

    public function testGetMinDateAndUpdateDate()
    {
        $this->assertEqual(FeedInfo::getMinDate(), '2016-01-01 00:00:00');

        FeedInfo::updateDate(1483228800);
        $this->assertEqual(FeedInfo::getMinDate(), '2017-01-01 00:00:00');

        FeedInfo::updateDate(1451606400);
        $this->assertEqual(FeedInfo::getMinDate(), '2016-01-01 00:00:00');
    }

}
