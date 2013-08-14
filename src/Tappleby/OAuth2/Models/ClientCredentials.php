<?php

namespace Tappleby\OAuth2\Models;


use Illuminate\Database\Eloquent\Model;

class ClientCredentials extends Model implements ClientCredentialsInterface {

	protected $table = 'oauth_clients';

	protected $guarded = array('id');

	/**
	 * >=40 character client id.
	 *
	 * @return string
	 */
	function getId()
	{
		return $this->client_id;
	}

	/**
	 * >=40 character client secret.
	 *
	 * @return string
	 */
	function getSecret()
	{
		return $this->client_secret;
	}

	/**
	 * @return mixed
	 */
	function getRedirectUri()
	{
		return $this->redirect_uri;
	}

	/**
	 * @return array|null
	 */
	function getRestrictedGrantTypes()
	{
		return null;
	}


}