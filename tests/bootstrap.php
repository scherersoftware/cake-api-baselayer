<?php
/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

require dirname(__DIR__) . '/vendor/autoload.php';

$_SERVER['PHP_SELF'] = '/';

Configure::write('App.namespace', 'CakeApiBaselayer');

ConnectionManager::setConfig('test', [
    'className' => 'Cake\Database\Connection',
    'driver' => 'Cake\Database\Driver\Mysql',
    'persistent' => false,
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'cake_api_baselayer_test',
    'encoding' => 'utf8',
    'timezone' => 'UTC'
]);
