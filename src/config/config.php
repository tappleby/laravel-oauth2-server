<?php

return array(

	/**
	 * Bindings for the various repositories.
	 */

	'repositories' => array(

		/**
		 * Bind string which will be automatically resolved.
		 */

		'client_credentials' => '\Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryEloquent',
		'access_token' => '\Tappleby\OAuth2\Repositories\AccessTokenRepositoryEloquent',
		'refresh_token' => '\Tappleby\OAuth2\Repositories\RefreshTokenRepositoryEloquent',
		'authorization_code' => '\Tappleby\OAuth2\Repositories\AuthorizationCodeRepositoryEloquent',

		/**
		 * Below are examples of Redis repositories (Also applicable for any Repo which has
		 * dependencies which need to be resolved).
		 */

		//'access_token' => function($app) {
		//	$redis = $app['redis'];
		//	return new \Tappleby\OAuth2\Repositories\AccessTokenRepositoryRedis( $redis );
		//},
		//
		//'refresh_token' => function($app) {
		//	$redis = $app['redis'];
		//	return new \Tappleby\OAuth2\Repositories\RefreshTokenRepositoryRedis( $redis );
		//},
		//
		//'authorization_code' => function($app) {
		//  $redis = $app['redis'];
		//  return new \Tappleby\OAuth2\Repositories\AuthorizationCodeRepositoryRedis( $redis );
		//}


	),
	/**
	 * Raw configuration for oauth server.
	 */

	'server' => array(
		'access_lifetime' => 3600,
		'www_realm' => 'Service',
		'token_param_name' => 'access_token',
		'token_bearer_header_name' => 'Bearer',
		'enforce_state' => true,
		'require_exact_redirect_uri' => true,
		'allow_implicit' => false,
	)

);