<?php

namespace Tappleby\OAuth2\Storage;

use Mockery as m;

class UserStorageTest extends \PHPUnit_Framework_TestCase {

	protected function tearDown() {
		m::close();
	}

	public function formatCreds() {
		return function ($username, $password) {
			return array(
				'username' => $username,
				'password' => $password
			);
		};
	}

	public function testInvalidCredentials()
	{
		$guard = m::mock('\Illuminate\Auth\Guard');
		$guard->shouldReceive('attempt')->andReturn(false);

		$userStorage = new UserStorage($guard, $this->formatCreds());

		$this->assertFalse($userStorage->checkUserCredentials('foo', 'bar'));
	}

	public function testValidCredentails()
	{
		$guard = m::mock('\Illuminate\Auth\Guard');
		$guard->shouldReceive('attempt')->andReturn(true);

		$userStorage = new UserStorage($guard, $this->formatCreds());

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

		$seenUser = null;
		$seenPass = null;

		$userStorage = new UserStorage($guard, function ($u, $p) use($userCreds, &$seenUser, &$seenPass) {
			$seenUser = $u;
			$seenPass = $p;
			return $userCreds;
		});
		$userStorage->checkUserCredentials($userCreds['username'], $userCreds['password']);

		$this->assertEquals('foo', $seenUser);
		$this->assertEquals('bar', $seenPass);
	}


}
