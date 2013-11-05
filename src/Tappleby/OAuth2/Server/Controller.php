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

  public function getAuthorize() {
    $bridgeRequest = BridgeRequest::createFromRequest($this->request);
    $bridgeResponse = new BridgeResponse;

    $authRequestData = $this->server->validateAuthorizeRequest($bridgeRequest, $bridgeResponse);

    if(!$authRequestData) {
      return $bridgeResponse;
    }

    return $this->onGetAuthorized($authRequestData);
  }

  public function onGetAuthorized($authRequestData) {
    throw new \RuntimeException('Must Implement onGetAuthorized method');
  }

  public function onPostAuthorized() {
    throw new \RuntimeException('Must Implement onPostAuthorized method');
  }

  public function postAuthorize() {
    $bridgeRequest = BridgeRequest::createFromRequest($this->request);
    $bridgeResponse = new BridgeResponse;

    $userId = $this->onPostAuthorized();
    $isAuthorized = (bool)$userId;

    $this->server->handleAuthorizeRequest($bridgeRequest, $bridgeResponse, $isAuthorized, $userId);

    $bridgeResponse->headers->set('Location', null);
    return $bridgeResponse;
  }
}