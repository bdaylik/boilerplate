<?php
/**
 * Created by JetBrains PhpStorm.
 * User: baris
 * Date: 5/12/12
 * Time: 2:26 PM
 * To change this template use File | Settings | File Templates.
 */
require_once(dirname(__FILE__) . '/service/RedirectService.php');
require_once(dirname(__FILE__) . '/model/exceptions/NotfoundException.php');
require_once(dirname(__FILE__) . '/model/exceptions/AccessRestrictedException.php');

class Router
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var Commons
     */
    private $commons;

    /**
     * @var RedirectService
     */
    private $redirectService;

    function __construct(&$em, &$commons)
    {
        $this->em = $em;
        $this->commons = $commons;

        $this->redirectService = new RedirectService($em);
    }

    function route($controllerName, $methodName)
    {
        if (is_null($controllerName)) {
            //Redirect to default
            $this->redirectService->redirect(DEFAULT_CONTROLLER);
        } else {
            if (is_null($methodName)) {
                $methodName = DEFAULT_METHOD;
            }
            try {
                $controller = $this->loadController($controllerName);

                if (!$controller->isLoginRequired() || ($controller->isLoginRequired() && $this->commons->isLoggedIn)) {
                    if (!$controller->isAdminRequired() || ($controller->isAdminRequired() && $this->commons->isAdmin)) {
                        $controller->baseRun($methodName);
                    } else {
                        throw new AccessRestrictedException();
                    }
                } else {
                    $this->redirectService->setRedirectUrl($this->commons->url);
                    $this->redirectService->redirect('login');
                }
            } catch (NotfoundException $ne) {
                $this->redirectService->redirect('notfound');
            } catch (MethodNotfoundException $ne) {
                $this->redirectService->redirect('notfound');
            } catch (AccessRestrictedException $ne) {
                $this->redirectService->redirect('notfound');
            }
        }
    }

    protected function loadController($controllerName)
    {
        $controllerPath = dirname(__FILE__) . '/controller/' . $controllerName . 'Controller.php';

        if (!file_exists($controllerPath)) {
            throw new NotfoundException();
        }

        include($controllerPath);

        $class = $controllerName . 'Controller';

        if (!class_exists($class)) {
            throw new NotfoundException();
        }

        $controller = new $class($this->em, $this->commons);

        return $controller;
    }
}