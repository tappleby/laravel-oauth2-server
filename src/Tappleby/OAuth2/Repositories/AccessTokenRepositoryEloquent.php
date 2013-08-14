<?php

namespace Tappleby\OAuth2\Repositories;


use Tappleby\OAuth2\Models\AccessTokenInterface;
use Illuminate\Database\Eloquent\Model;

class AccessTokenRepositoryEloquent implements AccessTokenRepositoryInterface {

	/** @var \Tappleby\OAuth2\Models\AccessToken */
	protected $model = 'Tappleby\OAuth2\Models\AccessToken';

	/**
	 * @return Model
	 * @throws \UnexpectedValueException
	 */
	public function createModel() {
		$class = '\\'.ltrim($this->model, '\\');
		$model = new $class;

		if(! ($model instanceof AccessTokenInterface) ) {
			throw new \UnexpectedValueException("Model must implement Tappleby\\OAuth2\\Models\\AccessTokenInterface");
		}

		return $model;
	}

	/**
	 * @param $token
	 * @return AccessTokenInterface|Model|null
	 */
	protected function getModelInstance($token) {
		$ret = null;

		if( $token instanceof AccessTokenInterface && $token instanceof Model ) {
			$ret = $token;
		} else if( $token instanceof AccessTokenInterface ) {
			$ret = $this->find($token->getId());
		} else {
			$ret = $this->find($token);
		}

		return $ret;
	}

	/**
	 * @param $token
	 * @return AccessTokenInterface|null
	 */
	function find($token)
	{
		$model = $this->createModel()->find($token);

		return $model;
	}

	/**
	 * @param array $attributes
	 * @param bool $save
	 * @return AccessTokenInterface|null
	 */
	function create($attributes, $save = true)
	{
		$attributes['access_token'] = $attributes['id'];
		unset($attributes['id']);
		$model = $this->createModel()->fill($attributes);

		if($save) $this->save($model);

		return $model;
	}

	/**
	 * @param AccessTokenInterface $token
	 * @return bool
	 */
	function save(AccessTokenInterface $token)
	{
		$saved = false;
		$token = $this->getModelInstance($token);

		if($token) {
			$saved = $token->save();
		}

		return $saved;
	}

	/**
	 * @param AccessTokenInterface|string $token
	 * @return bool
	 */
	function delete($token)
	{
		$deleted = false;
		$token = $this->getModelInstance($token);

		if($token) {
			$deleted = $token->delete();
		}

		return $deleted;
	}


}