<?php


namespace Skopa\Ethereum;


use Brick\Math\BigDecimal;

/**
 * Class TransactionReceipt
 * @package Skopa\Ethereum
 */
class TransactionReceipt
{
    /**
     * @var bool
     */
    public $success;
    /**
     * @var string
     */
    public $hash;
    /**
     * @var string
     */
    public $from;
    /**
     * @var string
     */
    public $to;
    /**
     * @var BigDecimal
     */
    public $amount;
    /**
     * @var string
     */
    public $contract;
    /**
     * @var int
     */
    public $timestamp;

    /**
     * TransactionReceipt constructor.
     * @param array $transaction
     * @param array $receipt
     * @param int $timestamp
     */
    protected function __construct(array $transaction, array $receipt, int $timestamp)
    {
        $this->timestamp = $timestamp;
        $this->hash = $transaction['hash'];
        $this->from = $receipt['from'];
        $this->contract = $transaction['to'];
        $this->success = (bool)hexdec($receipt['status']);
        list($this->to, $this->amount) = array_values($this->parseERC20($transaction['input']));
    }

    /**
     * Wrap in transaction
     *
     * @param array $transaction
     * @param array $receipt
     * @param array $block
     * @return TransactionReceipt
     */
    public static function fromArray(array $transaction, array $receipt, array $block): TransactionReceipt
    {
        return new TransactionReceipt($transaction, $receipt, $block['timestamp']);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'hash' => $this->hash,
            'from' => $this->from,
            'to' => $this->to,
            'contract' => $this->contract,
            'timestamp' => $this->timestamp,
            'amount' => $this->amount,
            'success' => $this->success,
        ];
    }

    /**
     * Parse ERC20 token transaction
     *
     * @param $data
     * @return array
     */
    protected function parseERC20($data): array
    {
        $data = substr($data, 10);

        return [
            'to' => '0x' . substr($data, 24, 40),
            'amount' => '0x' . substr($data, 64)
        ];
    }
}
