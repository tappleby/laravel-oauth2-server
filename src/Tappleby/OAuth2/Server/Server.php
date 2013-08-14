<?php

namespace Tappleby\OAuth2\Server;


use OAuth2_ClientAssertionTypeInterface;
use OAuth2_ScopeInterface;
use OAuth2_TokenTypeInterface;

class Server extends \OAuth2_Server {
	public function __construct($storage = array(), array $config = array(), array $grantTypes = array(), array $responseTypes = array(), OAuth2_TokenTypeInterface $tokenType = null, OAuth2_ScopeInterface $scopeUtil = null, OAuth2_ClientAssertionTypeInterface $clientAssertionType = null)
	{
		parent::__construct($storage, $config, $grantTypes, $responseTypes, $tokenType, $scopeUtil, $clientAssertionType);
	}
}