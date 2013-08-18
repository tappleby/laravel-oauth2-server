<?php

namespace Tappleby\OAuth2\Models;


use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model implements AccessTokenInterface {

  public $incrementing = false;

  protected $table = 'oauth_access_tokens';
	protected $guarded = array();


	/**
	 * @return string
	 */
	function getId()
	{
		return $this->id;
	}

	/**
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