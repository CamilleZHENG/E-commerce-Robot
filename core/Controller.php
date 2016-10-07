<?php

/**
 * Created by PhpStorm.
 * User: wap21
 * Date: 20/07/16
 * Time: 16:47
 */
abstract class Controller
{
    protected $viewData = [];

    protected function generateView($viewPath, $templatePath = 'default.phtml')
    {
        $view = new View($viewPath, $this-> viewData, $templatePath);
        $view -> generate();
    }
    
}
