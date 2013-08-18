<?php

namespace Tappleby\OAuth2\Filter;


use Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryInterface;
use Tappleby\OAuth2\Server\Server;
use Illuminate\Auth\UserProviderInterface;
use Illuminate\Events\Dispatcher;

use OAuth2\HttpFoundationBridge\Request as BridgeRequest;
use OAuth2\HttpFoundationBridge\Response as BridgeResponse;

class AccessFilter {

	/** @var \Tappleby\OAuth2\Server\Server */
	protected $server;

	/** @var \Illuminate\Events\Dispatcher */
	protected $dispatcher;

	/** @var \Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryInterface */
	protected $clientRepo;

	/** @var \Illuminate\Auth\UserProviderInterface */
	protected $userProvider;

	function __construct(Server $server, Dispatcher $dispatcher, ClientCredentialsRepositoryInterface $clientRepo, UserProviderInterface $userProvider)
	{
		$this->dispatcher = $dispatcher;
		$this->server = $server;
		$this->clientRepo = $clientRepo;
		$this->userProvider = $userProvider;
	}

  /**
   * @param $route
   * @param $request
   * @param null $scope
   * @return null|BridgeResponse
   */
  public function filter($route, $request, $scope=null) {
		$beforeAccessResult = $this->dispatcher->until('oauth.access.before', array($scope));

		if($beforeAccessResult) return null;

		/** @var BridgeRequest $bridgeRequest */
		$bridgeRequest = BridgeRequest::createFromRequest($request);
		$bridgeResponse = new BridgeResponse;

    $resController = $this->server->getResourceController();

    if(!$resController->verifyResourceRequest($bridgeRequest, $bridgeResponse, $scope) ) {
      $this->dispatcher->fire('oauth.access.failed');
			return $bridgeResponse;
    }

    $token = $resController->getAccessTokenData($bridgeRequest, $bridgeResponse);

		$client = $this->clientRepo->find( $token['client_id'] );
		$tokenScope = $token['scope'];
		$user = null;

		if(isset($token['user_id'])) {
			$user = $this->userProvider->retrieveById($token['user_id']);
		}

    if($tokenScope) {
      $tokenScope = explode(' ', $tokenScope);
    }

		$eventPayload = array($client, $user, $tokenScope);
		$this->dispatcher->fire('oauth.access.valid', $eventPayload);
	}
}