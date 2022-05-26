<?php

namespace MODXSlim\Api\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use MODXSlim\Api\Exceptions\RestfulException;

class Restful
{
    /**
     * @var array Defines the default HTTP methods allowed by a Restful route.
     */
    private $allowedMethods = ['DELETE', 'GET', 'HEAD', 'PATCH', 'POST', 'PUT', 'OPTIONS'];

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     *
     * @throws RestfulException
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response();
            return $response->withHeader('Allow', implode(',', $this->allowedMethods));
        }

        $this->validate($request);

        return $handler->handle($request);
    }

    /**
     * Return an instance of this middleware with the specified allowed HTTP methods.
     *
     * @param array $methods An array of allowed HTTP methods for a route.
     *
     * @return Restful
     */
    public function withAllowedMethods(array $methods): Restful
    {
        $clone = clone $this;
        $clone->allowedMethods = $methods;

        return $clone;
    }

    /**
     * Validate a request for a Restful route.
     *
     * @param ServerRequestInterface $request
     *
     * @throws RestfulException
     */
    private function validate(ServerRequestInterface $request)
    {
        if (!$this->checkAllowedMethods($request)) {
            throw RestfulException::methodNotAllowed(
                [
                    'hint' => 'This resource supports the following HTTP methods: ' . implode(', ', $this->allowedMethods),
                ],
                [
                    'Allow' => implode(',', $this->allowedMethods)
                ]
            );
        }
    }

    /**
     * Check if the requested HTTP method is allowed on this route.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    private function checkAllowedMethods(ServerRequestInterface $request): bool
    {
        return in_array($request->getMethod(), $this->allowedMethods);
    }
}
