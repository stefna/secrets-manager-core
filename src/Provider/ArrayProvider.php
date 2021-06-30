<?php declare(strict_types=1);

namespace Stefna\SecretsManager\Provider;

use Stefna\SecretsManager\Exceptions\SecretNotFoundException;
use Stefna\SecretsManager\Values\Secret;

class ArrayProvider implements ProviderInterface
{
	/** @var array<string, Secret> */
	protected $data = [];

	/**
	 * @param array<string, Secret|string> $data
	 */
	public static function fromArray(array $data): self
	{
		$self = new self;
		foreach ($data as $key => $secret) {
			if (!$secret instanceof Secret) {
				$secret = new Secret($key, $secret);
			}
			$self->putSecret($secret);
		}
		return $self;
	}

	public function getSecret(string $key, ?array $options = []): Secret
	{
		if (array_key_exists($key, $this->data)) {
			return $this->data[$key];
		}

		throw SecretNotFoundException::withKey($key);
	}

	public function putSecret(Secret $secret, ?array $options = []): Secret
	{
		$this->data[$secret->getKey()] = $secret;
		return $secret;
	}

	public function deleteSecret(Secret $secret, ?array $options = []): void
	{
		unset($this->data[$secret->getKey()]);
	}
}
