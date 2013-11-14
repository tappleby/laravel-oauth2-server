<?php
/*
 * User: tappleby
 * Date: 11/4/2013
 * Time: 10:30 PM
 */

namespace Tappleby\OAuth2\Repositories;


use Illuminate\Database\Eloquent\Model;
use Tappleby\OAuth2\Models\AuthorizationCodeInterface;

class AuthorizationCodeRepositoryEloquent implements AuthorizationCodeRepositoryInterface, PurgeExpiredInterface {

	/** @var \Tappleby\OAuth2\Models\AuthorizationCode */
	protected $model = 'Tappleby\OAuth2\Models\AuthorizationCode';

	/**
	 * @return Model
	 * @throws \UnexpectedValueException
	 */
	public function createModel() {
		$class = '\\'.ltrim($this->model, '\\');
		$model = new $class;

		if(! ($model instanceof AuthorizationCodeInterface) ) {
			throw new \UnexpectedValueException("Model must implement Tappleby\\OAuth2\\Models\\AuthorizationCodeInterface");
		}

		return $model;
	}

	/**
	 * @param $authCode
	 * @return AuthorizationCodeInterface|Model|null
	 */
	protected function getModelInstance($authCode) {
		$ret = null;

		if( $authCode instanceof AuthorizationCodeInterface && $authCode instanceof Model ) {
			$ret = $authCode;
		} else if( $authCode instanceof AuthorizationCodeInterface ) {
			$ret = $this->find($authCode->getId());
		} else {
			$ret = $this->find($authCode);
		}

		return $ret;
	}

	/**
	 * @param $code
	 * @return AuthorizationCodeInterface|null
	 */
	function find($code)
	{
		$model = $this->createModel()->find($code);

		return $model;
	}

	/**
	 * @param array $attributes
	 * @param bool $save
	 * @return AuthorizationCodeInterface|null
	 */
	function create($attributes, $save = true)
	{
		$model = $this->createModel()->fill($attributes);

		if($save) $this->save($model);

		return $model;
	}

	/**
	 * @param AuthorizationCodeInterface $code
	 * @return bool
	 */
	function save(AuthorizationCodeInterface $code)
	{
		$saved = false;
		$authCode = $this->getModelInstance($code);

		if($authCode) {
			$saved = $authCode->save();
		}

		return $saved;
	}

	/**
	 * @param AuthorizationCodeInterface|string $code
	 * @return bool
	 */
	function delete($code)
	{
		$deleted = false;
		$authCode = $this->getModelInstance($code);

		if($authCode) {
			$deleted = $authCode->delete();
		}

		return $deleted;
	}

	/**
	 * Purges expired tokens or codes.
	 *
	 * @return void
	 */
	function purgeExpired()
	{
		$this->createModel()->where('expires', '<=', new \DateTime())->delete();
	}


}