<?php
/*
 * User: tappleby
 * Date: 11/16/2013
 * Time: 11:27 PM
 */

namespace Tappleby\OAuth2\ResponseType;


use OAuth2_Storage_AccessTokenInterface;
use OAuth2_Storage_RefreshTokenInterface;

class AccessTokenResponseType extends \OAuth2_ResponseType_AccessToken {

	public function __construct(OAuth2_Storage_AccessTokenInterface $tokenStorage, OAuth2_Storage_RefreshTokenInterface $refreshStorage = null, array $config = array())
	{
		parent::__construct($tokenStorage, $refreshStorage, $config);
	}

	public function createAccessToken($client_id, $user_id, $scope = null, $includeRefreshToken = true)
	{
		$accessToken =  parent::createAccessToken(
			$client_id,
			$user_id,
			$scope,
			$includeRefreshToken
		);

		if(isset($this->config['access_token_return_user_id']) && $this->config['access_token_return_user_id']) {
			$accessToken['user_id'] = $user_id;
		}

		return $accessToken;
	}


}