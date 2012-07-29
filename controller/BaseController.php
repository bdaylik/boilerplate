<?php

require_once(dirname(__FILE__) . '/../helper/ViewLoader.php');
require_once(dirname(__FILE__) . '/../lib/mustache/Mustache.php');
require_once(dirname(__FILE__) . '/../model/exceptions/MethodNotfoundException.php');
require_once(dirname(__FILE__) . '/../service/RedirectService.php');

abstract class BaseController
{

    /**
     * @var array
     */
    private $viewParameters;
    /**
     * @var array
     */
    private $viewPartials;
    /**
     * @var null
     */
    private $view;
    /**
     * @var Mustache
     */
    private $mustache;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Commons
     */
    protected $commons;

    /**
     * @var RedirectService
     */
    protected $redirectService;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param $commons
     */
    function __construct(&$em, &$commons)
    {
        $this->viewParameters = array();
        $this->viewPartials = array();
        $this->view = null;

        $this->mustache = new Mustache();

        $this->em = $em;
        $this->commons = $commons;

        $this->redirectService = new RedirectService($em);

        $this->init();
    }

    protected abstract function init();

    public abstract function isLoginRequired();

    protected abstract function run();

    public function isAdminRequired()
    {
        return false;
    }

    public function baseRun($method)
    {
        if (method_exists($this, $method)) {
            $this->$method();
            $this->render();
        } else {
            throw new MethodNotFoundException();
        }
    }

    public function addViewParameter($name, $value, &$viewParameters = null)
    {
        if (is_null($viewParameters)) {
            $viewParameters = &$this->viewParameters;
        }
        $viewParameters[$name] = $value;
    }

    public function addViewPart($file, $refName, &$viewPartials = null)
    {
        if (is_null($viewPartials)) {
            $viewPartials = &$this->viewPartials;
        }
        $viewPartials[$refName] = ViewLoader::load($file);
    }

    public function setView($file, &$view = null)
    {
        if (is_null($view)) {
            $view = &$this->view;
        }
        $view = ViewLoader::load($file);
    }

    protected function render($view = null, $viewParameters = null, $viewPartials = null)
    {
        $skipMustache = false;

        if (!$view) {
            if ($this->view) {
                $view = $this->view;
            } else {
                $skipMustache = true;
            }
        }

        if (!$skipMustache) {
            if (!$viewParameters) {
                if ($this->viewParameters) {
                    $viewParameters = $this->viewParameters;
                }
            }

            if (!$viewPartials) {
                if ($this->viewPartials) {
                    $viewPartials = $this->viewPartials;
                }
            } else {
                $viewPartials = array_merge($this->getMessages(), $viewPartials);
            }
            $viewParameters['commons'] = $this->commons;
            $viewPartials['ROOT_URL'] = ROOT_URL;
            $viewPartials['INDEX_URL'] = INDEX_URL;

            header('Content-Type: text/html; charset=UTF-8');

            echo $this->mustache->render($view, $viewParameters, $viewPartials);
        }
    }
}
