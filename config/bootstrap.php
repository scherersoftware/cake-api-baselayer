<?php
if (substr(env('REQUEST_URI'), 0, 5) === '/api/') {
    $errorHandler = new \CakeApiBaselayer\Error\ApiErrorHandler([
        'exceptionRenderer' => '\CakeApiBaselayer\Error\ApiExceptionRenderer'
    ]);
    $errorHandler->register();
}
