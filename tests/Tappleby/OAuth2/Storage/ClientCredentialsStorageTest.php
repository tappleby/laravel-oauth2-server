<?php

namespace Tappleby\OAuth2\Storage;

use Mockery as m;

class ClientCredentialsStorageTest extends \PHPUnit_Framework_TestCase {


	public function testCredentialsInvalidClientId()
	{
		$clientRepo = m::mock('Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryInterface');
		$clientRepo->shouldReceive('find')->andReturn(null);

		$clientStorage = new ClientCredentialsStorage($clientRepo);

		$this->assertFalse($clientStorage->checkClientCredentials('foo', 'bar'));

	}

	public function testCredentialsInvalidClientSecret()
	{
		$client = m::mock('Tappleby\OAuth2\Models\ClientCredentialsInterface');
		$client->shouldReceive('getSecret')->once()->andReturn('wrong');

		$clientRepo = m::mock('Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryInterface');
		$clientRepo->shouldReceive('find')->andReturn($client);

		$clientStorage = new ClientCredentialsStorage($clientRepo);

		$this->assertFalse($clientStorage->checkClientCredentials('foo', 'bar'));
	}

	public function testCredentialsValid()
	{
		$client = m::mock('Tappleby\OAuth2\Models\ClientCredentialsInterface');
		$client->shouldReceive('getSecret')->once()->andReturn('bar');

		$clientRepo = m::mock('Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryInterface');
		$clientRepo->shouldReceive('find')->andReturn($client);

		$clientStorage = new ClientCredentialsStorage($clientRepo);

		$this->assertTrue($clientStorage->checkClientCredentials('foo', 'bar'));
	}

	public function testClientDetailsMissingClient()
	{
		$clientRepo = m::mock('Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryInterface');
		$clientRepo->shouldReceive('find')->andReturn(null);

		$clientStorage = new ClientCredentialsStorage($clientRepo);

		$this->assertFalse($clientStorage->getClientDetails('foo'));
	}

	public function testClientDetailsValid()
	{
		$client = m::mock('Tappleby\OAuth2\Models\ClientCredentialsInterface');
		$client->shouldReceive('getId')->once()->andReturn('foo');
		$client->shouldReceive('getRedirectUri')->once()->andReturn('bar');

		$clientRepo = m::mock('Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryInterface');
		$clientRepo->shouldReceive('find')->andReturn($client);

		$clientStorage = new ClientCredentialsStorage($clientRepo);

		$details = $clientStorage->getClientDetails('foo');

		$this->assertInternalType('array', $details);

		$this->assertArrayHasKey('client_id', $details);
		$this->assertEquals('foo', $details['client_id']);

		$this->assertArrayHasKey('redirect_uri', $details);
		$this->assertEquals('bar', $details['redirect_uri']);
	}

	public function testCheckRestrictedGrantTypeMissingClient()
	{
		$clientRepo = m::mock('Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryInterface');
		$clientRepo->shouldReceive('find')->andReturn(null);

		$clientStorage = new ClientCredentialsStorage($clientRepo);

		$this->assertFalse($clientStorage->checkRestrictedGrantType('foo', 'bar'));
	}

	public function testCheckRestrictedGrantTypeNullGrantTypes()
	{
		$client = m::mock('Tappleby\OAuth2\Models\ClientCredentialsInterface');
		$client->shouldReceive('getRestrictedGrantTypes')->once()->andReturnNull();

		$clientRepo = m::mock('Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryInterface');
		$clientRepo->shouldReceive('find')->andReturn($client);

		$clientStorage = new ClientCredentialsStorage($clientRepo);

		$this->assertTrue( $clientStorage->checkRestrictedGrantType('foo', 'bar') );
	}

	public function testCheckRestrictedGrantTypeInvalid()
	{
		$client = m::mock('Tappleby\OAuth2\Models\ClientCredentialsInterface');
		$client->shouldReceive('getRestrictedGrantTypes')->once()->andReturn(array('baz', 'fiz'));

		$clientRepo = m::mock('Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryInterface');
		$clientRepo->shouldReceive('find')->andReturn($client);

		$clientStorage = new ClientCredentialsStorage($clientRepo);

		$this->assertFalse( $clientStorage->checkRestrictedGrantType('foo', 'bar') );
	}

	public function testCheckRestrictedGrantTypeValid()
	{
		$client = m::mock('Tappleby\OAuth2\Models\ClientCredentialsInterface');
		$client->shouldReceive('getRestrictedGrantTypes')->once()->andReturn(array('bar', 'fiz'));

		$clientRepo = m::mock('Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryInterface');
		$clientRepo->shouldReceive('find')->andReturn($client);

		$clientStorage = new ClientCredentialsStorage($clientRepo);

		$this->assertTrue( $clientStorage->checkRestrictedGrantType('foo', 'bar') );
	}


}
