<?php

namespace Tappleby\OAuth2\Repositories;


use Tappleby\OAuth2\Models\AccessTokenBasic;
use Tappleby\OAuth2\Models\AccessTokenInterface;

class AccessTokenRepositoryRedis implements  AccessTokenRepositoryInterface {

	/** @var \Predis\Connection\SingleConnectionInterface */
	protected $redis;

	protected $namespace;
	protected $prefix = 'access';

	function __construct($redis, $namespace = 'oauth')
	{
		$this->namespace = $namespace;
		$this->redis = $redis;
	}

	protected function key() {
		$data = array_merge( array($this->namespace, $this->prefix), func_get_args() );
		return implode(':', $data);
	}


	/**
	 * @param $token
	 * @return AccessTokenInterface|null
	 */
	function find($token)
	{
		$clientKey = $this->key($token);

		$token_data = null;
		$payload = $this->redis->get($clientKey);

		if($payload) {
			$payload = json_decode($payload);

			$token_data = new AccessTokenBasic(
				$token, $payload->client_id, $payload->user_id, $payload->expires, $payload->scope
			);
		}

		return $token_data;
	}

	/**
	 * @param array $attributes
	 * @param bool $save
	 * @return AccessTokenInterface|null
	 */
	function create($attributes, $save = true)
	{

		$token_data = new AccessTokenBasic(
			$attributes['id'],
			$attributes['client_id'],
			$attributes['user_id'],
			$attributes['expires'],
			$attributes['scope']
		);

		$this->save($token_data);

		return $token_data;
	}

	/**
	 * @param AccessTokenInterface $token
	 * @return bool
	 */
	function save(AccessTokenInterface $token)
	{
		$data = array(
			'client_id' => $token->getClientId(),
			'user_id' => $token->getUserId(),
			'expires' => $token->getExpires(),
			'scope' => $token->getScopes(),
		);

		$payload = json_encode($data);
		$expires = $token->getExpires() - time();

		$this->redis->setex( $this->key($token->getId()), $expires, $payload );
	}

	/**
	 * @param AccessTokenInterface|string $token
	 * @return bool
	 */
	function delete($token)
	{
		$deleted = false;

		if(!($token instanceof AccessTokenInterface)) {
			$token = $this->find($token);
		}

		if($token) {
			$deleted = $this->redis->del( $this->key( $token->getId() ) );
			$deleted = $deleted > 0;
		}

		return $deleted;
	}


}