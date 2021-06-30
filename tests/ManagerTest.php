<?php declare(strict_types=1);

namespace Stefna\SecretsManager\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stefna\SecretsManager\Exceptions\SecretNotFoundException;
use Stefna\SecretsManager\Manager;
use Stefna\SecretsManager\Provider\ProviderInterface;
use Stefna\SecretsManager\Values\Secret;

final class ManagerTest extends TestCase
{
	/** @var ProviderInterface&MockObject */
	private $provider;

	protected function setUp(): void
	{
		$this->provider = $this->createMock(ProviderInterface::class);
	}

	public function testGetSecret(): void
	{
		$key = 'fop';
		$value = 'bar';
		$secret  = new Secret($key, $value);
		$manager = new Manager($this->provider);

		$this->provider
			->expects($this->once())
			->method('getSecret')
			->with('foo', [])
			->willReturn($secret);

		$result = $manager->getSecret('foo');
		$this->assertInstanceOf(Secret::class, $result);
		$this->assertSame($secret, $result);
		$this->assertSame($key, $secret->getKey());
		$this->assertSame($value, $secret->getValue());
		$this->assertEmpty($secret->getMetadata());
	}

	public function testGetBadSecret(): void
	{
		$this->expectException(SecretNotFoundException::class);
		$this->expectExceptionMessage('No secret was found with the key: "foo"');

		$manager = new Manager($this->provider);

		$this->provider
			->expects($this->once())
			->method('getSecret')
			->with('foo', [])
			->willThrowException(SecretNotFoundException::withKey('foo'));

		$manager->getSecret('foo');
	}

	public function testPutSecret(): void
	{
		$manager = new Manager($this->provider);
		$secret = new Secret('foo', 'bar');

		$this->provider
			->expects($this->once())
			->method('putSecret')->with($secret, [])
			->willReturn($secret);

		$response = $manager->putSecret($secret);
		$this->assertSame($secret, $response);
	}

	public function testDeleteSecretByKey(): void
	{
		$manager = new Manager($this->provider);
		$secret = new Secret('foo', '');

		$this->provider
			->expects($this->once())
			->method('getSecret')
			->with('foo', [])
			->willReturn($secret);

		$this->provider
			->expects($this->once())
			->method('deleteSecret')
			->with($secret, []);

		$manager->deleteSecretByKey('foo');
	}

	public function testDeleteSecret(): void
	{
		$manager = new Manager($this->provider);
		$secret = new Secret('foo', 'bar');

		$this->provider
			->expects($this->once())
			->method('deleteSecret')
			->with($secret, []);

		$manager->deleteSecret($secret);
	}

	public function testGetProvider(): void
	{
		$manager = new Manager($this->provider);

		$this->assertSame($this->provider, $manager->getProvider());
		$this->assertInstanceOf(ProviderInterface::class, $manager->getProvider());
	}
}
