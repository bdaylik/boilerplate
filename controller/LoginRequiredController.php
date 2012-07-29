<?php

require_once(dirname(__FILE__) . '/BaseController.php');

abstract class LoginRequiredController extends BaseController
{
    public function isLoginRequired()
    {
        return true;
    }
}
