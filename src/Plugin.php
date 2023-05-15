<?php
declare(strict_types = 1);
namespace CakeApiBaselayer;

use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;

class Plugin extends BasePlugin
{
    /**
     * @inheritDoc
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        if (env('REQUEST_URI') && substr(env('REQUEST_URI'), 0, 5) === '/api/') {
            Configure::write('Error.exceptionRenderer', '\CakeApiBaselayer\Error\ApiExceptionRenderer');
        }
    }
}
