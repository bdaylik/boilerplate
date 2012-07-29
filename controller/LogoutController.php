<?php

require_once(dirname(__FILE__) . '/BaseController.php');
require_once(dirname(__FILE__) . '/../service/LoginService.php');

class LogoutController extends BaseController
{
    /**
     * @var LoginService
     */
    private $loginService;

    protected function init()
    {
        $this->loginService = new LoginService($this->em);
    }

    public function isLoginRequired()
    {
        return false;
    }

    protected function run()
    {
        $this->loginService->logoutUser();
        $this->redirectService->redirect('login',null,array('loggedout' => 'true'));
    }
}
