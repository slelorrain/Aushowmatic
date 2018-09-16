<?php

use slelorrain\Aushowmatic\Components\Link;

class TestOfLink extends UnitTestCase
{

    public function testAction()
    {
        $action = Link::action('Content', 'action');

        $this->assertPattern('/>Content</', $action);
        $this->assertPattern('/\?action=action/', $action);
        $this->assertNoPattern('/class="showSomething"/', $action);
        $this->assertNoPattern('/target="_blank"/', $action);
    }

    public function testActionWithParameter()
    {
        $action = Link::action('Content', 'action', 'parameter');

        $this->assertPattern('/>Content</', $action);
        $this->assertPattern('/\?action=action/', $action);
        $this->assertPattern('/\&parameter=parameter/', $action);
        $this->assertNoPattern('/class="showSomething"/', $action);
        $this->assertNoPattern('/target="_blank"/', $action);
    }

    public function testShow()
    {
        $show = Link::show('Content', 'href');

        $this->assertPattern('/>Content</', $show);
        $this->assertPattern('/id="show_/', $show);
        $this->assertPattern('/href="#href"/', $show);
        $this->assertPattern('/class="showSomething"/', $show);
        $this->assertNoPattern('/target="_blank"/', $show);
    }

    public function testOut()
    {
        $out = Link::out('Content', 'href');

        $this->assertPattern('/>Content</', $out);
        $this->assertPattern('/target="_blank"/', $out);
        $this->assertNoPattern('/class="showSomething"/', $out);
    }
}
