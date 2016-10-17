<?php
use Cake\Core\Configure;
use Cake\Utility\Hash;

// Load and merge default with app config
$config = require 'config.php';
if (substr(env('REQUEST_URI'), 0, 5) === '/api/') {
    Configure::write('Error.exceptionRenderer', '\CkTools\Error\ApiExceptionRenderer');
}
Configure::write('Api.V2', $config);
