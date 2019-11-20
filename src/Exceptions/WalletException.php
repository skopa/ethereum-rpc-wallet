<?php


namespace Skopa\Ethereum\Exceptions;


/**
 * Class WalletException
 * @package Skopa\Ethereum\Exceptions
 */
class WalletException extends EthereumClientException
{
    /**
     * @return WalletException
     */
    public static function alreadyUnlocked()
    {
        return new static("Wallet already unlocked.");
    }

    /**
     * @return WalletException
     */
    public static function keyNotSame()
    {
        return new static("Private and public keys are not from one address.");
    }

    /**
     * @return RequestException
     */
    public static function transactionNotExist()
    {
        return new RequestException("Transaction not exist or invalid transaction hash.");
    }
}
