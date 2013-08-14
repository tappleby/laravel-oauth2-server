<?php

namespace Tappleby\OAuth2\Storage;

use Mockery as m;

class UserStorageTest extends \PHPUnit_Framework_TestCase {

	public function testInvalidCredentials()
	{
		$guard = m::mock('\Illuminate\Auth\Guard');
		$guard->shouldReceive('attempt')->andReturn(false);

		$userStorage = new UserStorage($guard);

		$this->assertFalse($userStorage->checkUserCredentials('foo', 'bar'));
	}

	public function testValidCredentails()
	{
		$guard = m::mock('\Illuminate\Auth\Guard');
		$guard->shouldReceive('attempt')->andReturn(true);

		$userStorage = new UserStorage($guard);

		$this->assertTrue($userStorage->checkUserCredentials('foo', 'bar'));
	}

	public function testCredentailsCustomUsernameKey()
	{
		$guard = m::mock('\Illuminate\Auth\Guard');

		$userCreds = array(
			'username' => 'foo',
			'password' => 'bar'
		);

		$guard->shouldReceive('attempt')->with($userCreds, m::any(), m::any())->andReturn(false);

		$userStorage = new UserStorage($guard, 'username');
		$userStorage->checkUserCredentials($userCreds['username'], $userCreds['password']);
	}


}
