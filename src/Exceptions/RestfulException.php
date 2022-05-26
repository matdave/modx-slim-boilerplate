<?php

namespace MODXSlim\Api\Exceptions;

use Throwable;

class RestfulException extends \Exception
{
    /**
     * @var int
     */
    private $statusCode;
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $detail;
    /**
     * @var array
     */
    private $body;
    /**
     * @var array
     */
    private $headers;

    public function __construct($statusCode, $title, $detail, array $body = [], array $headers = [], Throwable $previous = null)
    {
        $code = isset($body['code']) ? (int)$body['code'] : 0;

        parent::__construct($title, $code, $previous);

        $this->statusCode = $statusCode;
        $this->title = $title;
        $this->detail = $detail;
        $this->body = array_merge(
            [
                'status' => $statusCode,
                'title' => $title,
                'detail' => $detail
            ],
            $body
        );
        $this->headers = $headers;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDetail(): string
    {
        return $this->detail;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param RestfulException $exception
     *
     * @return static
     */
    public static function from(RestfulException $exception)
    {
        return new static(
            $exception->getStatusCode(),
            $exception->getTitle(),
            $exception->getDetail(),
            $exception->getBody(),
            $exception->getHeaders(),
            $exception->getPrevious()
        );
    }

    /**
     * @param array          $body
     * @param array          $headers
     * @param Throwable|null $previous
     *
     * @return static
     */
    public static function badRequest(array $body = [], array $headers = [], \Throwable $previous = null)
    {
        return new static(
            400,
            'Bad Request',
            'The request could not be understood by the server due to malformed syntax. DO NOT repeat the request without modifications.',
            array_merge(['type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.1'], $body),
            $headers,
            $previous
        );
    }

    /**
     * @param array          $body
     * @param array          $headers
     * @param Throwable|null $previous
     *
     * @return static
     */
    public static function unauthorized(array $body = [], array $headers = [], \Throwable $previous = null)
    {
        return new static(
            401,
            'Unauthorized',
            'The requested resource requires authentication.',
            array_merge(['type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.2'], $body),
            $headers,
            $previous
        );
    }

    /**
     * @param array          $body
     * @param array          $headers
     * @param Throwable|null $previous
     *
     * @return static
     */
    public static function notFound(array $body = [], array $headers = [], \Throwable $previous = null)
    {
        return new static(
            404,
            'Not Found',
            'The requested resource was not found on this server.',
            array_merge(['type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.5'], $body),
            $headers,
            $previous
        );
    }

    /**
     * @param array          $body
     * @param array          $headers
     * @param Throwable|null $previous
     *
     * @return static
     */
    public static function methodNotAllowed(array $body = [], array $headers = [], \Throwable $previous = null)
    {
        return new static(
            405,
            'Method Not Allowed',
            'The requested method is not supported by this resource.',
            array_merge(['type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.6'], $body),
            $headers,
            $previous
        );
    }

    /**
     * @param array          $body
     * @param array          $headers
     * @param Throwable|null $previous
     *
     * @return static
     */
    public static function conflict(array $body = [], array $headers = [], \Throwable $previous = null)
    {
        return new static(
            409,
            'Conflict',
            'The request could not be completed due to a conflict with the current state of the resource.',
            array_merge(['type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.10'], $body),
            $headers,
            $previous
        );
    }

    /**
     * @param array          $body
     * @param array          $headers
     * @param Throwable|null $previous
     *
     * @return static
     */
    public static function internalServerError(array $body = [], array $headers = [], \Throwable $previous = null)
    {
        return new static(
            500,
            'Internal Server Error',
            'The server encountered an unexpected condition that prevented it from fulfilling the request.',
            array_merge(['type' => 'https://tools.ietf.org/html/rfc2616#section-10.5.1'], $body),
            $headers,
            $previous
        );
    }

    /**
     * @param array          $body
     * @param array          $headers
     * @param Throwable|null $previous
     *
     * @return static
     */
    public static function forbidden(array $body = [], array $headers = [], \Throwable $previous = null)
    {
        return new static(
            403,
            'Forbidden',
            'The server understood the request, but is refusing to fulfill it.',
            array_merge(['type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.4'], $body),
            $headers,
            $previous
        );
    }

    /**
     * @param array          $body
     * @param array          $headers
     * @param Throwable|null $previous
     *
     * @return static
     */
    public static function notImplemented(array $body = [], array $headers = [], \Throwable $previous = null)
    {
        return new static(
            501,
            'Not Implemented',
            'The server does not support the functionality required to fulfill the request.',
            array_merge(['type' => 'https://tools.ietf.org/html/rfc2616#section-10.5.2'], $body),
            $headers,
            $previous
        );
    }

    /**
     * @param array          $body
     * @param array          $headers
     * @param Throwable|null $previous
     *
     * @return static
     */
    public static function unprocessableEntity(array $body = [], array $headers = [], \Throwable $previous = null)
    {
        return new static(
            422,
            'Unprocessable Entity',
            'The server understood the request, but request is in a wrong format.',
            array_merge(['type' => 'https://tools.ietf.org/html/rfc4918#section-11.2'], $body),
            $headers,
            $previous
        );
    }
}
