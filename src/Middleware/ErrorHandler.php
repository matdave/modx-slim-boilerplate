<?php

namespace MODXSlim\Api\Middleware;

use MODXSlim\Api\Exceptions\RestfulException;
use MODXSlim\Api\Renderers\HttpExceptionRenderer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Slim\Interfaces\CallableResolverInterface;
use Throwable;

class ErrorHandler extends SlimErrorHandler
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \MODXSlim\Api\Exceptions\RestfulException
     */
    protected $exception;

    protected $logErrorRenderer = HttpExceptionRenderer::class;

    /**
     * @param CallableResolverInterface $callableResolver
     * @param ResponseFactoryInterface  $responseFactory
     */
    public function __construct(CallableResolverInterface $callableResolver, ResponseFactoryInterface $responseFactory, LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct($callableResolver, $responseFactory);
    }

    /**
     * Invoke error handler
     *
     * @param ServerRequestInterface $request             The most recent Request object
     * @param Throwable              $exception           The caught Exception object
     * @param bool                   $displayErrorDetails Whether or not to display the error details
     * @param bool                   $logErrors           Whether or not to log errors
     * @param bool                   $logErrorDetails     Whether or not to log error details
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): ResponseInterface {
        $this->displayErrorDetails = $displayErrorDetails;
        $this->logErrors = $logErrors;
        $this->logErrorDetails = $logErrorDetails;
        $this->request = $request;
        $this->exception = $exception;

        if ($this->exception instanceof \Slim\Exception\HttpNotFoundException) {
            $this->exception = RestfulException::notFound(['detail' => $this->exception->getMessage()], [], $this->exception);
        }

        if ($this->exception instanceof \Slim\Exception\HttpBadRequestException) {
            $this->exception = RestfulException::badRequest(['detail' => $this->exception->getMessage()], [], $this->exception);
        }

        if ($this->exception instanceof \Slim\Exception\HttpForbiddenException) {
            $this->exception = RestfulException::forbidden(['detail' => $this->exception->getMessage()], [], $this->exception);
        }

        if ($this->exception instanceof \Slim\Exception\HttpMethodNotAllowedException) {
            $this->exception = RestfulException::methodNotAllowed(['detail' => $this->exception->getMessage()], [], $this->exception);
        }

        if ($this->exception instanceof \Slim\Exception\HttpUnauthorizedException) {
            $this->exception = RestfulException::unauthorized(['detail' => $this->exception->getMessage()], [], $this->exception);
        }

        if ($this->exception instanceof \Slim\Exception\HttpNotImplementedException) {
            $this->exception = RestfulException::notImplemented(['detail' => $this->exception->getMessage()], [], $this->exception);
        }

        if (!$this->exception instanceof RestfulException) {
            $this->exception = RestfulException::internalServerError(['detail' => $this->exception->getMessage()], [], $this->exception);
        }

        $this->method = $request->getMethod();
        $this->statusCode = $this->determineStatusCode();
        if ($this->contentType === null) {
            $this->contentType = $this->determineContentType($request);
        }

        if ($logErrors) {
            $this->writeToErrorLog();
        }

        return $this->respond();
    }

    protected function respond(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($this->statusCode);
        if ($this->contentType !== null && array_key_exists($this->contentType, $this->errorRenderers)) {
            $response = $response->withHeader('Content-type', $this->contentType);
        } else {
            $response = $response->withHeader('Content-type', $this->defaultErrorRendererContentType);
        }

        $body = $this->exception->getBody();
        if ($this->displayErrorDetails) {
            $body['exception'] = [
                'message' => $this->exception->getMessage(),
                'code' => $this->exception->getCode(),
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
                'trace' => $this->exception->getTrace(),
            ];
        }

        foreach ($this->exception->getHeaders() as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        $response->getBody()->write(json_encode($body));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($this->exception->getStatusCode());
    }

    /**
     * Write to the error log if $logErrors has been set to true
     *
     * @return void
     */
    protected function writeToErrorLog(): void
    {
        $renderer = $this->callableResolver->resolve($this->logErrorRenderer);
        $error = $renderer($this->exception, $this->logErrorDetails);

        $this->logError($error);
    }

    protected function logError(string $error): void
    {
        $this->logger->error($error);
    }
}
