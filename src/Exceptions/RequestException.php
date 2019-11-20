<?php


namespace Skopa\EthereumWallet\Exceptions;

/**
 * Class RequestException
 * @package Skopa\EthereumWallet\Exceptions
 */
class RequestException extends EthereumClientException
{
    /**
     * @param string $message
     * @param int $code
     * @return RequestException
     */
    public static function fromResponse(string $message, int $code): RequestException
    {
        return new RequestException("One of requests exit with exception: $message", $code);
    }

    /**
     * @param int $code
     * @param string $message
     * @return RequestException
     */
    public static function serverException(int $code, string $message): RequestException
    {
        return new RequestException("Wrong response from JSON RPC (Response code: $code): $message");
    }
}
