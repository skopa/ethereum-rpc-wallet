# Ethereum JSONRPC Wallet

This package provide quick and simple interface 
to interact with your deployed contracts. For now 
realized full compatibility with ERC20 tokens.
Also you can interact with any other your contract 
by using ABI code.

**Not all responses are realized for now! 
Only static types: _string, address, uint_**

#### THIS PACKAGE IS PROVIDE "AS IS"
Be careful that your case of using are fully tested!

## Examples
Only few steps to start interact with your contract.
Before this your contract must be successfully deployed
on the blockchain network.

##### 1.Create your contract class:
For creating and interacting with contract you need
to extend base class: 
`\Skopa\EthereumWallet\Contracts\ERC20TokenContract`

And define next methods:
* **getAddress()** - must return address of your contract
* **getABIJson()** - must return json string of ABI code.
Can be ignored for ERC20 contracts.
* **getDecimals()** - must return number of decimals

```php
use \Skopa\EthereumWallet\Contracts\ERC20TokenContract;

class ExampleContract extends ERC20TokenContract
{
    public function getAddress(): string
    {
        return 'Contract address here';
    }
    
    public function getABIJson(): ?string
    {
        return file_get_contents('ABI.json');
    }

    public function getDecimals(): int
    {
        return 18;
    }
}

$exampleContract = new ExampleToken();
```

##### 2.Connect to JSON RPC Geth server:

Setup the connection to your Geth server. How to do it you can find
at https://github.com/ethereum/go-ethereum/wiki.

```php
$networkClient = new JsonRpcNetworkClient(
    'http://localhost:8545', 
    '90'
);
```

First parameter is server address with port. Second is chain id.


#### 3. Create wallet instance:

**Using public address:**
```php
$wallet = \Skopa\EthereumWallet\Wallet::fromPublicAddress(
    $network,
    'Address in format: 0x...'
);
```

If in future you will need to do signed actions you can 
just 'unlock' your wallet. If private key will from other wallet 
you will receive exception. 

```php
$wallet->unlock('private key');
```

**Using private key:**
```php
$wallet = \Skopa\EthereumWallet\Wallet::fromPrivateKey(
    $network,
    'private key'
);
```
In this case wallet will unlocked from start.

#### 4.Interact with contract:

To interact with you ERC20 contract just pass previously created
instance of ``ExampleContract`` to wallet method as on example:
```php
$contractManager = $wallet->contract($exampleContract);
```
The ``$contractManager`` is the instance of 
``\Skopa\EthereumWallet\ERC20ContractManager`` that provide to you 
methods of interactions:
  - requestContractTokenData()
  - balance()
  - balanceOf(string $address)
  - transfer(Transaction $transaction)
  - approve(Transaction $transaction)
  - allowance(string $spender)
  - allowanceBy(string $owner, string $spender)
  - transferFrom(Transaction $transaction)

Each method has documentation so you can investigate it by you-self.

#### 4.1 Interaction with custom contract:
You also can use this lib to interact with your custom contacts.
You can create instance of your contract by extending class 
of ``Skopa\EthereumWallet\Contracts\Contract``.
In this case you can create you own contract non compatible with
ERC20 and interact with it or event expand ERC20 contract with custom tokens.
But you will must provide ABI code.

```php
class ExampleContract extends Contract
{
    public function myMethod(string $uint)
    {
        return '0x'
            . $this->functionSha('myMethod')
            . $this->formattedArg($amount);
    }
}
```
Get contract manager and interact with it:
```php
$contract = new ExampleContract;

$contractManager = $wallet->customContract($contract);
```

The ``Skopa\EthereumWallet\ContractManager`` returned by ``customContract`` method
has two methods to interact with contract:
 - callDirectly(\Closure $closure) - call contract methods without sign
 - callDirectlySigned(\Closure $closure) - call contract method by transaction
 
```php
$custom = $contractManager->callDirectly(function (ExampleContract $token) {
    return $token->mint(\Skopa\EthereumWallet\Utils::amountToDecimalHex(
        \Brick\Math\BigDecimal::of('10')
    ));
});
```

Of course you can extend ERC20 contract too:
```php
class ExampleContract extends ERC20TokenContract
{
    public function getAddress(): string
    {
        // ...
    }

    public function getABIJson(): ?string
    {
        // ...
    }

    public function getDecimals(): int
    {
        // ...
    }
    
    public function mint(string $amount)
    {
        return '0x'
            . $this->functionSha('mint')
            . $this->formattedArg($amount);
    }
}
```

And interact with it in same way:
```php
$custom = $contractManager->callDirectlySigned(function (ExampleContract $token) {
    return $token->mint(\Skopa\EthereumWallet\Utils::amountToDecimalHex(
        \Brick\Math\BigDecimal::of('10'), $token->getDecimals()
    ));
});
```

#### 5. Transaction:
To do some transactions you just need to create transaction instance
with recipient and amount. And send it by ``transfer`` method of ``$contractManager``.
This method will the return transaction hash.
```php
$transaction = new \Skopa\EthereumWallet\Transaction('recipient address', '2.4')

$res = $contractManager->transfer($transaction);
```

**Reminder: Transaction are not immediate action (by blockchain spec).**

#### 6. Get transaction data:
You can get next data of transaction of your ERC20 contracts:
 - from - sender address
 - to - receiver address
 - contract - address of contract
 - timestamp - timestamp of block
 - amount - amount of tokens
 - success - boolean is transaction is success

To get this data you need call ``receipt`` on wallet instance:
```php
$receipt = $wallet->receipt('transaction hash');
``` 

#### 7. Additionally
Also lib contains some helpers like converters from decimal to hex and other. 
You can to get acquainted with it in source code. 

### Summary
This lib is not finished and can contain some bugs and exceptions. So it is
distributed 'AS IS'.

To say thanks send some coins here: 0xfd9c54573dd27f23d3c8df154bd550df4c44bd8a
