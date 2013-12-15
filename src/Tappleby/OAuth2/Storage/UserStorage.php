<?php

namespace Tappleby\OAuth2\Storage;


class UserStorage implements \OAuth2\Storage\UserCredentialsInterface {

	/** @var \Illuminate\Auth\Guard  */
	protected $guard;

	protected $usernameField;

	function __construct($guard, $usernameField = 'email')
	{
		$this->guard = $guard;
		$this->usernameField = $usernameField;
	}

	public function checkUserCredentials($username, $password)
	{
		$creds = array(
			$this->usernameField => $username,
			'password' => $password
		);

		return $this->guard->attempt($creds, false, false);
	}


	public function getUserDetails($username)
	{
		$creds = array(
			$this->usernameField => $username
		);

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