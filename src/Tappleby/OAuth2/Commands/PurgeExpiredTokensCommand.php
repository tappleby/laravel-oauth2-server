<?php namespace Tappleby\OAuth2\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Tappleby\OAuth2\Repositories\AccessTokenRepositoryInterface;
use Tappleby\OAuth2\Repositories\AuthorizationCodeRepositoryInterface;
use Tappleby\OAuth2\Repositories\PurgeExpiredInterface;
use Tappleby\OAuth2\Repositories\RefreshTokenRepositoryInterface;

class PurgeExpiredTokensCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'oauth2-server:purge-expired';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Removes expired tokens';

	protected $accessTokenRepo;
	protected $authCodeRepo;
	protected $refreshTokenRepo;


	/**
	 * Create a new command instance.
	 *
	 */
	function __construct(AccessTokenRepositoryInterface $accessTokenRepo, AuthorizationCodeRepositoryInterface $authCodeRepo, RefreshTokenRepositoryInterface $refreshTokenRepo)
	{
		parent::__construct();

		$this->accessTokenRepo = $accessTokenRepo;
		$this->authCodeRepo = $authCodeRepo;
		$this->refreshTokenRepo = $refreshTokenRepo;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		if($this->accessTokenRepo instanceof PurgeExpiredInterface) {
			$this->info("Purging expired AccessTokens");
			$this->accessTokenRepo->purgeExpired();
		} else {
			$this->info("Skipping AccessTokenRepository, Must implement PurgeExpiredInterface");
		}

		if($this->authCodeRepo instanceof PurgeExpiredInterface) {
			$this->info("Purging expired AuthorizationCodes");
			$this->authCodeRepo->purgeExpired();
		} else {
			$this->info("Skipping AuthorizationCodeRepository, Must implement PurgeExpiredInterface");
		}

		if($this->refreshTokenRepo instanceof PurgeExpiredInterface) {
			$this->info("Purging expired RefreshTokens");
			$this->refreshTokenRepo->purgeExpired();
		} else {
			$this->info("Skipping RefreshTokenRepository, Must implement PurgeExpiredInterface");
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}