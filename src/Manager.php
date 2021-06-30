<?php declare(strict_types=1);

namespace Stefna\SecretsManager;

use Stefna\SecretsManager\Exceptions\SecretNotFoundException;
use Stefna\SecretsManager\Provider\ProviderInterface;
use Stefna\SecretsManager\Values\Secret;

final class Manager implements ProviderInterface
{
	/** @var ProviderInterface */
	private $provider;

	public function __construct(ProviderInterface $provider)
	{
		$this->provider = $provider;
	}

	/**
	 * @throws SecretNotFoundException
	 */
	public function getSecret(string $key, ?array $options = []): Secret
	{
		return $this->provider->getSecret($key);
	}

	public function putSecret(Secret $secret, ?array $options = []): Secret
	{
		return  $this->provider->putSecret($secret);
	}

	public function deleteSecret(Secret $secret, ?array $options = []): void
	{
		$this->provider->deleteSecret($secret);
	}

	/**
	 * @param array<string, mixed>|null $options
	 * @throws SecretNotFoundException
	 */
	public function deleteSecretByKey(string $key, ?array $options = []): void
	{
		$this->deleteSecret($this->getSecret($key, $options), $options);
	}

	public function getProvider(): ProviderInterface
	{
		return $this->provider;
	}
}
