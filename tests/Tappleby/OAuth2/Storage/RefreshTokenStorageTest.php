<?php


namespace Tappleby\OAuth2\Storage;

use Mockery as m;
use Tappleby\OAuth2\Models\RefreshTokenBasic;

class RefreshTokenStorageTest extends \PHPUnit_Framework_TestCase {

	protected function tearDown() {
		m::close();
	}

  protected function getRefreshToken() {
	  return new RefreshTokenBasic(
			'foo_id', 'foo_client', 'foo_user', 1337, array('foo', 'bar')
	  );
  }

  public function testReturnsNullOnInvalidToken()
  {
    $refreshTokenRepo = m::mock('Tappleby\OAuth2\Repositories\RefreshTokenRepositoryInterface');
    $refreshTokenRepo->shouldReceive('find')->once()->with(1)->andReturnNull();

    $refreshTokenStorage = new RefreshTokenStorage($refreshTokenRepo);

    $this->assertNull( $refreshTokenStorage->getRefreshToken(1) );
  }

  public function testReturnsValidTokenData()
  {
    $refreshTokenRepo = m::mock('Tappleby\OAuth2\Repositories\RefreshTokenRepositoryInterface');
    $refreshTokenRepo->shouldReceive('find')->once()->with(1)->andReturn( $this->getRefreshToken() );

    $refreshTokenStorage = new RefreshTokenStorage($refreshTokenRepo);

    $tk = $refreshTokenStorage->getRefreshToken(1);
    $this->assertInternalType('array', $tk);

	  $this->assertEquals('foo_id', $tk['refresh_token']);
	  $this->assertEquals('foo_client', $tk['client_id']);
	  $this->assertEquals('foo_user', $tk['user_id']);
	  $this->assertEquals(1337, $tk['expires']);
    $this->assertEquals('foo bar', $tk['scope']);
  }

  public function testSetRefreshToken()
  {
    $refreshTokenRepo = m::mock('Tappleby\OAuth2\Repositories\RefreshTokenRepositoryInterface');
    $refreshTokenRepo->shouldReceive('create')->once()->with(array(
      'id' => 1,
      'client_id' => 2,
      'user_id' => 3,
      'expires' => 4,
      'scope' => array('foo', 'bar')
    ))->andReturn('fizbin');

    $refreshTokenStorage = new RefreshTokenStorage($refreshTokenRepo);

    $this->assertEquals($refreshTokenStorage->setRefreshToken(1,2,3,4, 'foo bar'), 'fizbin', 'Did not return result from RefreshTokenRepositoryInterface::create.');
  }

  public function testUnsetRefreshToken()
  {
    $refreshTokenRepo = m::mock('Tappleby\OAuth2\Repositories\RefreshTokenRepositoryInterface');
    $refreshTokenRepo->shouldReceive('delete')->once()->with('foo')->andReturn('fizbin');

    $refreshTokenStorage = new RefreshTokenStorage($refreshTokenRepo);
    $this->assertEquals($refreshTokenStorage->unsetRefreshToken('foo'), 'fizbin', 'Did not return result from RefreshTokenRepositoryInterface::delete.');
  }
}