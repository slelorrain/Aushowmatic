<?php

use slelorrain\Aushowmatic\Components\Button;

class TestOfButton extends UnitTestCase
{

    public function testAction()
    {
        $action = Button::action('Content', 'action');

        $this->assertPattern('/>Content</', $action);
        $this->assertPattern('/\?action=action/', $action);
        $this->assertNoPattern('/class="yt-button showSomething"/', $action);
        $this->assertNoPattern('/target="_blank"/', $action);
    }

    public function testActionWithParameter()
    {
        $action = Button::action('Content', 'action', 'parameter');

        $this->assertPattern('/>Content</', $action);
        $this->assertPattern('/\?action=action/', $action);
        $this->assertPattern('/\&parameter=parameter/', $action);
        $this->assertNoPattern('/class="yt-button showSomething"/', $action);
        $this->assertNoPattern('/target="_blank"/', $action);
    }

    public function testShow()
    {
        $show = Button::show('Content', 'href');

        $this->assertPattern('/>Content</', $show);
        $this->assertPattern('/id="show_/', $show);
        $this->assertPattern('/href="#href"/', $show);
        $this->assertPattern('/class="yt-button showSomething"/', $show);
        $this->assertNoPattern('/target="_blank"/', $show);
    }

    public function testOut()
    {
        $out = Button::out('Content', 'href');

        $this->assertPattern('/>Content</', $out);
        $this->assertPattern('/target="_blank"/', $out);
        $this->assertNoPattern('/class="yt-button showSomething"/', $out);
    }
}
