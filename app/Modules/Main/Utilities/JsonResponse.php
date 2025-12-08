<?php

namespace App\Modules\Main\Utilities;

use Illuminate\Http\Response as IlluminateResponse;

class JsonResponse
{
    protected static $statusCode = IlluminateResponse::HTTP_OK;

    public static function notFound($message)
    {
        return self::setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)->error($message);
    }

    public static function error($message = '', $errors = [])
    {
        return self::response([
            'status'      => 'failed',
            'status_code' => self::$statusCode,
            'message'     => $message,
            'errors'      => is_string($errors) ? [$errors] : $errors,
            'data'        => []
        ]);
    }

    public static function success($message, $data = [])
    {
        return self::setStatusCode(IlluminateResponse::HTTP_OK)->response([
            'status'      => 'success',
            'status_code' => self::$statusCode,
            'message'     => $message,
            'errors'      => [],
            'data'        => $data
        ]);
    }

    public static function successWithOnlyData($data)
    {
        return self::setStatusCode(IlluminateResponse::HTTP_OK)->response(['data' => $data]);
    }

    public static function forbidden($message)
    {
        return self::setStatusCode(IlluminateResponse::HTTP_FORBIDDEN)->error($message);
    }

    public static function unauthorized($message)
    {
        return self::setStatusCode(IlluminateResponse::HTTP_UNAUTHORIZED)->error($message);
    }

    public static function validationError($message = null, $errors = [])
    {
        return self::setStatusCode(IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY)->error($message, $errors);
    }

    public static function internalError($message)
    {
        return self::setStatusCode(IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR)->error($message);
    }

    private static function response($data, $headers = [])
    {
        return response()->json($data, self::$statusCode, $headers);
    } 

    private static function setStatusCode($statusCode)
    {
        self::$statusCode = $statusCode;
        return new static;
    }
}
