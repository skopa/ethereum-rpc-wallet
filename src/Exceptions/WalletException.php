<?php


namespace Skopa\EthereumWallet\Exceptions;


/**
 * Class WalletException
 * @package Skopa\EthereumWallet\Exceptions
 */
class WalletException extends EthereumClientException
{
    /**
     * @return WalletException
     */
    public static function alreadyUnlocked(): WalletException
    {
        return new static("Wallet already unlocked.");
    }

    /**
     * @return WalletException
     */
    public static function keyNotSame(): WalletException
    {
        return new static("Private and public keys are not from one address.");
    }

    /**
     * @param string $hash
     * @return WalletException
     */
    public static function transactionNotExist(string $hash): WalletException
    {
        return new static("Transaction not exist or invalid transaction hash: $hash");
    }
}
