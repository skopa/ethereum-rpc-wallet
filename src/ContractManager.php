<?php


namespace Skopa\EthereumWallet;


use Skopa\EthereumWallet\Contracts\Contract;
use Skopa\EthereumWallet\Exceptions\RequestException;
use Web3p\EthereumTx\Transaction as EthereumTransaction;


class ContractManager
{
    /**
     * @var JsonRpcNetworkClient
     */
    protected $networkClient;
    /**
     * @var Contract
     */
    protected $contract;
    /**
     * @var Wallet
     */
    protected $wallet;

    /**
     * ContractManager constructor.
     * @param JsonRpcNetworkClient $networkClient
     * @param Contract $contract
     * @param Wallet $wallet
     */
    public function __construct(JsonRpcNetworkClient $networkClient, Contract $contract, Wallet $wallet)
    {
        $this->networkClient = $networkClient;
        $this->contract = $contract;
        $this->wallet = $wallet;
    }

    /**
     * @param \Closure $closure
     * @return array|string
     * @throws Exceptions\RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function callDirectly(\Closure $closure)
    {
        return $this->networkClient->request([
            $this->networkClient->rawCall($this->contract->getAddress(), $closure($this->contract))
        ]);
    }

    /**
     * @param \Closure $closure
     * @return array|string
     * @throws RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function callDirectlySigned(\Closure $closure)
    {
        return $this->networkClient->request([
            $this->networkClient->sendRawTransaction(
                $this->transaction($closure($this->contract))
            )
        ]);
    }

    /**
     * Generate and sign transaction
     *
     * @param string $data
     * @return string
     * @throws Exceptions\RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function transaction(string $data): string
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
