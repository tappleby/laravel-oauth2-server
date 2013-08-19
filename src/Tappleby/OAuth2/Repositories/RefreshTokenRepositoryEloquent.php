<?php

namespace Tappleby\OAuth2\Repositories;


use Tappleby\OAuth2\Models\RefreshTokenInterface;
use Illuminate\Database\Eloquent\Model;

class RefreshTokenRepositoryEloquent implements RefreshTokenRepositoryInterface {

	/** @var \Tappleby\OAuth2\Models\RefreshToken */
	protected $model = 'Tappleby\OAuth2\Models\RefreshToken';

	/**
	 * @return Model
	 * @throws \UnexpectedValueException
	 */
	public function createModel() {
		$class = '\\'.ltrim($this->model, '\\');
		$model = new $class;

		if(! ($model instanceof RefreshTokenInterface) ) {
			throw new \UnexpectedValueException("Model must implement Tappleby\\OAuth2\\Models\\RefreshTokenInterface");
		}

		return $model;
	}

	/**
	 * @param $token
	 * @return RefreshTokenInterface|Model|null
	 */
	protected function getModelInstance($token) {
		$ret = null;

		if( $token instanceof RefreshTokenInterface && $token instanceof Model ) {
			$ret = $token;
		} else if( $token instanceof RefreshTokenInterface ) {
			$ret = $this->find($token->getId());
		} else {
			$ret = $this->find($token);
		}

		return $ret;
	}

	/**
	 * @param $token
	 * @return RefreshTokenInterface|null
	 */
	function find($token)
	{
		$model = $this->createModel()->find($token);

		return $model;
	}

	/**
	 * @param array $attributes
	 * @param bool $save
	 * @return RefreshTokenInterface|null
	 */
	function create($attributes, $save = true)
	{
		$model = $this->createModel()->fill($attributes);

		if($save) $this->save($model);

		return $model;
	}

	/**
	 * @param RefreshTokenInterface $token
	 * @return bool
	 */
	function save(RefreshTokenInterface $token)
	{
		$saved = false;
		$token = $this->getModelInstance($token);

		if($token) {
			$saved = $token->save();
		}

		return $saved;
	}

	/**
	 * @param RefreshTokenInterface|string $token
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