<?php


namespace Tappleby\OAuth2\Storage;

use Mockery as m;

class RefreshTokenStorageTest extends \PHPUnit_Framework_TestCase {
  protected function getRefreshToken() {
    $tk = m::mock('Tappleby\OAuth2\Models\RefreshTokenInterface');
    $tk->shouldReceive('getScopes')->andReturn(array('foo', 'bar'));
    $tk->shouldIgnoreMissing();

    return $tk;
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

    $this->assertArrayHasKey('client_id', $tk);
    $this->assertArrayHasKey('user_id', $tk);
    $this->assertArrayHasKey('expires', $tk);
    $this->assertArrayHasKey('scope', $tk);
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