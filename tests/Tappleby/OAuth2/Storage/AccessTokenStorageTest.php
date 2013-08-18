<?php


namespace Tappleby\OAuth2\Storage;

use Mockery as m;

class AccessTokenStorageTest extends \PHPUnit_Framework_TestCase {

  protected function getAccessToken() {
    $tk = m::mock('Tappleby\OAuth2\Models\AccessTokenInterface');
    $tk->shouldReceive('getScopes')->andReturn(array('foo', 'bar'));
    $tk->shouldIgnoreMissing();

    return $tk;
  }

  public function testReturnsNullOnInvalidToken()
  {
    $accessTokenRepo = m::mock('Tappleby\OAuth2\Repositories\AccessTokenRepositoryInterface');
    $accessTokenRepo->shouldReceive('find')->once()->with(1)->andReturnNull();

    $accessTokenStorage = new AccessTokenStorage($accessTokenRepo);

    $this->assertNull( $accessTokenStorage->getAccessToken(1) );
  }

  public function testReturnsValidTokenData()
  {
    $accessTokenRepo = m::mock('Tappleby\OAuth2\Repositories\AccessTokenRepositoryInterface');
    $accessTokenRepo->shouldReceive('find')->once()->with(1)->andReturn( $this->getAccessToken() );

    $accessTokenStorage = new AccessTokenStorage($accessTokenRepo);

    $tk = $accessTokenStorage->getAccessToken(1);
    $this->assertInternalType('array', $tk);

		$this->assertArrayHasKey('client_id', $tk);
		$this->assertArrayHasKey('user_id', $tk);
		$this->assertArrayHasKey('expires', $tk);
		$this->assertArrayHasKey('scope', $tk);
    $this->assertEquals('foo bar', $tk['scope']);
  }

  public function testSetAccessToken()
  {
    $accessTokenRepo = m::mock('Tappleby\OAuth2\Repositories\AccessTokenRepositoryInterface');
    $accessTokenRepo->shouldReceive('create')->once()->with(array(
      'id' => 1,
      'client_id' => 2,
      'user_id' => 3,
      'expires' => 4,
      'scope' => array('foo', 'bar')
    ))->andReturn('fizbin');

    $accessTokenStorage = new AccessTokenStorage($accessTokenRepo);

    $this->assertEquals($accessTokenStorage->setAccessToken(1,2,3,4, 'foo bar'), 'fizbin', 'Did not return result from AccessTokenRepositoryInterface::create');
  }


}