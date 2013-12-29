<?php

namespace Tappleby\OAuth2\Storage;


use Tappleby\OAuth2\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenStorage implements \OAuth2\Storage\RefreshTokenInterface {
	/** @var \Tappleby\OAuth2\Repositories\RefreshTokenRepositoryInterface */
	protected $repo;

	function __construct(RefreshTokenRepositoryInterface $repo)
	{
		$this->repo = $repo;
	}

	public function getRefreshToken($refresh_token)
	{
		$token = $this->repo->find($refresh_token);

		if(!$token) return null;

		$scope = null;

		if(is_array($token->getScopes())) {
			$scope = implode(' ', $token->getScopes());
		}

		return array(
			'client_id' => $token->getClientId(),
			'user_id' => $token->getUserId(),
			'expires' => $token->getExpires(),
			'scope' => $scope
		);
	}

	public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null)
	{
		if($scope) {
			$scope = explode(' ', $scope);
		}

		return $this->repo->create(array(
			'id' => $refresh_token,
			'client_id' => $client_id,
			'user_id' => $user_id,
			'expires' => $expires,
			'scope' => $scope
		));
	}

	public function unsetRefreshToken($refresh_token)
	{
		return $this->repo->delete($refresh_token);
	}


}