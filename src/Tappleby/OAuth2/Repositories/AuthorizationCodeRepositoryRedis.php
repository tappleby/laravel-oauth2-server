<?php

namespace Tappleby\OAuth2\Repositories;


use Tappleby\OAuth2\Models\AccessTokenBasic;
use Tappleby\OAuth2\Models\AccessTokenInterface;
use Tappleby\OAuth2\Models\AuthorizationCodeBasic;
use Tappleby\OAuth2\Models\AuthorizationCodeInterface;

class AuthorizationCodeRepositoryRedis implements AuthorizationCodeRepositoryInterface {

	/** @var \Predis\Connection\SingleConnectionInterface */
	protected $redis;

	protected $namespace;
	protected $prefix = 'authorization';

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
   * @param $code
   * @return AuthorizationCodeInterface|null
   */
  function find($code)
  {
    $clientKey = $this->key($code);

    $authCode = null;
    $payload = $this->redis->get($clientKey);

    if ($payload) {
      $payload = json_decode($payload);

      $authCode = new AuthorizationCodeBasic(
        $code, $payload->client_id, $payload->user_id, $payload->redirect_uri, $payload->expires, $payload->scope
      );
    }

    return $authCode;
  }

  /**
   * @param array $attributes
   * @param bool $save
   * @return AuthorizationCodeInterface|null
   */
  function create($attributes, $save = true)
  {
    $authCode = new AuthorizationCodeBasic(
      $attributes['id'],
      $attributes['client_id'],
      $attributes['user_id'],
      $attributes['redirect_uri'],
      $attributes['expires'],
      $attributes['scope']
    );

    $this->save($authCode);

    return $authCode;
  }

  /**
   * @param AuthorizationCodeInterface $code
   * @return bool
   */
  function save(AuthorizationCodeInterface $code)
  {
    $data = array(
      'client_id' => $code->getClientId(),
      'user_id' => $code->getUserId(),
      'redirect_uri' => $code->getRedirectUri(),
      'expires' => $code->getExpires(),
      'scope' => $code->getScopes(),
    );

    $payload = json_encode($data);
    $expires = $code->getExpires() - time();

    $key = $this->key($code->getId());
    $this->redis->setex($key, $expires, $payload);
  }

  /**
   * @param AuthorizationCodeInterface|string $code
   * @return bool
   */
  function delete($code)
  {
    $deleted = false;

    if (!($code instanceof AuthorizationCodeInterface)) {
      $code = $this->find($code);
    }

    if ($code) {
      $key = $this->key($code->getId());
      $deleted = $this->redis->del($key);
      $deleted = $deleted > 0;
    }

    return $deleted;
  }
}