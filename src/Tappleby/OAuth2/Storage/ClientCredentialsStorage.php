<?php

namespace Tappleby\OAuth2\Storage;


use Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryInterface;

class ClientCredentialsStorage implements \OAuth2\Storage\ClientCredentialsInterface {

	/** @var ClientCredentialsRepositoryInterface */
	protected $repo;

	function __construct(ClientCredentialsRepositoryInterface $repo)
	{
		$this->repo = $repo;
	}


	/**
	 * Checks if client credentials are valid.
	 *
	 * @param $client_id
	 * @param null $client_secret
	 * @return bool
	 */
	public function checkClientCredentials($client_id, $client_secret = null)
	{
		$client = $this->repo->find($client_id);

		if(!$client) {
			return false;
		}

		return $client_secret == $client->getSecret();
	}


	/**
	 * Returns array containing `client_id` and `redirect_uri`. False if missing.
	 *
	 * @param $client_id
	 * @return array|bool
	 */
	public function getClientDetails($client_id)
	{
		$client = $this->repo->find($client_id);

		if(!$client) {
			return false;
		}

		return array(
			'client_id' => $client->getId(),
			'redirect_uri' => $client->getRedirectUri()
		);
	}

	/**
	 * Checks if grant tpye is restricted.
	 *
	 * @param $client_id
	 * @param $grant_type
	 * @return bool
	 */
	public function checkRestrictedGrantType($client_id, $grant_type)
	{
		$client = $this->repo->find($client_id);

		if(!$client) {
			return false;
		}

		$restrictedTypes = $client->getRestrictedGrantTypes();

		// Null restricted type assuemd to be true.
		if(is_null($restrictedTypes)) {
			return true;
		}


		return in_array($grant_type, $client->getRestrictedGrantTypes());
	}


}