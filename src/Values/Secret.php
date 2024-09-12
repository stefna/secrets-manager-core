<?php declare(strict_types=1);

namespace Stefna\SecretsManager\Values;

use Stefna\SecretsManager\Exceptions\ValueNotSupportedException;

/**
 * @implements \ArrayAccess<string, mixed>
 */
final class Secret implements \ArrayAccess
{
	private string $key;
	/** @var string|array<string, mixed> */
	private string|array $value;
	/** @var array<string, mixed>|null */
	private ?array $metadata;

	/**
	 * @param string|array<string, mixed> $value
	 * @param array<string, mixed>|null $metadata
	 */
	public function __construct(string $key, array|string $value, ?array $metadata = null)
	{
		$this->key = $key;
		$this->value = $value;
		$this->metadata = $metadata;
	}

	public function getKey(): string
	{
		return $this->key;
	}

	/**
	 * @return string|array<string, mixed>
	 */
	public function getValue(): array|string
	{
		return $this->value;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getMetadata(): array
	{
		return $this->metadata ?? [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetExists(mixed $offset): bool
	{
		return is_array($this->value) && array_key_exists($offset, $this->value);
	}

	/**
	 * @throws ValueNotSupportedException
	 */
	public function offsetGet(mixed $offset): mixed
	{
		if (!is_array($this->value)) {
			throw new ValueNotSupportedException($this->key);
		}

		return $this->value[$offset];
	}

	/**
	 * @throws \BadMethodCallException
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		throw new \BadMethodCallException('Secrets are immutable');
	}

	/**
	 * @throws \BadMethodCallException
	 */
	public function offsetUnset($offset): void
	{
		throw new \BadMethodCallException('Secrets are immutable');
	}

	/**
	 * @param string|array<string, mixed> $value
	 */
	public function withValue(array|string $value): self
	{
		return new Secret($this->key, $value, $this->metadata);
	}

	/**
	 * @param array<string, mixed> $metadata
	 */
	public function withMetadata(array $metadata): self
	{
		return new Secret($this->key, $this->value, $metadata);
	}
}
