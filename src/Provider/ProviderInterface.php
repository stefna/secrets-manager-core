<?php declare(strict_types=1);

namespace Stefna\SecretsManager\Provider;

use Stefna\SecretsManager\Exceptions\SecretNotFoundException;
use Stefna\SecretsManager\Values\Secret;

interface ProviderInterface
{
	/**
	 * @param array<string, mixed> $options
	 * @throws SecretNotFoundException
	 */
	public function getSecret(string $key, ?array $options = []): Secret;

	/**
	 * @param array<string, mixed> $options
	 */
	public function putSecret(Secret $secret, ?array $options = []): Secret;

	/**
	 * @param array<string, mixed> $options
	 */
	public function deleteSecret(Secret $secret, ?array $options = []): void;
}
