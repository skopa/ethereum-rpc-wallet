<?php


namespace Skopa\EthereumWallet;


use BI\BigInteger;
use Brick\Math\BigDecimal;
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
     * @param BigDecimal $amount
     * @param int $decimals
     * @return string
     */
    public static function amountToDecimalHex(BigDecimal $amount, int $decimals)
    {
        $natural = $amount
            ->multipliedBy(BigDecimal::of(10)->power($decimals))
            ->toBigInteger()
            ->__toString();

        return BigInteger::createSafe($natural)->toHex();
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

    //TODO: Add formatting for other types.
}
