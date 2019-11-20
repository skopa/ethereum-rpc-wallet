<?php


namespace Skopa\Ethereum\Exceptions;

/**
 * Class RequestException
 * @package Skopa\Ethereum\Exceptions
 */
class RequestException extends EthereumClientException
{
    /**
     * @param string $message
     * @param int $code
     * @return RequestException
     */
    public static function fromResponse(string $message, int $code)
    {
        return new RequestException("One of requests exit with exception: $message", $code);
    }

    /**
     * @param int $code
     * @param string $message
     * @return RequestException
     */
    public static function serverException(int $code, string $message)
    {
        return new RequestException("Wrong response from JSON RPC (Response code: $code): $message");
    }
}
