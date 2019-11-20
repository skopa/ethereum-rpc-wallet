<?php


namespace Skopa\EthereumWallet\Contracts;

/**
 * Interface ContractInterface
 * @package Skopa\EthereumWallet\Contracts
 */
interface ContractInterface
{
    /**
     * Get contract address
     *
     * @return string
     */
    public function getAddress(): string;

    /**
     * Get contract ABI
     *
     * @return string|null
     */
    public function getABIJson(): ?string;
}
