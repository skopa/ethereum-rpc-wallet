<?php


namespace Skopa\EthereumWallet;

use BI\BigInteger;
use Brick\Math\BigDecimal;

/**
 * Class Transaction
 * @package Skopa\EthereumWallet
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
     * Transaction constructor.
     * @param string $receiver
     * @param mixed $amount
     */
    public function __construct(string $receiver, $amount)
    {
        $this->receiver = $receiver;
        $this->amount = BigDecimal::of(Utils::getInstance()->isHex($amount) ?
            BigInteger::createSafe($amount, 16)->toDec()
            : $amount
        );
    }

    /**
     * Get receiver
     *
     * @return string
     */
    public function getReceiver(): string
    {
        return $this->receiver;
    }

    /**
     * Get amount
     *
     * @return BigDecimal
     */
    public function getAmount(): BigDecimal
    {
        return $this->amount;
    }

    /**
     * Get hex representation with defined decimals
     *
     * @param int $decimals
     * @return string
     */
    public function getHexAmount($decimals = 0)
    {
        $natural = $this->amount
            ->multipliedBy(BigDecimal::of(10)->power($decimals))
            ->toBigInteger()
            ->__toString();

        return BigInteger::createSafe($natural)->toHex();
    }
}
