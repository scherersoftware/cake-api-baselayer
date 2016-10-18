# CakeApiBaselayer

![CakePHP 3 Api Baselayer Plugin](https://raw.githubusercontent.com/scherersoftware/cake-api-baselayer/update_10_2016/cake-api-baselayer.png)

[![Build Status](https://travis-ci.org/scherersoftware/cake-api-baselayer.svg?branch=update_10_2016)](https://travis-ci.org/scherersoftware/cake-api-baselayer)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)

- Create RESTFUL APIs for your CakePHP 3 project
- Use API Tokens for authentication
- Respond to HTTP requests with lightweigt JSON
- Organize your API in different versions

## Dependencies
- **php 5.4.16** or higher
- **cakephp 3.0** or higher

## Installation & Configuration

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
$ composer require scherersoftware/cake-api-baselayer
```

In addition, you have to add the following line to your **config/bootstrap.php**:

```
Plugin::load('CakeApiBaselayer', ['bootstrap' => true, 'routes' => true]);
```

Now, in your **src/Controller/Appcontroller.php**, add the plugin to your **$components**:

```
public $components = [
    'CakeApiBaselayer.Api',
    ...
];
```

## Creating your own API

You can use code generation via cake bake.

```
$ bin/cake bake plugin <your_api_name>/<version>
```

By default, cake will generate some files you don't need:

- `<your_plugin>/<your_version>/composer.json`
- `<your_plugin>/<your_version>/phpunit.xml.dist`
- `README.md`
- `<your_plugin>/<your_version>/*` (whole directory)

as well as some lines you need to sligthly adjust:

`<your_plugin>/<your_version>/src/Controller/`**`AppController.php`:**

**use statements:**
	
```
use CakeApiBaselayer\Controller\AppController as BaseController;
use CakeApiBaselayer\Lib\ApiReturnCode;
use Cake\Routing\Router;
```
	
**functions:**

```
public function initialize()
{
	$this->loadComponent('RequestHandler');
	$this->loadComponent('CakeApiBaselayer.Api');
}
```

```
public function beforeFilter(\Cake\Event\Event $event)
{
    $this->Api->setup();
    parent::beforeFilter($event);
}
```

**@TODO:** Prüfen, ob das Unfug ist

Finally, a custom bootstrap-file is needed at `<your_plugin>/<your_version>/config/bootstrap.php`:

```
<?php
use Cake\Core\Configure;

if (substr(env('REQUEST_URI'), 0, 5) === '/<your_api_name>/')
{
    Configure::write('Error.exceptionRenderer', '\CakeApiBaselayer\Error\ApiExceptionRenderer');
}
```

## Usage

In the that Controller you want to handle API responses, make use of the plugins `ApiReturnCode` Lib file:

```
use CakeApiBaselayer\Lib\ApiReturnCode;
```

Now, a simple call to the plugins `response` function handles returning JSON data to requests. This function requires the following parameters:

- a HTTP status as a string (see the list below for supported status codes; the default value is SUCCESS (200)
- an array of key value pairs, as defined by you
- a HTTP status as a code (see the list below; this parameter is `null` by default)

So a call to this function might look something like this:

```
$this->Api->response(
	ApiReturnCode::FORBIDDEN,		// explicit return code
	[
	    'your_key_00' => 'your_value_00',	// your data
	    'your_val_01' => 'your_value_01'	// your data
	]
);
```

The plugin supports the following HTTP status codes:

- 200, `SUCCESS`
- 400, `INVALID_PARAMS`
- 400, `VALIDATION_FAILED`
- 401, `NOT_AUTHENTICATED`
- 401, `INVALID_CREDENTIALS`
- 403, `NOT_AUTHORIZED`
- 403, `FORBIDDEN`
- 404, `NOT_FOUND`
- 500, `INTERNAL_ERROR`

If needed, further status codes can be added in the `cake-api-baselayer/src/Lib/ApiReturnCode.php`