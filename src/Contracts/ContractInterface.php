<?php


namespace Skopa\Ethereum\Contracts;

/**
 * Interface ContractInterface
 * @package Skopa\Ethereum\Contracts
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
