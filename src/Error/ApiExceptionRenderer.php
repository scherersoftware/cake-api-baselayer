<?php
declare(strict_types = 1);
namespace CakeApiBaselayer\Error;

use Cake\Controller\Controller;
use Cake\Error\ExceptionRenderer;

class ApiExceptionRenderer extends ExceptionRenderer
{

    /**
     * overwriting get controller
     *
     * @return \Cake\Controller\Controller instance of controller
     */
    protected function _getController(): Controller
    {
        $controller = parent::_getController();
        $controller->loadComponent('RequestHandler');
        $controller->RequestHandler->renderAs($controller, 'json');

        return $controller;
    }

    /**
     * Make sure the code value is returned as a string
     * // FIXME make this configurable via an option
     *
     * @param \Exception $exception Exception
     * @return int Error code value within range 400 to 506
     */
    protected function _code(\Exception $exception): int
    {
        return (int)parent::_code($exception);
    }
}
