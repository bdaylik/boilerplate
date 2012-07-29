<?php

require_once(dirname(__FILE__) . '/BaseController.php');

class NotfoundController extends BaseController
{
    protected function init()
    {
    }

    public function isLoginRequired()
    {
        return false;
    }

    protected function run()
    {
        $this->setView('notfound');
    }
}
