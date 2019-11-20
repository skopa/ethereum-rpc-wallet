<?php


namespace Skopa\EthereumWallet\Exceptions;


use Exception;

/**
 * Class ABIException
 * @package Skopa\EthereumWallet\Exceptions
 */
class ABIException extends EthereumClientException
{
    /**
     * @param $functionName
     * @return ABIException
     */
    public static function functionNotExist($functionName): ABIException
    {
        return new static("Provided function: $functionName not exists in ABI.");
    }

    /**
     * @param Exception $exception
     * @return ABIException
     */
    public static function parseException(Exception $exception): ABIException
    {
        return new static(
            'ABI parsing exception: ' . $exception->getMessage(),
            $exception->getCode(),
            $exception
        );
    }
}
