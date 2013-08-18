<?php

namespace Tappleby\OAuth2\Storage;


use Tappleby\OAuth2\Repositories\AccessTokenRepositoryInterface;

class AccessTokenStorage implements \OAuth2_Storage_AccessTokenInterface {

	/** @var \Tappleby\OAuth2\Repositories\AccessTokenRepositoryInterface */
	protected $repo;

	function __construct(AccessTokenRepositoryInterface $repo)
	{
		$this->repo = $repo;
	}

	public function getAccessToken($oauth_token)
	{
    $retData = null;
		$token = $this->repo->find($oauth_token);

    if($token) {
      $scope = null;

      if(is_array($token->getScopes())) {
        $scope = implode(' ', $token->getScopes());
      }

      $retData = array(
        'client_id' => $token->getClientId(),
        'user_id' => $token->getUserId(),
        'expires' => $token->getExpires(),
        'scope' => $scope
      );
    }

    return $retData;
  }

	public function setAccessToken($oauth_token, $client_id, $user_id, $expires, $scope = null)
	{
		if($scope) {
			$scope = explode(' ', $scope);
		}

		return $this->repo->create(array(
			'id' => $oauth_token,
			'client_id' => $client_id,
			'user_id' => $user_id,
			'expires' => $expires,
			'scope' => $scope
		));
	}
}