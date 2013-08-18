<?php

namespace Tappleby\OAuth2\Filter;

use Mockery as m;
use Symfony\Component\HttpFoundation\Request;


class AccessFilterTest extends \PHPUnit_Framework_TestCase
{

  /** @var \Mockery\MockInterface */
  protected $mockScopeUtil;

  /** @var \Mockery\MockInterface */
  protected $mockServer;

  /** @var \Mockery\MockInterface */
  protected $mockDispatcher;

  /** @var \Mockery\MockInterface */
  protected $mockClientRepo;

  /** @var \Mockery\MockInterface */
  protected $mockUserRepo;

  /** @var  AccessFilter */
  protected $accessFilter;

  protected function setUp()
  {
    $this->mockScopeUtil = m::mock('OAuth2_ScopeInterface');
    $this->mockServer = m::mock('Tappleby\OAuth2\Server\Server');
    $this->mockServer->shouldReceive('getScopeUtil')->andReturn( $this->mockScopeUtil );

    $this->mockDispatcher = m::mock('Illuminate\Events\Dispatcher');
    $this->mockClientRepo = m::mock('Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryInterface');
    $this->mockUserRepo = m::mock('Illuminate\Auth\UserProviderInterface');

    $this->accessFilter = new AccessFilter(
      $this->mockServer,
      $this->mockDispatcher,
      $this->mockClientRepo,
      $this->mockUserRepo
    );
  }

  protected function getRequest() {
    return new Request();
  }

  protected function tearDown()
  {
    m::close();
  }


  public function testBeforeEventEarlyExit()
  {
    $this->mockDispatcher->shouldReceive('until')->once()->with('oauth.access.before', m::any())->andReturn(true);
    $this->assertNull( $this->accessFilter->filter(null, $this->getRequest(), null) );
  }

  public function testFailsOnInvalidResourceRequest()
  {
    $this->mockDispatcher->shouldReceive('until')->once()->andReturnNull();
    $this->mockDispatcher->shouldReceive('fire')->once()->with('oauth.access.failed');

    $mockResourceController = m::mock('OAuth2_Controller_ResourceControllerInterface');
    $mockResourceController->shouldReceive('verifyResourceRequest')->andReturn(false);
    $this->mockServer->shouldReceive('getResourceController')->once()->andReturn($mockResourceController);

    $resp = $this->accessFilter->filter(null, $this->getRequest(), null);
    $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $resp);
  }

  public function testTriggersSuccess()
  {
    $this->mockDispatcher->shouldReceive('until')->once()->andReturnNull();

    $mockResourceController = m::mock('OAuth2_Controller_ResourceControllerInterface');
    $mockResourceController->shouldReceive('verifyResourceRequest')->andReturn(true);
    $mockResourceController->shouldReceive('getAccessTokenData')->once()->andReturn(array(
      'client_id' => 1, 'scope' => null
    ));

    $this->mockServer->shouldReceive('getResourceController')->once()->andReturn($mockResourceController);
    $this->mockClientRepo->shouldReceive('find')->once()->with(1)->andReturn('foo');

    $this->mockDispatcher->shouldReceive('fire')->once()->with(
      'oauth.access.valid',
      array('foo', null, null)
    );


    $this->assertNull($this->accessFilter->filter(null, $this->getRequest(), null));
  }

  public function testTriggersSuccessWithUser()
  {
    $this->mockDispatcher->shouldReceive('until')->once()->andReturnNull();

    $mockResourceController = m::mock('OAuth2_Controller_ResourceControllerInterface');
    $mockResourceController->shouldReceive('verifyResourceRequest')->andReturn(true);
    $mockResourceController->shouldReceive('getAccessTokenData')->once()->andReturn(array(
      'client_id' => 1, 'user_id' => 2, 'scope' => null
    ));

    $this->mockServer->shouldReceive('getResourceController')->once()->andReturn($mockResourceController);
    $this->mockClientRepo->shouldReceive('find')->once()->with(1)->andReturn('foo');
    $this->mockUserRepo->shouldReceive('retrieveById')->once()->with(2)->andReturn('bar');


    $this->mockDispatcher->shouldReceive('fire')->once()->with(
      'oauth.access.valid',
      array('foo', 'bar', null)
    );


    $this->assertNull($this->accessFilter->filter(null, $this->getRequest(), null));
  }

  public function testTriggersSuccessWithScopes()
  {
    $this->mockDispatcher->shouldReceive('until')->once()->andReturnNull();

    $mockResourceController = m::mock('OAuth2_Controller_ResourceControllerInterface');
    $mockResourceController->shouldReceive('verifyResourceRequest')->andReturn(true);
    $mockResourceController->shouldReceive('getAccessTokenData')->once()->andReturn(array(
      'client_id' => 1, 'scope' => 'fiz bin'
    ));

    $this->mockServer->shouldReceive('getResourceController')->once()->andReturn($mockResourceController);
    $this->mockClientRepo->shouldReceive('find')->once()->with(1)->andReturn('foo');

    $this->mockDispatcher->shouldReceive('fire')->once()->with(
      'oauth.access.valid',
      array('foo', null, array('fiz', 'bin'))
    );


    $this->assertNull($this->accessFilter->filter(null, $this->getRequest(), null));
  }
}