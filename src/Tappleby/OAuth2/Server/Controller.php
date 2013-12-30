<?php

namespace Tappleby\OAuth2\Server;

use Illuminate\Container\Container;
use Illuminate\Routing\Router;

use Controller as BaseController;
use OAuth2\HttpFoundationBridge\Request as BridgeRequest;
use OAuth2\HttpFoundationBridge\Response as BridgeResponse;

class Controller extends BaseController {

	/** @var \Symfony\Component\HttpFoundation\Request */
	protected $request;

	/** @var Server */
	protected $server;

	function __construct(Server $server)
	{
		$this->server = $server;

		$me = $this;
		$this->beforeFilter(function ($route, $request) use ($me) {
			$me->request = $request;
		});
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

    if($this->server->validateAuthorizeRequest($bridgeRequest, $bridgeResponse)) {

	    $authController = $this->server->getAuthorizeController();

	    //TODO: Break out into class?
	    $authRequestData = array(
		    'scope' => $authController->getScope(),
		    'state' => $authController->getState(),
		    'client_id' => $authController->getClientId(),
		    'redirect_uri' => $authController->getRedirectUri(),
		    'response_type' => $authController->getResponseType()
	    );

	    $bridgeResponse = $this->onGetAuthorized($authRequestData);
    }

    return $bridgeResponse;
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

    return $bridgeResponse;
  }
}