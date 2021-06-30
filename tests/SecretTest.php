<?php declare(strict_types=1);

namespace Stefna\SecretsManager\Tests;

use PHPUnit\Framework\TestCase;
use Stefna\SecretsManager\Exceptions\ValueNotSupportedException;
use Stefna\SecretsManager\Values\Secret;

final class SecretTest extends TestCase
{
	public function testIssetReturnFalseWithScalarValue(): void
	{
		$secret = new Secret('key', 'value');

		$this->assertFalse(isset($secret['value']));
	}

	public function testIssetReturnTrueWhenValueIsArrayAndKeyExists(): void
	{
		$secret = new Secret('key', [
			'assocKey' => 'value',
		]);

		$this->assertTrue(isset($secret['assocKey']));
	}

	public function testGetValueFromArray(): void
	{
		$value = 'value';
		$secret = new Secret('key', [
			'assocKey' => $value,
		]);

		$this->assertSame($value, $secret['assocKey']);
	}

	public function testGetValueFromArrayWhenValueIsScalar(): void
	{
		$secret = new Secret('key', 'value');
		$this->expectException(ValueNotSupportedException::class);

		$value = $secret['testKey'];
	}

	public function testCantSetValueOnArrayAccess(): void
	{
		$secret = new Secret('key', [
			'test' => 'value',
		]);
		$this->expectException(\BadMethodCallException::class);

		$secret['test'] = 'value';
	}

	public function testCantDeleteArrayValue(): void
	{
		$value = 'value';
		$secret = new Secret('key', [
			'test' => $value,
		]);
		try {
			$secret->offsetUnset('test');
			$this->fail('Shouldn\'t be able to delete value');
		}
		catch (\BadMethodCallException $e) {
			$this->assertTrue(true);
		}

		$this->assertSame($value, $secret['test']);
	}

	public function testUpdateValueNotModifyingOriginal(): void
	{
		$value = 'value';
		$newValue = 'newValue';
		$secret = new Secret('key', $value);

		$newSecret = $secret->withValue($newValue);

		$this->assertNotSame($secret, $newSecret);
		$this->assertSame($value, $secret->getValue());
		$this->assertSame($newValue, $newSecret->getValue());
		$this->assertSame($newSecret->getKey(), $secret->getKey());
		$this->assertNotSame($newSecret->getValue(), $secret->getValue());
	}

	public function testUpdateMetaData(): void
	{
		$metaData = ['stage' => 'dev'];
		$newMetaData = ['stage' => 'prod'];
		$secret = new Secret('key', 'value', $metaData);

		$newSecret = $secret->withMetadata($newMetaData);

		$this->assertNotSame($secret, $newSecret);
		$this->assertSame($metaData, $secret->getMetadata());
		$this->assertSame($newMetaData, $newSecret->getMetadata());
		$this->assertSame($newSecret->getKey(), $secret->getKey());
	}
}
