<?php

namespace Tappleby\OAuth2\Server;

use Illuminate\Container\Container;
use Illuminate\Routing\Controllers\Controller as IlluminateController;
use Illuminate\Routing\Router;

use OAuth2\HttpFoundationBridge\Request as BridgeRequest;
use OAuth2\HttpFoundationBridge\Response as BridgeResponse;

class Controller extends IlluminateController {

	/** @var \Symfony\Component\HttpFoundation\Request */
	protected $request;

	/** @var Server */
	protected $server;

	function __construct(Server $server)
	{
		$this->server = $server;
	}

	public function callAction(Container $container, Router $router, $method, $parameters)
	{
		$this->request = $router->getRequest();
		return parent::callAction($container, $router, $method, $parameters);
	}

	public function getIndex() {
		$bridgeRequest = BridgeRequest::createFromRequest($this->request);
		$bridgeResponse = new BridgeResponse;

		if(!$this->server->verifyResourceRequest($bridgeRequest, $bridgeResponse)) {
			return $bridgeResponse;
		}

		$bridgeResponse->setData(array('success' => true, 'message' => 'Valid Access.'));

		return $bridgeResponse;
	}

	public function getToken() {
		$bridgeRequest = BridgeRequest::createFromRequest($this->request);
		$bridgeResponse = new BridgeResponse;

		return $this->server->handleTokenRequest($bridgeRequest, $bridgeResponse);
	}

	public function postToken() {
		$bridgeRequest = BridgeRequest::createFromRequest($this->request);
		$bridgeResponse = new BridgeResponse;

		return $this->server->handleTokenRequest($bridgeRequest, $bridgeResponse);
	}
}