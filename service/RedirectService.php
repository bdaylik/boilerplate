<?php

require_once(dirname(__FILE__) . '/BaseService.php');

class RedirectService extends BaseService
{
    private $defaultRedirectUrl;

    function __construct(&$em, $defaultRedirectUrl = false)
    {
        parent::__construct($em);

        if ($defaultRedirectUrl) {
            $this->defaultRedirectUrl = $defaultRedirectUrl;
        } else {
            $this->defaultRedirectUrl = INDEX_URL . DEFAULT_CONTROLLER;
        }
    }

    public function setRedirectUrl($url)
    {
        $_SESSION['redirect'] = $url;
    }

    public function getRedirectUrl()
    {
        $redirectUrl = isset($_SESSION['redirect']) ? $_SESSION['redirect'] : null;

        if (is_null($redirectUrl)) {
            $redirectUrl = $this->defaultRedirectUrl;
        }

        return $redirectUrl;
    }

    public function redirect($controllerName = null, $methodName = null, $queryParams = null)
    {
        if (is_null($controllerName)) {
            $redirectUrl = $this->getRedirectUrl();
            unset($_SESSION['redirect']);
        } else {
            $redirectUrl = INDEX_URL . $controllerName;
            if(!is_null($methodName) && !preg_match('/^\s*$/', $methodName)) {
                $redirectUrl .= '/' . $methodName;
            }
            if(!is_null($queryParams) && is_array($queryParams)) {
                $redirectUrl .= '?';
                foreach($queryParams as $key => $value) {
                    $redirectUrl .= rawurlencode($key) . '=' . rawurlencode($value) . '&';
                }
            }
        }

        if (headers_sent()) {
            echo '<script type="text/javascript">top.window.location = ' . $redirectUrl . ';</script>';
        } else {
            header('Location: ' . $redirectUrl);
        }
    }
}
