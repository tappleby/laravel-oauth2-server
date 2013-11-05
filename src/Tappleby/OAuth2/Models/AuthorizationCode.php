<?php
/*
 * User: tappleby
 * Date: 11/4/2013
 * Time: 10:26 PM
 */

namespace Tappleby\OAuth2\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorizationCode extends Model implements AuthorizationCodeInterface {
	public $incrementing = false;

	protected $table = 'oauth_authorization_codes';
	protected $guarded = array();

	/**
	 * @return string
	 */
	function getId()
	{
		return $this->id;
	}

	/**
	 * >=40 character client id.
	 *
	 * @return string
	 */
	function getClientId()
	{
		return $this->client_id;
	}

	/**
	 * @return int
	 */
	function getUserId()
	{
		return $this->user_id;
	}

	/**
	 * @return string
	 */
	function getRedirectUri()
	{
		return $this->redirect_uri;
	}

	/**
	 * Unix time stamp
	 * @return int
	 */
	function getExpires()
	{
		return $this->expires;
	}

	/**
	 * @return array|null
	 */
	function getScopes()
	{
		return null;
	}


	/**
	 * Model Accessors & Mutators
	 */

	public function getExpiresAttribute($value) {
		return strtotime($value);
	}

	public function setExpiresAttribute($value) {

		if(is_int($value)) $value = date('Y-m-d H:i:s', $value);

		$this->attributes['expires'] = $value;
	}

}