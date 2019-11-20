<?php


namespace Skopa\EthereumWallet;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Skopa\EthereumWallet\Exceptions\RequestException;

/**
 * Class JsonRpcNetworkClient
 * @package Skopa\EthereumWallet
 */
class JsonRpcNetworkClient
{
    /**
     * @var Client
     */
    protected $httpClient;
    /**
     * @var string version
     */
    private $jsonrpc = "2.0";
    /**
     * @var string
     */
    private $chain;

    /**
     * NetworkClient constructor.
     * @param $url string network url
     * @param string $chain
     */
    public function __construct(string $url, string $chain)
    {
        $this->chain = $chain;
        $this->httpClient = new Client([
            'http_errors' => false,
            'base_uri' => $url,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    /**
     * Send jsonrpc request to network
     *
     * @param array $request Array of requests
     * @return array|string String if one and array if mass requests
     * @throws GuzzleException
     * @throws RequestException
     */
    public function request(array $request)
    {
        $response = $this->httpClient->request('POST', '', [
            'json' => $request,
        ]);

        if ($response->getStatusCode() !== 200) {
            throw RequestException::serverException($response->getStatusCode(), $response->getReasonPhrase());
        }

        $data = json_decode($response->getBody()->getContents(), true);

        $callback = function ($data) {
            // Check for error
            if (key_exists('error', $data)) {
                throw RequestException::fromResponse($data['error']['message'], $data['error']['code']);
            }

            return $data['result'];
        };

        $response = array_map($callback, $data);

        return count($response) > 1 ? $response : $response[0];
    }

    /**
     * Call method as signed
     *
     * @param $transaction
     * @return array|string
     */
    public function sendRawTransaction($transaction)
    {
        return $this->jsonrpc('eth_sendRawTransaction', [$transaction]);
    }

    /**
     * Estimate necessary gas for transaction
     *
     * @param $properties
     * @return array
     */
    public function estimateGas($properties)
    {
        return $this->jsonrpc('eth_estimateGas', [$properties]);
    }

    /**
     * Get gas price
     *
     * @return array
     */
    public function gasPrice(): array
    {
        return $this->jsonrpc('eth_gasPrice');
    }

    /**
     * Get count of transactions (nonce)
     *
     * @param string $account
     * @param string $status
     * @return array
     */
    public function getTransactionCount(string $account, string $status = 'pending'): array
    {
        return $this->jsonrpc('eth_getTransactionCount', [$account, $status]);
    }

    /**
     * Call public method of contract
     *
     * @param string $contract
     * @param string $data
     * @param string $status
     * @return array
     */
    public function rawCall(string $contract, string $data, string $status = 'pending')
    {
        return $this->jsonrpc('eth_call', [[
            "data" => $data,
            "to" => $contract
        ], $status]);
    }

    /**
     * Get transaction by hash
     *
     * @param string $hash
     * @return array
     */
    public function getTransactionByHash(string $hash): array
    {
        return $this->jsonrpc('eth_getTransactionByHash', [
            $hash
        ]);
    }

    /**
     * Get transaction receipt by hash
     *
     * @param string $hash
     * @return array
     */
    public function getTransactionReceipt(string $hash): array
    {
        return $this->jsonrpc('eth_getTransactionReceipt', [
            $hash
        ]);
    }

    /**
     * Get block info by hash
     *
     * @param string $hash
     * @param bool $onlyHashes
     * @return array
     */
    public function getBlockByHash(string $hash, bool $onlyHashes = true): array
    {
        return $this->jsonrpc('eth_getBlockByHash', [
            $hash, !$onlyHashes
        ]);
    }

    /**
     * Get chain id
     *
     * @return string
     */
    public function getChainId(): string
    {
        return $this->chain;
    }

    /**
     * Generate request body
     *
     * @param string $method
     * @param array $params
     * @return array
     */
    protected function jsonrpc(string $method, array $params = [])
    {
        return [
            'id' => $this->getRequestId(),
            'jsonrpc' => $this->jsonrpc,
            'method' => $method,
            'params' => $params,
        ];
    }

    /**
     * Generate random string for identity request
     *
     * @return string
     */
    protected function getRequestId()
    {
        return md5(time());
    }
}
