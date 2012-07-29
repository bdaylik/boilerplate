<?php

require_once(dirname(__FILE__) . '/BaseController.php');
require_once(dirname(__FILE__) . '/../service/LoginService.php');
require_once(dirname(__FILE__) . '/../service/RedirectService.php');

class LoginController extends BaseController
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
        if ($this->commons->isLoggedIn) {
            //Redirect if already logged in
            $this->redirectService->redirect();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //If form post is done check for username and password
            $username = $this->commons->post['username'];
            $password = $this->commons->post['password'];

            $errorMessage = false;
            //Check if exists via login service
            try {
                $this->loginService->loginUser($username, $password);
            } catch (UserNotFoundException $anfe) {
                $errorMessage = 'Kullanıcı adınız ya da şifreniz hatalı';
            }

            if ($errorMessage) {
                $this->showLoginForm($username, $errorMessage);
            } else {
                //Success
                $this->redirectService->redirect();
            }
        } else {
            //Just show the login form
            $this->showLoginForm();
        }
    }

    private function showLoginForm($username = '', $errorMessage = '')
    {
        $loggedOut = isset($this->commons->get['loggedout']) ? $this->commons->get['loggedout'] : false;

        $this->addViewParameter('username', $username);
        $this->addViewParameter('errorMessage', $errorMessage);
        $this->addViewParameter('loggedOut', $loggedOut);

        $this->addViewPart('header', 'header');
        $this->addViewPart('navbar', 'navbar');
        $this->addViewPart('footer', 'footer');

        $this->setView('login');
    }
}
