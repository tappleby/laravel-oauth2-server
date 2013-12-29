<?php

namespace Tappleby\OAuth2\Storage;


class UserStorage implements \OAuth2\Storage\UserCredentialsInterface {

	/** @var \Illuminate\Auth\Guard  */
	protected $guard;

	protected $credentialsFormatter;

	function __construct($guard, $credentialsFormatter)
	{
		$this->guard = $guard;
		$this->credentialsFormatter = $credentialsFormatter;
	}

	public function checkUserCredentials($username, $password)
	{
		$creds = call_user_func($this->credentialsFormatter, $username, $password);

		return $this->guard->attempt($creds, false, false);
	}


	public function getUserDetails($username)
	{
		$creds = call_user_func($this->credentialsFormatter, $username, null);

		/** @var \Illuminate\Auth\UserInterface $user */
		$user = $this->guard->getProvider()->retrieveByCredentials($creds) ?: false;

		if($user) {
			$user = array(
				'scope' => null,
				'user_id' => $user->getAuthIdentifier()
			);
		}

		return $user;
	}

}