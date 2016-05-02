<?php

namespace CakeApiBaselayer\Controller;

use App\Controller\AppController as BaseController;
use Cake\Core\Configure;
use CakeApiBaselayer\Lib\ApiReturnCode;

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

    /**
     * Version getter
     *
     * @return string
     */
    public function version()
    {
        $configId = str_replace('/', '.', $this->request->param('plugin'));
        $versionInfo = Configure::read($configId . '.version_info');
        return $this->Api->response(ApiReturnCode::SUCCESS, $versionInfo);
    }
}
