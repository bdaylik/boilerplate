<?php
session_start();

require_once dirname(__FILE__) . '/config/application.php';
require_once dirname(__FILE__) . '/config/config.php';
require_once dirname(__FILE__) . '/helper/EntityManagerFactory.php';
require_once(dirname(__FILE__) . '/Router.php');
require_once(dirname(__FILE__) . '/helper/Commons.php');
require_once(dirname(__FILE__) . '/service/LoginService.php');

class Init
{
    /**
     * @var array
     */
    protected $params;
    /**
     * @var string
     */
    protected $controllerName;
    /**
     * @var string
     */
    protected $methodName;
    /**
     * @var Commons
     */
    protected $commons;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var LoginService
     */
    private $loginService;

    public function __construct(&$em)
    {
        $this->em = $em;
        $this->loginService = new LoginService($em);
    }

    /**
     * @param $url
     */
    public function go($url)
    {
        list($controllerName, $methodName) = $this->computeControllerAndMethodNames($url);
        $this->controllerName = $controllerName;
        $this->methodName = $methodName;
        $this->commons = $this->createCommons($url);
        $this->route();
    }

    public function computeControllerAndMethodNames($url)
    {
        preg_match('/index.php\/([^\/\?][^\/\?]*)(\/([^\/\?][^\/\?]*))*/', $url, $matches);

        $controllerName = null;
        if (isset($matches[1])) {
            $controllerName = $matches[1];
            $controllerName = ucfirst(strtolower($controllerName));
        }

        $methodName = DEFAULT_METHOD;
        if (isset($matches[3])) {
            $methodName = $matches[3];
            $methodName = strtolower($methodName);
        }

        return array($controllerName, $methodName);
    }

    /**
     * @param string
     * @return Commons
     */
    public function createCommons($url)
    {
        $commons = new Commons();
        $commons->post = $_POST;
        $commons->get = $_GET;
        $user = null;
        try {
            $user = $this->loginService->getLoggedInUser();
        } catch (UserNotFoundException $e) {
        }
        if (is_null($user)) {
            $commons->isLoggedIn = false;
            $commons->user = null;
        } else {
            $commons->isLoggedIn = true;
            $commons->user = $user;
            $commons->isAdmin = $user->getRole() == 'admin';
        }

        $commons->url = $url;
        return $commons;
    }

    public function route()
    {
        $router = new Router($this->em, $this->commons);
        $router->route($this->controllerName, $this->methodName);
    }
}

$em = EntityManagerFactory::getEntityManager();

$init = new Init($em);

$init->go($_SERVER['REQUEST_URI']);
