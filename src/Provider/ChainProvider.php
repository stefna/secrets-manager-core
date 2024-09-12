<?php declare(strict_types=1);

namespace Stefna\SecretsManager\Provider;

use Stefna\SecretsManager\Exceptions\SecretNotFoundException;
use Stefna\SecretsManager\Values\Secret;

final class ChainProvider implements ProviderInterface
{
	/** @var ProviderInterface[] */
	private array $providers;

	public function __construct(ProviderInterface ...$providers)
	{
		$this->providers = $providers;
	}

	public function getSecret(string $key, ?array $options = []): Secret
	{
		foreach ($this->providers as $provider) {
			try {
				return $provider->getSecret($key, $options);
			}
			catch (SecretNotFoundException) {}
		}
		throw SecretNotFoundException::withKey($key);
	}

	public function putSecret(Secret $secret, ?array $options = []): Secret
	{
		foreach ($this->providers as $provider) {
			$provider->putSecret($secret);
		}
		return $secret;
	}

	public function deleteSecret(Secret $secret, ?array $options = []): void
	{
		foreach ($this->providers as $provider) {
			$provider->deleteSecret($secret);
		}
	}
}
