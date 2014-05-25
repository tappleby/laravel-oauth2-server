<?php

namespace Tappleby\OAuth2\Storage;


use Tappleby\OAuth2\Models\ScopeInterface;
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

		$clientDetails = array(
			'client_id' => $client->getId(),
			'redirect_uri' => $client->getRedirectUri(),
		);


		if ($client instanceof ScopeInterface) {
			if (is_array($scopes = $client->getScopes())) {
				$clientDetails['scope'] = implode(' ', $scopes);
			}
		}

		return $clientDetails;
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

	/**
	 * Determine if the client is a "public" client, and therefore
	 * does not require passing credentials for certain grant types
	 *
	 * @param $client_id
	 * Client identifier to be check with.
	 *
	 * @return
	 * true if the client is public, and false if it isn't.
	 * @endcode
	 *
	 * @see http://tools.ietf.org/html/rfc6749#section-2.3
	 * @see https://github.com/bshaffer/oauth2-server-php/issues/257
	 *
	 * @ingroup oauth2_section_2
	 */
	public function isPublicClient($client_id)
	{
		if (($client = $this->repo->find($client_id))) {
			$secret = $client->getSecret();
			$isPublic = empty($secret);
		} else {
			$isPublic = false;
		}

		return $isPublic;
	}

	/**
	 * Get the scope associated with this client
	 *
	 * @return
	 * STRING the space-delineated scope list for the specified client_id
	 */
	public function getClientScope($client_id)
	{
		// TODO: Implement getClientScope() method.
	}


}