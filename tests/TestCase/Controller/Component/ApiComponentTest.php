<?php
declare(strict_types = 1);
namespace CakeApiBaselayer\Test\TestCase\Controller\Component;

use Cake\Controller\Controller;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use CakeApiBaselayer\Controller\Component\ApiComponent;
use CakeApiBaselayer\Lib\ApiReturnCode;

/**
 * Api\Controller\Component\ApiComponent Test Case
 *
 * @property \CakeApiBaselayer\Controller\Component\ApiComponent Api
*/
class ApiComponentTest extends TestCase
{

    /**
     * fixtures property
     *
     * @var array
     */
    protected $fixtures = ['plugin.CakeApiBaselayer.Users'];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $request = new ServerRequest([]);
        $response = new Response();

        $this->Controller = new Controller($request, $response);
        $this->Api = new ApiComponent($this->Controller->components());
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Controller);
        unset($this->Api);

        parent::tearDown();
    }

    /**
     * Test generateApiToken()
     *
     * @return void
     */
    public function testGenerateApiToken()
    {
        $token = $this->Api->generateApiToken();
        $this->assertTrue(is_string($token));
    }

    /**
     * Test the response() method.
     *
     * @return void
     */
    public function testResponse()
    {
        $cakeResponse = new Response();
        $this->Api->setResponse($cakeResponse);

        $httpStatus = 201;
        $code = ApiReturnCode::SUCCESS;
        $data = [
            'foo' => 'bar'
        ];
        $response = $this->Api->response($code, $data, $httpStatus);

        $this->assertEquals($response->getType(), 'application/json');
        $this->assertEquals($response->getStatusCode(), $httpStatus);

        $decoded = json_decode((string)$response->getBody(), true);
        $this->assertEquals($decoded['data'], $data);
        $this->assertEquals($decoded['code'], $code);
    }

    /**
     * Test getHttpStatusForReturnCode(), mapStatusCode() and mapStatusCodes()
     *
     * @return void
     */
    public function testStatusCodeMapping()
    {
        $this->assertEquals($this->Api->getHttpStatusForReturnCode(ApiReturnCode::SUCCESS), 200);
        $this->assertEquals($this->Api->getHttpStatusForReturnCode(ApiReturnCode::NOT_AUTHENTICATED), 401);

        $this->Api->mapStatusCode('foobar', 123);
        $this->assertEquals($this->Api->getHttpStatusForReturnCode('foobar'), 123);

        $this->assertEquals($this->Api->getHttpStatusForReturnCode(ApiReturnCode::NOT_AUTHENTICATED), 401);

        $this->Api->mapStatusCodes([
            'code1' => 111,
            'code2' => 222
        ]);
        $this->assertEquals($this->Api->getHttpStatusForReturnCode('code1'), 111);
        $this->assertEquals($this->Api->getHttpStatusForReturnCode('code2'), 222);

        $this->assertEquals($this->Api->getHttpStatusForReturnCode(ApiReturnCode::NOT_AUTHENTICATED), 401);
    }
}
