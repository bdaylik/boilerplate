<?php

class ViewLoader
{
    public static function load($view = null)
    {
        $rv = '';

        if(! is_null($view)) {
            $viewFile = dirname(__FILE__) . '/../view/' . $view . '.mustache';

            if(file_exists($viewFile)) {
                $rv = file_get_contents($viewFile);
            }
        }

        return $rv;
    }
}
