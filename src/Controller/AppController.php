<?php
declare(strict_types = 1);
namespace CakeApiBaselayer\Controller;

use App\Controller\AppController as BaseController;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Routing\Router;
use CakeApiBaselayer\Lib\ApiReturnCode;

class AppController extends BaseController
{
    /**
     * Initialization Hook.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('CakeApiBaselayer.Api');
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Api->setup();
    }

    /**
     * {@inheritDoc}
     */
    public function redirect($url, int $status = 302): ?Response
    {
        if (strpos(Router::normalize($url), Router::normalize($this->Auth->getConfig('loginAction'))) === 0) {
            return $this->Api->response(ApiReturnCode::NOT_AUTHENTICATED);
        }

        return parent::redirect($url, $status);
    }

    /**
     * Version getter
     *
     * @return string
     */
    public function version(): string
    {
        $configId = str_replace('/', '.', $this->request->getParam('plugin'));
        $versionInfo = Configure::read($configId . '.version_info');

        return $this->Api->response(ApiReturnCode::SUCCESS, $versionInfo);
    }
}
