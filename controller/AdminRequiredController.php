<?php

require_once(dirname(__FILE__) . '/LoginRequiredController.php');

abstract class AdminRequiredController extends LoginRequiredController
{
    public function isAdminRequired()
    {
        return true;
    }
}
