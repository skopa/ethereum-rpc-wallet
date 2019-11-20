<?php


namespace Skopa\Ethereum\Contracts;


use Brick\Math\BigDecimal;

/**
 * Class ERC20TokenContract
 * @package Skopa\Ethereum\Contracts
 */
abstract class ERC20TokenContract extends Contract
{
    /**
     * @return int
     */
    public abstract function getDecimals(): int;

    /**
     * @return string
     * @throws \Skopa\Ethereum\Exceptions\ABIException
     */
    public function name(): string
    {
        return '0x' . $this->functionSha('name');
    }

    /**
     * @return string
     * @throws \Skopa\Ethereum\Exceptions\ABIException
     */
    public function decimals(): string
    {
        return '0x' . $this->functionSha('decimals');
    }

    /**
     * @return string
     * @throws \Skopa\Ethereum\Exceptions\ABIException
     */
    public function symbol(): string
    {
        return '0x' . $this->functionSha('symbol');
    }

    /**
     * Returns the amount of tokens in existence.
     *
     * @return string
     * @throws \Skopa\Ethereum\Exceptions\ABIException
     */
    public function totalSupply(): string
    {
        return '0x' . $this->functionSha('totalSupply');
    }

    /**
     * Returns the amount of tokens owned by `account`.
     *
     * @param $account string Account address
     * @return string
     * @throws \Skopa\Ethereum\Exceptions\ABIException
     */
    public function balanceOf(string $account): string
    {
        return '0x'
            . $this->functionSha('balanceOf')
            . $this->formattedAddress($account);
    }

    /**
     * Moves `amount` tokens from the caller's account to `recipient`.
     *
     * Emits a {Transfer} event.
     *
     * @param $recipient string Receiver address
     * @param $amount string Amount to sent
     * @return string Transactions data string
     * @throws \Skopa\Ethereum\Exceptions\ABIException
     */
    public function transfer(string $recipient, string $amount): string
    {
        return '0x'
            . $this->functionSha('transfer')
            . $this->formattedAddress($recipient)
            . $this->formattedArg($amount);
    }

    /**
     * Returns the remaining number of tokens that `spender` will be
     * allowed to spend on behalf of `owner` through {transferFrom}. This is
     * zero by default.
     *
     * This value changes when {approve} or {transferFrom} are called.
     *
     * @param $owner string Address of owner
     * @param $spender string Address of spender
     * @return string
     * @throws \Skopa\Ethereum\Exceptions\ABIException
     */
    function allowance(string $owner, string $spender): string
    {
        return '0x'
            . $this->functionSha('allowance')
            . $this->formattedAddress($owner)
            . $this->formattedAddress($spender);
    }

    /**
     * Sets `amount` as the allowance of `spender` over the caller's tokens.
     *
     * Returns a boolean value indicating whether the operation succeeded.
     *
     * IMPORTANT: Beware that changing an allowance with this method brings the risk
     * that someone may use both the old and the new allowance by unfortunate
     * transaction ordering. One possible solution to mitigate this race
     * condition is to first reduce the spender's allowance to 0 and set the
     * desired value afterwards:
     * https://github.com/ethereum/EIPs/issues/20#issuecomment-263524729
     *
     * Emits an {Approval} event.
     * @param string $spender Address of spender
     * @param string $amount Amount
     * @return bool
     * @throws \Skopa\Ethereum\Exceptions\ABIException
     */
    function approve(string $spender, string $amount): bool
    {
        return '0x'
            . $this->functionSha('approve')
            . $this->formattedAddress($spender)
            . $this->formattedArg($amount);
    }

    /**
     * Moves `amount` tokens from `sender` to `recipient` using the
     * allowance mechanism. `amount` is then deducted from the caller's
     * allowance.
     *
     * Returns a boolean value indicating whether the operation succeeded.
     *
     * Emits a {Transfer} event.
     * @param string $sender
     * @param string $recipient
     * @param string $amount
     * @return bool
     * @throws \Skopa\Ethereum\Exceptions\ABIException
     */
    function transferFrom(string $sender, string $recipient, string $amount): bool
    {
        return '0x'
            . $this->functionSha('transferFrom')
            . $this->formattedAddress($sender)
            . $this->formattedAddress($recipient)
            . $this->formattedArg($amount);
    }
}
