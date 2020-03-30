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

ConnectionManager::setConfig('test', ['url' => getenv('DB_DSN')]);
