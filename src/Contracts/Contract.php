<?php


namespace Skopa\EthereumWallet\Contracts;

use Skopa\EthereumWallet\Exceptions\ABIException;
use Skopa\EthereumWallet\Utils;

/**
 * Class Contract
 * @package Skopa\EthereumWallet\Contracts
 */
abstract class Contract implements ContractInterface
{
    /**
     * @var array
     */
    protected $ABI = [];

    /**
     * @var \Web3p\EthereumUtil\Util
     */
    protected $utils;

    /**
     * Contract constructor.
     * @throws ABIException
     */
    public function __construct()
    {
        $this->utils = Utils::getInstance();
        $this->processABI($this->getABIJson());
    }

    /**
     * Basic contract functions response formatter
     *
     * @param string $bytes
     * @param string $format
     * @return string
     */
    public function decodeResult(string $bytes, string $format): string
    {
        $bytes = $this->utils->stripZero($bytes);

        switch ($format) {
            case 'string':
                // Parse string
                $bytes = substr($bytes, 64);
                $len = hexdec(substr($bytes, 0, 64));
                return hex2bin(substr($bytes, 64, $len * 2));
            case 'address':
                return '0x' . substr($bytes, 64);
            case 'uint8':
                return hexdec($bytes);
            default:
                return $bytes;
        }
    }

    /**
     * Generate function cutted sha3
     *
     * @param string $function
     * @param string $default
     * @return string
     * @throws ABIException
     */
    protected function functionSha(string $function, string $default = null): string
    {
        if (!key_exists($function, $this->ABI) && is_null($default)) {
            throw ABIException::functionNotExist($function);
        }

        return mb_strcut(
            $this->utils->sha3($this->ABI[$function]['input'] ?? $default),
            0,
            8
        );
    }

    /**
     * Format argument to general length
     *
     * @param $value
     * @return string
     */
    protected function formattedArg(string $value): string
    {
        return str_pad($value, 64, '0', STR_PAD_LEFT);
    }

    /**
     * Format address
     *
     * @param string $address
     * @return string
     */
    protected function formattedAddress(string $address): string
    {
        return $this->formattedArg($this->utils->stripZero($address));
    }

    /**
     * Process ABI
     *
     * @param string $abi
     * @throws ABIException
     */
    private function processABI(string $abi = null): void
    {
        if (is_null($abi)) {
            return;
        }

        // Try to parse json
        try {
            $array = json_decode($abi, true);
        } catch (\Exception $exception) {
            throw ABIException::parseException($exception);
        }

        // Process to necessary format
        foreach ($array as $item) {
            // Except not functions
            if ($item['type'] !== 'function') {
                continue;
            }

            // Map input types
            $inputs = array_map(function ($item) {
                return $item['type'];
            }, $item['inputs']);

            // Save
            $this->ABI[$item['name']]['input'] = $item['name'] . '(' . implode(',', $inputs) . ')';
            $this->ABI[$item['name']]['output'] = $item['outputs'];
        }
    }
}
