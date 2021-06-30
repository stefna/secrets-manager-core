<?php declare(strict_types=1);

namespace Stefna\SecretsManager\Exceptions;

final class SecretNotFoundException extends \DomainException
{
	public static function withKey(string $key): self
	{
		return new self(sprintf('No secret was found with the key: "%s"', $key));
	}
}
