<?php


namespace Skopa\Ethereum;

use BI\BigInteger;
use Brick\Math\BigDecimal;

/**
 * Class Transaction
 * @package Skopa\Ethereum
 */
class Transaction
{
    /**
     * @var string
     */
    private $receiver;
    /**
     * @var BigDecimal
     */
    private $amount;
    /**
     * @var \Web3p\EthereumUtil\Util
     */
    private $utils;

    /**
     * Transaction constructor.
     * @param string $receiver
     * @param mixed $amount
     */
    public function __construct(string $receiver, $amount)
    {
        $this->utils = Utils::getInstance();
        $this->receiver = $receiver;
        $this->amount = BigDecimal::of($this->utils->isHex($amount) ?
            BigInteger::createSafe($amount, 16)->toDec()
            : $amount
        );
    }

    /**
     * @return string
     */
    public function getReceiver(): string
    {
        return $this->receiver;
    }

    /**
     * @return BigDecimal
     */
    public function getAmount(): BigDecimal
    {
        return $this->amount;
    }

    /**
     * @param int $decimals
     * @return string
     */
    public function getHexAmount($decimals = 0)
    {
        $natural = $this->amount->multipliedBy(BigDecimal::of(10)->power($decimals))->toBigInteger();
        return BigInteger::createSafe($natural->__toString())->toHex();
    }
}
