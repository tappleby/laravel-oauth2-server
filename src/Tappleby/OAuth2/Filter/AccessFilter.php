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

	protected function verifyResourceRequest(&$outToken,BridgeRequest $request, BridgeResponse $response, $scope = null)
	{
		$token = $this->server->getAccessTokenData($request, $response, $scope);

		// Check if we have token data
		if (is_null($token)) {
			return false;
		}

		$outToken = $token;

		/**
		 * Check scope, if provided
		 * If token doesn't have a scope, it's null/empty, or it's insufficient, then throw 403
		 * @see http://tools.ietf.org/html/rfc6750#section-3.1
		 */
		if ($scope && (!isset($token["scope"]) || !$token["scope"] || !$this->server->getScopeUtil()->checkScope($scope, $token["scope"]))) {
			$response->setError(403, 'insufficient_scope', 'The request requires higher privileges than provided by the access token');
			$response->addHttpHeaders(array(
					'WWW-Authenticate' => sprintf('%s realm="%s", scope="%s", error="%s", error_description="%s"',
						$this->tokenType->getTokenType(),
						$this->config['www_realm'],
						$scope,
						$response->getParameter('error'),
						$response->getParameter('error_description')
					)
				));
			return false;
		}

		return (bool)$token;
	}

	public function filter($route, $request, $scope=null) {
		if($scope) {
			$scope = explode(' ', $scope);
		}

		$beforeAccessResult = $this->dispatcher->until('oauth.access.before', array($scope));

		if($beforeAccessResult) return null;

		/** @var BridgeRequest $bridgeRequest */
		$bridgeRequest = BridgeRequest::createFromRequest($request);
		$bridgeResponse = new BridgeResponse;



		if(! $this->verifyResourceRequest($token, $bridgeRequest, $bridgeResponse, $scope) ) {
			$this->dispatcher->fire('oauth.access.failed');
			return $bridgeResponse;
		}


		$client = $this->clientRepo->find( $token['client_id'] );
		$tokenScope = $token['scope'];
		$user = null;

		if(isset($token['user_id'])) {
			$user = $this->userProvider->retrieveById($token['user_id']);
		}

		$eventPayload = array($client, $user, $tokenScope);
		$this->dispatcher->fire('oauth.access.valid', $eventPayload);
	}
}