<?php declare(strict_types=1);

namespace Stefna\SecretsManager\Values;

use Stefna\SecretsManager\Exceptions\ValueNotSupportedException;

/**
 * @implements \ArrayAccess<string, mixed>
 */
final class Secret implements \ArrayAccess
{
	/** @var string */
	private $key;
	/** @var string|array<string, mixed> */
	private $value;
	/** @var array<string, mixed>|null */
	private $metadata;

	/**
	 * @param string|array<string, mixed> $value
	 * @param array<string, mixed>|null $metadata
	 */
	public function __construct(string $key, $value, ?array $metadata = null)
	{
		$this->key = $key;
		$this->value = $value;
		$this->metadata = $metadata;
	}

	/**
	 * @return string
	 */
	public function getKey(): string
	{
		return $this->key;
	}

	/**
	 * @return string|array<string, mixed>
	 */
	public function getValue()
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
	public function offsetExists($offset)
	{
		return is_array($this->value) && array_key_exists($offset, $this->value);
	}

	/**
	 * @throws ValueNotSupportedException
	 */
	public function offsetGet($offset)
	{
		if (!is_array($this->value)) {
			throw new ValueNotSupportedException($this->key);
		}

		return $this->value[$offset];
	}

	/**
	 * @throws \BadMethodCallException
	 */
	public function offsetSet($offset, $value)
	{
		throw new \BadMethodCallException('Secrets are immutable');
	}

	/**
	 * @throws \BadMethodCallException
	 */
	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException('Secrets are immutable');
	}

	/**
	 * @param string|array<string, mixed> $value
	 */
	public function withValue($value): self
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
