<?php

namespace CakeApiBaselayer\Controller;

use App\Controller\AppController as BaseController;
use Cake\Routing\Router;
use CakeApiBaselayer\Lib\ApiReturnCode;
use Cake\Core\Configure;

class AppController extends BaseController
{
    /**
     * Initialization Hook.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('CakeApiBaselayer.Api');
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(\Cake\Event\Event $event)
    {
        parent::beforeFilter($event);

        $this->Api->setup();
        $this->Api->RequestHandler->renderAs($this->Api->_registry->getController(), 'json');
    }
}
