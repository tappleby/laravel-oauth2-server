<?php

namespace Tappleby\OAuth2;

use Tappleby\OAuth2\Commands\PurgeExpiredTokensCommand;
use Tappleby\OAuth2\Filter\AccessFilter;
use Tappleby\OAuth2\Server\Server;
use Tappleby\OAuth2\Storage\AccessTokenStorage;

use Tappleby\OAuth2\Repositories\AccessTokenRepositoryInterface;
use Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryInterface;
use Tappleby\OAuth2\Repositories\RefreshTokenRepositoryInterface;

use Tappleby\OAuth2\Storage\AuthorizationCodeStorage;
use Tappleby\OAuth2\Storage\ClientCredentialsStorage;
use Tappleby\OAuth2\Storage\RefreshTokenStorage;
use Tappleby\OAuth2\Storage\UserStorage;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

class OAuth2ServiceProvider extends ServiceProvider {

	const CLIENT_CREDENTIALS_INTERFACE = 'Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryInterface';
	const ACCESS_TOKEN_INTERFACE = 'Tappleby\OAuth2\Repositories\AccessTokenRepositoryInterface';
	const REFRESH_TOKEN_INTERFACE = 'Tappleby\OAuth2\Repositories\RefreshTokenRepositoryInterface';
  const AUTHORIZATION_CODE_INTERFACE = 'Tappleby\OAuth2\Repositories\AuthorizationCodeRepositoryInterface';

	public function boot()
	{
		$this->package('tappleby/laravel-oauth2-server');
		$this->bindRepositories();
	}

	protected function bindRepositories() {

		$cfg = $this->app['config'];

		$clientCredRepo = $cfg->get('laravel-oauth2-server::repositories.client_credentials', 'Tappleby\OAuth2\Repositories\ClientCredentialsRepositoryEloquent');
		$accessTokenRepo = $cfg->get('laravel-oauth2-server::repositories.access_token', 'Tappleby\OAuth2\Repositories\AccessTokenRepositoryEloquent');
		$refreshTokenRepo = $cfg->get('laravel-oauth2-server::repositories.refresh_token', 'Tappleby\OAuth2\Repositories\RefreshTokenRepositoryEloquent');
    $authCodeRepo = $cfg->get('laravel-oauth2-server::repositories.authorization_code', 'Tappleby\OAuth2\Repositories\AuthorizationCodeRepositoryEloquent');

		$this->app->bind( self::CLIENT_CREDENTIALS_INTERFACE , $clientCredRepo, true );
		$this->app->bind( self::ACCESS_TOKEN_INTERFACE, $accessTokenRepo, true );
		$this->app->bind( self::REFRESH_TOKEN_INTERFACE, $refreshTokenRepo, true );
    $this->app->bind( self::AUTHORIZATION_CODE_INTERFACE, $authCodeRepo, true );
	}


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$app = $this->app;

		$app['oauth2.storage.access_token'] = $app->share(function(Container $app) {
			/** @var AccessTokenRepositoryInterface  $tokenRepo */
			$tokenRepo = $app->make( self::ACCESS_TOKEN_INTERFACE );
			return new AccessTokenStorage( $tokenRepo );
		});

		$app['oauth2.storage.refresh_token'] = $app->share(function(Container $app) {
			/** @var RefreshTokenRepositoryInterface  $tokenRepo */
			$tokenRepo = $app->make( self::REFRESH_TOKEN_INTERFACE );
			return new RefreshTokenStorage( $tokenRepo );
		});

		$app['oauth2.storage.client_credentials'] = $app->share(function(Container $app) {
			/** @var ClientCredentialsRepositoryInterface  $clientRepo */
			$clientRepo = $app->make( self::CLIENT_CREDENTIALS_INTERFACE );
			return new ClientCredentialsStorage( $clientRepo );
		});

    $app['oauth2.storage.authorization_code'] = $app->share(function(Container $app) {
      /** @var AuthorizationCodeRepositoryInterface  $clientRepo */
      $authCodeRepo = $app->make( self::AUTHORIZATION_CODE_INTERFACE );
      return new AuthorizationCodeStorage( $authCodeRepo );
    });

		$app['oauth2.storage.user_storage'] = $app->share(function(Container $app) {
			return new UserStorage( $app['auth'] );
		});

		$app['oauth2.storage'] = $app->share(function(Container $app) {
			$storages = array(
				$app['oauth2.storage.access_token'],
				$app['oauth2.storage.refresh_token'],
				$app['oauth2.storage.client_credentials'],
				$app['oauth2.storage.user_storage'],
        $app['oauth2.storage.authorization_code']
			);

			return array_filter($storages);
		});

		$app['oauth2.server'] = $app->share(function(Container $app) {
			$storage = $app['oauth2.storage'];
			$config = $app['config']->get('laravel-oauth2-server::server', array());

			return new Server( $storage, $config );

		});

		/**
		 * Allow for DI of server.
		 */
		$app->bind('Tappleby\OAuth2\Server\Server', function($app) {
			return $app['oauth2.server'];
		});

		$app->bind('Tappleby\OAuth2\Filter\AccessFilter', function($app) {
			$server = $app['oauth2.server'];
			$events = $app['events'];

			$clientRepo = $app->make( self::CLIENT_CREDENTIALS_INTERFACE );
			$userProvider = $app['auth']->getProvider();

			return new AccessFilter($server, $events, $clientRepo, $userProvider);
		});

		/**
		 * Register commands
		 */

		$this->app['command.oauth2-server.purge-expired-tokens'] = $this->app->share(function ($app) {
			return new PurgeExpiredTokensCommand(
				$app[self::ACCESS_TOKEN_INTERFACE],
				$app[self::AUTHORIZATION_CODE_INTERFACE],
				$app[self::REFRESH_TOKEN_INTERFACE]
			);
		});

		$this->commands('command.oauth2-server.purge-expired-tokens');

	}

	public function provides()
	{
		return array(
			'oauth2.storage.access_token',
			'oauth2.storage.refresh_token',
			'oauth2.storage.client_credentials',
			'oauth2.storage.user_storage',
			'oauth2.storage',
			'oauth2.server'
		);
	}


}