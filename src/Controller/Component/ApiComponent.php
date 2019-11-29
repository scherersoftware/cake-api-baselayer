<?php
declare(strict_types = 1);
namespace CakeApiBaselayer\Controller\Component;

use Cake\Controller\Component;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use CakeApiBaselayer\Lib\ApiReturnCode;
use Exception;

/**
 * @property \Cake\Controller\Component\AuthComponent $Auth
 * @property \Cake\Controller\Component\RequestHandlerComponent $RequestHandler
 */
class ApiComponent extends Component
{
    /**
     * Used Components
     *
     * @var array
     */
    public $components = [
        'RequestHandler',
        'Auth',
    ];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'jsonEncodeOptions' => JSON_UNESCAPED_SLASHES,
        'header_name' => 'APITOKEN',
        'repository' => 'Users',
        'field' => 'api_token',
        'allow_parallel_sessions' => true,
    ];

    /**
     * Holds the Response object
     *
     * @var \Cake\Http\Response
     */
    protected $_response = null;

    /**
     * Maps return codes to HTTP status codes
     *
     * @var array
     */
    protected $_statusCodeMapping = [];

    /**
     * Table to be used
     *
     * @var \Cake\ORM\Table
     */
    protected $_table = null;

    /**
     * Flag if response contains validation errors
     *
     * @var bool
     */
    protected $_hasErrors = false;

    /**
     * Constructor hook method.
     *
     * Implement this method to avoid having to overwrite
     * the constructor and call parent.
     *
     * @param array $config The configuration settings provided to this component.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->_table = TableRegistry::getTableLocator()->get($this->getConfig('repository'));
        $this->_statusCodeMapping = ApiReturnCode::getStatusCodeMapping();
    }

    /**
     * Should be called in the controller's beforeFilter callback
     *
     * @return void
     */
    public function setup(): void
    {
        $this->RequestHandler->prefers('json');
        $this->RequestHandler->renderAs($this->_registry->getController(), 'json');
        $this->apiTokenAuthentication();
    }

    /**
     * Returns the response object to modify
     *
     * @return \Cake\Http\Response
     */
    public function getResponse(): Response
    {
        if (!empty($this->_response)) {
            return $this->_response;
        }

        return $this->_registry->getController()->getResponse();
    }

    /**
     * Set the response object for manipulation by response()
     *
     * @param \Cake\Http\Response $response Response object to manipulate
     * @return void
     */
    public function setResponse(Response $response): void
    {
        $this->_response = $response;
    }

    /**
     * Returns a standardized JSON response
     *
     * @param string $returnCode     A string code more specific to the result
     * @param array  $data           Data for the 'data' key
     * @param int    $httpStatusCode HTTP Status Code to send
     * @return \Cake\Http\Response
     */
    public function response(
        string $returnCode = ApiReturnCode::SUCCESS,
        array $data = [],
        int $httpStatusCode = null
    ): Response {
        if (!$httpStatusCode) {
            $httpStatusCode = $this->getHttpStatusForReturnCode($returnCode);
        }

        $response = $this->getResponse();
        $response = $response->withStatus($httpStatusCode);

        $responseData = [
            'code' => $returnCode,
            'has_errors' => $this->hasErrors(),
            'data' => $data,
        ];
        $response = $response
            ->withType('json')
            ->withStringBody(json_encode($responseData, $this->getConfig('jsonEncodeOptions')));

        return $response;
    }

    /**
     * Returns the appropriate HTTP Status code for the given return code.
     *
     * @param string $returnCode Return Code
     * @return int
     */
    public function getHttpStatusForReturnCode(string $returnCode): int
    {
        if (!isset($this->_statusCodeMapping[$returnCode])) {
            throw new Exception("Return code {$returnCode} is not mapped to any HTTP Status Code.");
        }

        return $this->_statusCodeMapping[$returnCode];
    }

    /**
     * Obtain the status code mapping
     *
     * @return array
     */
    public function getStatusCodeMapping(): array
    {
        return $this->_statusCodeMapping;
    }

    /**
     * Map a return code to a status code
     *
     * @param string $returnCode     Return Code
     * @param int    $httpStatusCode The HTTP Status code to use for the given return code
     * @return void
     */
    public function mapStatusCode(string $returnCode, int $httpStatusCode): void
    {
        $this->_statusCodeMapping[$returnCode] = $httpStatusCode;
    }

    /**
     * Map return codes to HTTP Status codes
     *
     * @param array $codes Array with the return code as key and the HTTP Status code as value
     * @return void
     */
    public function mapStatusCodes(array $codes): void
    {
        $this->_statusCodeMapping = Hash::merge($this->getStatusCodeMapping(), $codes);
    }

    /**
     * Handles authentication via the ApiToken header.
     *
     * @return void
     */
    public function apiTokenAuthentication(): void
    {
        $token = $this->getController()->getRequest()->getHeaderLine($this->getConfig('header_name'));
        if (!empty($token)) {
            if (!$this->Auth->user() || $this->Auth->user($this->getConfig('field')) !== $token) {
                $user = $this->_getEntityByToken($token);
                if (!empty($user)) {
                    $this->Auth->setUser($user->toArray());
                } else {
                    $this->Auth->logout();
                }
            }
        }
    }

    /**
     * Provides a table record for a token
     *
     * @param string $token token string
     * @return \Cake\Datasource\EntityInterface
     */
    protected function _getEntityByToken(string $token): EntityInterface
    {
        return $this->_table->find()
            ->where([
                $this->getConfig('field') => $token,
            ])
            ->first();
    }

    /**
     * Use the configured authentication adapters, and attempt to identify the user
     * by credentials contained in $request.
     *
     * @return array|bool User record data, or false, if the user could not be identified.
     */
    public function identify()
    {
        if ($this->Auth->user()) {
            $this->Auth->logout();
        }
        $user = $this->Auth->identify();
        if (is_array($user)) {
            if (empty($user[$this->getConfig('field')]) || $this->getConfig('allow_parallel_sessions') === false) {
                $userEntity = $this->_table->get($user['id']);

                $userEntity->api_token = $this->generateApiToken();
                $this->_table->save($userEntity);
                $user[$this->getConfig('field')] = $userEntity->get($this->getConfig('field'));
            }
            $this->Auth->setUser($user);

            return $user;
        }

        return false;
    }

    /**
     * clears the api token
     *
     * @return void
     */
    public function logout(): void
    {
        if ($this->Auth->user()) {
            $userEntity = $this->_table->get($this->Auth->user('id'));
            $userEntity->api_token = null;
            $this->_table->save($userEntity);
            $this->Auth->logout();
        }
    }

    /**
     * Generates a unique API token
     *
     * @return string
     */
    public function generateApiToken(): string
    {
        $pseudoBytes = openssl_random_pseudo_bytes(16);
        if ($pseudoBytes === false) {
            throw new Exception('Could not generate API token');
        }

        return bin2hex($pseudoBytes);
    }

    /**
     * Gets and sets _hasErrors property.
     * If the parameter is null, it gets the actual value of the property.
     * If the parameter is not null, it sets the boolean representation of the given value into the
     * property and returns the newly set value.
     *
     * @param  bool  $errors sets the property
     * @return bool
     */
    public function hasErrors(bool $errors = null): bool
    {
        if ($errors !== null) {
            $this->_hasErrors = $errors;
        }

        return $this->_hasErrors;
    }

    /**
     * Checks if a given entity or its children entities have errors.
     * If so, sets _hasErrors and returns true, if not, returns false.
     *
     * @param \Cake\Datasource\EntityInterface $entity entity which has possibly errors in it
     * @return bool          has errors => true | has no errors => false
     */
    public function checkForErrors(EntityInterface $entity): bool
    {
        if (is_callable([$entity, 'errors']) && !empty($entity->getErrors())) {
            return $this->hasErrors(true);
        }
        foreach ($entity->getVisible() as $propertyName) {
            $property = $entity->get($propertyName);
            if (is_callable([$property, 'getErrors']) && !empty($property->getErrors())) {
                return $this->hasErrors(true);
            }
        }

        return false;
    }
}
