<?php


namespace Skopa\EthereumWallet;


use Skopa\EthereumWallet\Contracts\ERC20TokenContract;
use Skopa\EthereumWallet\Exceptions\WalletException;
use Web3p\EthereumTx\Transaction as EthereumTransaction;
use Web3p\EthereumUtil\Util;

/**
 * Class Wallet
 * @package Skopa\EthereumWallet
 */
class Wallet
{
    /**
     * @var string
     */
    private $privateKey;
    /**
     * @var string
     */
    private $account;
    /**
     * @var Util
     */
    private $utils;
    /**
     * @var JsonRpcNetworkClient
     */
    private $networkClient;

    /**
     * Wallet constructor.
     * @param JsonRpcNetworkClient $networkClient
     * @param string $account
     * @param string $privateKey
     */
    private function __construct(JsonRpcNetworkClient $networkClient, string $account = null, string $privateKey = null)
    {
        $this->utils = Utils::getInstance();
        $this->networkClient = $networkClient;
        $this->privateKey = $privateKey;
        $this->account = $account ?? $this->utils->publicKeyToAddress(
                $this->utils->privateKeyToPublicKey($privateKey)
            );
    }

    /**
     * Create wallet from private key
     *
     * @param JsonRpcNetworkClient $network
     * @param string $privateKey
     * @return Wallet
     */
    public static function fromPrivateKey(JsonRpcNetworkClient $network, string $privateKey): Wallet
    {
        return new static($network, null, $privateKey);
    }

    /**
     * Create wallet from public address
     *
     * @param JsonRpcNetworkClient $network
     * @param string $account
     * @return Wallet
     */
    public static function fromPublicAddress(JsonRpcNetworkClient $network, string $account): Wallet
    {
        return new static($network, $account);
    }

    /**
     * Get transaction receipt by hash
     *
     * @param string $hash
     * @return TransactionReceipt
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws WalletException
     * @throws Exceptions\RequestException
     */
    public function getReceipt(string $hash): TransactionReceipt
    {
        list($transaction, $receipt) = $this->networkClient->request([
            $this->networkClient->getTransactionByHash($hash),
            $this->networkClient->getTransactionReceipt($hash),
        ]);

        if (is_null($transaction) || is_null($receipt)) {
            throw WalletException::transactionNotExist($hash);
        }

        $block = $this->networkClient->request([
            $this->networkClient->getBlockByHash($transaction['blockHash'])
        ]);

        return TransactionReceipt::fromArray($transaction, $receipt, $block);
    }

    /**
     * @return string
     */
    public function getAccount(): string
    {
        return $this->account;
    }

    /**
     * Unlock wallet
     *
     * @param string $privateKey
     * @return bool
     * @throws WalletException
     */
    public function unlock(string $privateKey): bool
    {
        if ($this->privateKey !== null) {
            throw WalletException::alreadyUnlocked();
        }

        $keyAccount = $this->utils->publicKeyToAddress(
            $this->utils->privateKeyToPublicKey($privateKey)
        );

        if (strcmp(mb_strtolower($keyAccount), mb_strtolower($this->account)) !== 0) {
            throw WalletException::keyNotSame();
        }

        $this->privateKey = $privateKey;
        return true;
    }

    /**
     * Sign transaction
     *
     * @param EthereumTransaction $transaction
     * @return string
     */
    public function sign(EthereumTransaction $transaction): string
    {
        return $transaction->sign($this->privateKey);
    }

    /**
     * Manage contract
     *
     * @param ERC20TokenContract $tokenContract
     * @return ContractManager
     */
    public function contract(ERC20TokenContract $tokenContract): ContractManager
    {
        return new ContractManager($this->networkClient, $tokenContract, $this);
    }
}
