<?php

namespace Tappleby\OAuth2\Models;


use Illuminate\Database\Eloquent\Model;

class ClientCredentials extends Model implements ClientCredentialsInterface {

  public $incrementing = false;

	protected $table = 'oauth_clients';
  protected $guarded = array();

	/**
	 * >=40 character client id.
	 *
	 * @return string
	 */
	function getId()
	{
		return $this->id;
	}

	/**
	 * >=40 character client secret.
	 *
	 * @return string
	 */
	function getSecret()
	{
		return $this->secret;
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