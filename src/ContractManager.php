<?php


namespace Skopa\Ethereum;


use Brick\Math\BigDecimal;
use Skopa\Ethereum\Contracts\ERC20TokenContract;
use Web3p\EthereumTx\Transaction as EthereumTransaction;

/**
 * Class ContractManager
 * @package Skopa\Ethereum
 */
class ContractManager
{
    /**
     * @var JsonRpcNetworkClient
     */
    private $networkClient;
    /**
     * @var ERC20TokenContract
     */
    private $contract;
    /**
     * @var Wallet
     */
    private $wallet;

    /**
     * ContractManager constructor.
     * @param JsonRpcNetworkClient $networkClient
     * @param ERC20TokenContract $contract
     * @param Wallet $wallet
     */
    public function __construct(JsonRpcNetworkClient $networkClient, ERC20TokenContract $contract, Wallet $wallet)
    {
        $this->networkClient = $networkClient;
        $this->contract = $contract;
        $this->wallet = $wallet;
    }

    /**
     * Get token info by contract
     *
     * @throws Exceptions\ABIException
     * @throws Exceptions\RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestContractTokenData(): array
    {
        list($name, $decimals, $symbol) = $this->networkClient->request([
            $this->networkClient->rawCall($this->contract->getAddress(), $this->contract->name()),
            $this->networkClient->rawCall($this->contract->getAddress(), $this->contract->decimals()),
            $this->networkClient->rawCall($this->contract->getAddress(), $this->contract->symbol()),
        ]);

        return [
            'name' => $this->contract->decodeResult($name, 'string'),
            'decimals' => $this->contract->decodeResult($decimals, 'uint8'),
            'symbol' => $this->contract->decodeResult($symbol, 'string'),
        ];
    }

    /**
     * Get balance of requested tokens by contract
     *
     * @return BigDecimal
     * @throws Exceptions\ABIException
     * @throws Exceptions\RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function balance(): BigDecimal
    {
        return $this->balanceOf($this->wallet->getAccount());
    }

    /**
     * Get balance by address of requested tokens by contract
     *
     * @param string $address
     * @return BigDecimal
     * @throws Exceptions\ABIException
     * @throws Exceptions\RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function balanceOf(string $address): BigDecimal
    {
        $balance = $this->networkClient->request([
            $this->networkClient->rawCall(
                $this->contract->getAddress(),
                $this->contract->balanceOf($address)
            ),
        ]);

        return $this->convertAmount($balance);
    }

    /**
     * Transfer requested tokens by contact
     *
     * @param Transaction $transaction
     * @return string
     * @throws Exceptions\ABIException
     * @throws Exceptions\RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function transfer(Transaction $transaction): string
    {
        $data = $this->contract->transfer(
            $transaction->getReceiver(),
            $transaction->getHexAmount($this->contract->getDecimals())
        );

        return $this->networkClient->request([
            $this->networkClient->sendRawTransaction(
                $this->tokenTransaction($data)
            )
        ]);
    }

    /**
     * Authorize receiver to spend tokens of requested contract
     *
     * @param Transaction $transaction
     * @return string
     * @throws Exceptions\ABIException
     * @throws Exceptions\RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function approve(Transaction $transaction): string
    {
        $data = $this->contract->approve(
            $transaction->getReceiver(),
            $transaction->getHexAmount($this->contract->getDecimals())
        );

        return $this->networkClient->request([
            $this->networkClient->sendRawTransaction(
                $this->tokenTransaction($data)
            )
        ]);
    }

    /**
     * Return approved value of token possible to spend
     *
     * @param string $spender
     * @return BigDecimal
     * @throws Exceptions\ABIException
     * @throws Exceptions\RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function allowance(string $spender): string
    {
        return $this->allowanceBy($this->wallet->getAccount(), $spender);
    }

    /**
     * Return approved value of token possible to spend by spender from owner
     *
     * @param string $owner
     * @param string $spender
     * @return BigDecimal
     * @throws Exceptions\ABIException
     * @throws Exceptions\RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function allowanceBy(string $owner, string $spender): BigDecimal
    {
        $allowance = $this->networkClient->request([
            $this->networkClient->rawCall(
                $this->contract->getAddress(),
                $this->contract->allowance($owner, $spender)
            )
        ]);

        return $this->convertAmount($allowance);
    }

    /**
     * Spent approved amount of tokens of requested contract
     *
     * @param Transaction $transaction
     * @return array|string
     * @throws Exceptions\ABIException
     * @throws Exceptions\RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function transferFrom(Transaction $transaction): string
    {
        $data = $this->contract->transferFrom(
            $transaction->getReceiver(),
            $this->wallet->getAccount(),
            $transaction->getHexAmount($this->contract->getDecimals())
        );

        return $this->networkClient->request([
            $this->networkClient->sendRawTransaction(
                $this->tokenTransaction($data)
            )
        ]);
    }

    /**
     * Convert from hex to BigDecimal
     *
     * @param string $rawHex hex string
     * @return BigDecimal
     */
    public function convertAmount(string $rawHex): BigDecimal
    {
        $rawDec = Utils::getInstance()->parseHex($rawHex);
        return BigDecimal::ofUnscaledValue($rawDec, $this->contract->getDecimals());
    }

    /**
     * Generate and sign transaction
     *
     * @param string $data
     * @return string
     * @throws Exceptions\RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function tokenTransaction(string $data): string
    {
        list($nonce, $gasPrice, $gasLimit) = $this->networkClient->request([
            $this->networkClient->getTransactionCount($this->wallet->getAccount()),
            $this->networkClient->gasPrice(),
            $this->networkClient->estimateGas([
                "from" => $this->wallet->getAccount(),
                "to" => $this->contract->getAddress(),
                "value" => "0x0",
                "data" => $data,
            ]),
        ]);

        $transaction = new EthereumTransaction([
            "data" => $data,
            "gasLimit" => $gasLimit,
            "gasPrice" => $gasPrice,
            "to" => $this->contract->getAddress(),
            "value" => "0x0",
            "nonce" => $nonce,
            "from" => $this->wallet->getAccount(),
            "chainId" => $this->networkClient->getChainId(),
        ]);

        return '0x' . $this->wallet->sign($transaction);
    }
}
