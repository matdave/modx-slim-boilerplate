<?php

namespace MODXSlim\Api\Renderers;

use Slim\Error\AbstractErrorRenderer;
use Slim\Interfaces\ErrorRendererInterface;
use Throwable;

class HttpExceptionRenderer extends AbstractErrorRenderer
{

    /**
     * @param Throwable $exception
     * @param bool $displayErrorDetails
     * @param \Slim\Interfaces\ErrorRendererInterface|null $fallBackRenderer
     *
     * @return string
     */
    public function __invoke(Throwable $exception, bool $displayErrorDetails, ErrorRendererInterface $fallBackRenderer = null): string
    {
        if ($exception instanceof \MODXSlim\Api\Exceptions\RestfulException) {
            $body = $exception->getBody();
            $body['instance'] = (isset($body['instance'])) ? $body['instance'] : '';
            $text = "{$body['status']}; {$body['title']}; {$body['detail']}; {$body['instance']}";

            if ($displayErrorDetails) {
                $text .= '; DETAILS: ' . $this->formatExceptionFragment($exception);

                while ($exception = $exception->getPrevious()) {
                    $text .= "\nPrevious Error:\n";
                    $text .= $this->formatExceptionFragment($exception);
                }
            }

            return $text;
        }

        if (!empty($fallBackRenderer)) {
            return $fallBackRenderer($exception, $displayErrorDetails);
        }

        return '';

    }

    /**
     * @param \MODXSlim\Api\Exceptions\RestfulException $exception
     *
     * @return string
     */
    private function formatExceptionFragment(\MODXSlim\Api\Exceptions\RestfulException $exception): string
    {
        $text = sprintf("Type: %s\n", get_class($exception));

        $code = $exception->getCode();
        if ($code !== null) {
            $text .= sprintf("Code: %s\n", $code);
        }

        $message = $exception->getMessage();
        if ($message !== null) {
            $text .= sprintf("Message: %s\n", htmlentities($message));
        }

        $file = $exception->getFile();
        if ($file !== null) {
            $text .= sprintf("File: %s\n", $file);
        }

        $line = $exception->getLine();
        if ($line !== null) {
            $text .= sprintf("Line: %s\n", $line);
        }

        $trace = $exception->getTraceAsString();
        if ($trace !== null) {
            $text .= sprintf('Trace: %s', $trace);
        }

        return $text;
    }
}
