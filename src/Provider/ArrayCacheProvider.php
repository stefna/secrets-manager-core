<?php declare(strict_types=1);

namespace Stefna\SecretsManager\Provider;

use Stefna\SecretsManager\Values\Secret;

final class ArrayCacheProvider extends ArrayProvider
{
	public function __construct(
		private readonly ProviderInterface $provider,
	) {}

	public function getSecret(string $key, ?array $options = []): Secret
	{
		if (!array_key_exists($key, $this->data)) {
			$this->data[$key] = $this->provider->getSecret($key, $options);
		}
		return parent::getSecret($key, $options);
	}

	public function putSecret(Secret $secret, ?array $options = []): Secret
	{
		$this->provider->putSecret($secret, $options);
		return parent::putSecret($secret, $options);
	}

	public function deleteSecret(Secret $secret, ?array $options = []): void
	{
		$this->provider->deleteSecret($secret);
		parent::deleteSecret($secret);
	}
}
