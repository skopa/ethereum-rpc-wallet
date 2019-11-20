#Ethereum JSONRPC Wallet

This package provide quick and simple interface 
to interact with your deployed contracts. For now 
realized full compatibility with ERC20 tokens.
Also you can interact with any other your contract 
by using ABI code.

**Not all responses are realized for now! 
Only static types: _string, address, uint_**

#### THIS PACKAGE IS PROVIDE "AS IS"
Be careful that your case of using are fully tested!

##Examples

Only few steps to start interact with your contract.
Before this your contract must be successfully deployed
on the blockchain network.

#####1.Create your contract class:
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
        return file_get_contents('SkopaTestToken.json');
    }

    public function getDecimals(): int
    {
        return 18;
    }
}

$exampleContract = new ExampleToken();
```

#####2.Connect to JSON RPC Geth server:

```php
$networkClient = new JsonRpcNetworkClient(
    'http://localhost:8545', 
    '90'
);
```

First parameter is server address with port. Second is chain id.


####3. Create wallet instance:

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

//TODO: Continue.
####4.Interact with contract:
