# Stefna Secrets Manager

## Table of Contents

1. [Installation](#installation)
2. [Api Documentation](#api-documentation)
	1. [Stefna\SecretsManager\Manager](#manager-class)
		1. [Initializing](#manager-constructor)
		2. [getSecret](#manager-getSecret)
		3. [putSecret](#manager-putSecret)
		4. [deleteSecret](#manager-deleteSecret)
		5. [getProvider](#manager-getProvider)
	2. [Stefna\SecretsManager\Secret](#secret-class)
		1. [getKey](#secret-getKey)
		1. [getValue](#secret-getValue)

## Installation

```bash
$ composer require stefna/secrets-manager-core
```

The core only provides the basic functionality and some basic inmemory providers. 

We provide a couple of providers that can be installed separately 

| Provider | Badges |
| -------------- | -------- |
| [JSON File](./packages/json-provider/) | Coming soon |
| [Psr-6](./packages/psr-6-provider/) | Coming soon |
| [Aws Parameter Store](./packages/aws-parameter-store-provider/) | Coming soon |

## Api Documentation

<a name="manager-class" />

### Stefna\SecretsManager\Manager

<a name="manager-constructor" />

#### Stefna\SecretsManager\Manager->__construct(ProviderInterface $provider)

Pass in your desired provider.

```php
<?php
use Stefna\SecretsManager\Manager;
use Stefna\SecretsManager\Provider\ArrayProvider;

$manager = new Manager(
	ArrayProvider::fromArray([
		'key' => 'value',
		'key2' => new \Stefna\SecretsManager\Values\Secret('key2', ['mixed' => 'value'], ['stage' => 'dev'])
	])
);
```

You can also chain providers

```php
<?php
use Stefna\SecretsManager\Manager;
use Stefna\SecretsManager\Provider\ArrayProvider;
use Stefna\SecretsManager\Provider\ChainProvider;
use Stefna\SecretsManager\Provider\JsonProvider\JsonProvider;

$defaultSecretsProvider = ArrayProvider::fromArray([
	'key' => 'value',
	'key2' => new \Stefna\SecretsManager\Values\Secret('key2', ['mixed' => 'value'], ['stage' => 'dev'])
]); 

$manager = new Manager(
    new ChainProvider(
        new JsonProvider('secrets.json'),
        $defaultSecretsProvider // if secrets are missing in JsonProvider will fallback to defaultProvider
    )
);
```

<a name="manager-getSecret" />

#### Stefna\SecretsManager\Manager->getSecret(string $key, ?array $options): Secret

Fetches a secret from the configured provider, `$key` is the name of the secret (or path) you are trying to get.

This will throw a `Stefna\SecretsManager\SecretNotFoundException` if secret is not found

```php
use Stefna\SecretsManager\Manager;
use Stefna\SecretsManager\Provider\ArrayProvider;
$manager = new Manager(ArrayProvider::fromArray([
	'databases/redis/dsn' => 'redis://localhost:6379',
])); 

$secret = $manager->getSecret('databases/redis/dsn');

$secret->getValue() === 'redis://localhost:6379';
```

<a name="manager-putSecret" />

#### Stefna\SecretsManager\Manager->putSecret(string $key, string|array $value, ?array $options): void

Puts a secret with the given `$value`, into the storage engine, under the given `$key`.

If the current adapter doesn't support arrays, and you pass one it, it will throw a `Stefna\SecretsManager\ValueNotSupportedException`.

Again, some adapters allow passing in custom options to send along with the request.

```php
$manager->putSecret('database/redis', 'postgres://localhost:5432');
```

And for adapters that support a key/value map as a value:

```php
$manager->putSecret('database/redis', ['dsn' => 'redis://localhost:6379', 'password' => 'my_super_strong_password']);
```

<a name="manager-deleteSecret" />

#### Stefna\SecretsManager\Manager->deleteSecret(Secret $secret, ?array $options): void

Deletes a secret from the provider using the given `$secret`.

```php
use Stefna\SecretsManager\Manager;
use Stefna\SecretsManager\Provider\ArrayProvider;
$manager = new Manager(ArrayProvider::fromArray([
	'databases/redis/dsn' => 'redis://localhost:6379',
])); 

$secret = $manager->getSecret('databases/redis/dsn');

$manager->deleteSecret($secret);
```

<a name="manager-getProvider" />

#### Stefna\SecretsManager\Manager->getProvider(): ProviderInterface

Will return provider currently in use

<a name="secret-class" />

### Stefna\SecretsManager\Secret

Secrets are immutable and will throw exception if you try to modify it.

The class implements ArrayAccess to allow ease of reading secrets stored in assoc array.

<a name="secret-getKey" />

#### Stefna\SecretsManager\Secret->getKey(): string

Returns the key for the secret

```php
use Stefna\SecretsManager\Manager;

$manager = new Manager($provider);
$secret = $manager->getSecret('database/redis');

$secret->getKey() === 'database/redis';
```

<a name="secret-getValue" />

#### Stefna\SecretsManager\Secret->getValue(): string|array<string, mixed>

Returns the value for the secret. If the secret is a key/value map it can be used as an array

```php
use Stefna\SecretsManager\Manager;
use Stefna\SecretsManager\Provider\ArrayProvider;
$manager = new Manager(ArrayProvider::fromArray([
	'databases/redis/dsn' => 'redis://localhost:6379',
])); 
$secret = $manager->getSecret('dabase/redis/dsn');

$secret->getValue() === 'redis://localhost:6379';
```

Array like access
```php
use Stefna\SecretsManager\Manager;
use Stefna\SecretsManager\Provider\ArrayProvider;
$manager = new Manager(ArrayProvider::fromArray([
	'database' => new \Stefna\SecretsManager\Values\Secret('database', [
		'user' => 'test',
		'name' => 'testDb',
	]),
]));

$secret = $manager->getSecret('database');
$secret['user'] === 'test';
```
