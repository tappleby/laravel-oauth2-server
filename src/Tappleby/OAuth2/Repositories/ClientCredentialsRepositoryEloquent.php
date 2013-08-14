<?php

namespace Tappleby\OAuth2\Repositories;


use Tappleby\OAuth2\Models\ClientCredentialsInterface;

class ClientCredentialsRepositoryEloquent implements ClientCredentialsRepositoryInterface {

	/** @var \Tappleby\OAuth2\Models\ClientCredentials */
	protected $model = 'Tappleby\OAuth2\Models\ClientCredentials';

	/**
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createModel() {
		$class = '\\'.ltrim($this->model, '\\');
		$model = new $class;

		if(! ($model instanceof ClientCredentialsInterface) ) {
			throw new \UnexpectedValueException("Model must implement Tappleby\\OAuth2\\Models\\ClientCredentialsInterface");
		}

		return $model;
	}

	/**
	 * @param $client_id
	 * @return ClientCredentialsInterface|null
	 */
	function find($client_id)
	{
		$model = $this->createModel()->find($client_id);

		return $model;
	}

	/**
	 * @param array $attributes
	 * @param bool $save
	 * @return ClientCredentialsInterface|null
	 */
	function create($attributes, $save = true)
	{
		$model = $this->createModel()->fill($attributes);

		if($save) $model->save();

		return $model;
	}

	/**
	 * @param ClientCredentialsInterface $client
	 * @return bool
	 */
	function save(ClientCredentialsInterface $client)
	{
		//TODO: Should handle case where this isnt a eloquent model.
		$client->save();
	}
}