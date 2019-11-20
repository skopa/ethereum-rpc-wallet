<?php


namespace Skopa\Ethereum;


use BI\BigInteger;
use Web3p\EthereumUtil\Util;

/**
 * Class Utils
 * @package Skopa\Ethereum
 * @see Util
 */
class Utils extends Util
{
    /**
     * @var Utils
     */
    private static $utils;

    /**
     * @return Utils
     */
    public static function getInstance()
    {
        return static::$utils ?? static::$utils = new Utils();
    }

    /**
     * @param $hex
     * @return string
     */
    public function parseHex(string $hex)
    {
        return BigInteger::createSafe($hex, 16)->toString();
    }
}
