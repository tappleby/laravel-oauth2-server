<?php

namespace Tappleby\OAuth2\Server;


use OAuth2_ClientAssertionTypeInterface;
use OAuth2_ScopeInterface;
use OAuth2_TokenTypeInterface;
use Tappleby\OAuth2\ResponseType\AccessTokenResponseType;

class Server extends \OAuth2_Server {
	public function __construct($storage = array(), array $config = array(), array $grantTypes = array(), array $responseTypes = array(), OAuth2_TokenTypeInterface $tokenType = null, OAuth2_ScopeInterface $scopeUtil = null, OAuth2_ClientAssertionTypeInterface $clientAssertionType = null)
	{
		parent::__construct($storage, $config, $grantTypes, $responseTypes, $tokenType, $scopeUtil, $clientAssertionType);
	}

	protected function getAccessTokenResponseType()
	{
		if (isset($this->responseTypes['access_token'])) {
			return $this->responseTypes['access_token'];
		}
		if (!isset($this->storages['access_token'])) {
			throw new \LogicException("You must supply a response type implementing OAuth2_ResponseType_AccessTokenInterface, or a storage object implementing OAuth2_Storage_AccessTokenInterface to use the token server");
		}
		$refreshStorage = null;
		if (isset($this->storages['refresh_token'])) {
			$refreshStorage = $this->storages['refresh_token'];
		}
		$config = array_intersect_key($this->config, array_flip(explode(' ', 'access_lifetime refresh_token_lifetime access_token_return_user_id')));
		$config['token_type'] = $this->tokenType ? $this->tokenType->getTokenType() :  $this->getDefaultTokenType()->getTokenType();

		return new AccessTokenResponseType($this->storages['access_token'], $refreshStorage, $config);
	}
}