<?php
declare(strict_types = 1);
namespace CakeApiBaselayer\Lib;

class ApiReturnCode
{
    public const SUCCESS = 'success';
    public const ENTITY_CREATED = 'entity_created';
    public const NOT_AUTHENTICATED = 'not_authenticated';
    public const INVALID_PARAMS = 'invalid_params';
    public const INVALID_CREDENTIALS = 'invalid_credentials';
    public const NOT_AUTHORIZED = 'not_authorized';
    public const FORBIDDEN = 'forbidden';
    public const VALIDATION_FAILED = 'validation_failed';
    public const NOT_FOUND = 'not_found';
    public const INTERNAL_ERROR = 'internal_error';

    /**
     * Maps return codes to HTTP Status Codes
     *
     * @return array
     */
    public static function getStatusCodeMapping(): array
    {
        return [
            self::SUCCESS => 200,
            self::ENTITY_CREATED => 201,
            self::NOT_AUTHENTICATED => 401,
            self::INVALID_CREDENTIALS => 401,
            self::INVALID_PARAMS => 400,
            self::NOT_AUTHORIZED => 403,
            self::FORBIDDEN => 403,
            self::NOT_FOUND => 404,
            self::VALIDATION_FAILED => 400,
            self::INTERNAL_ERROR => 500,
        ];
    }
}
