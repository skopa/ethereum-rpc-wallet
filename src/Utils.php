<?php


namespace Skopa\EthereumWallet;


use BI\BigInteger;
use Web3p\EthereumUtil\Util;

/**
 * Class Utils
 * @package Skopa\EthereumWallet
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
     * Parse hex to dec string
     *
     * @param $hex
     * @return string
     */
    public static function parseHex(string $hex)
    {
        return BigInteger::createSafe($hex, 16)->toDec();
    }

    /**
     * Static access to methods
     *
     * @param $name
     * @param $arguments
     * @return mixed|null
     */
    public static function __callStatic($name, $arguments)
    {
        try {
            $method = new \ReflectionMethod(static::getInstance(), $name);
            return call_user_func($method->getClosure(), $arguments);
        } catch (\Exception $e) {
            return null;
        }
    }
}
