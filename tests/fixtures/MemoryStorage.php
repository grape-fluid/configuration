<?php

namespace Tests\Fixtures;

use Grapesc\GrapeFluid\Configuration\IStorage;
use Grapesc\GrapeFluid\Configuration\ParameterEntity;


/**
 * @author Mira Jakes <jakes@grapesc.cz>
 */
class MemoryStorage implements IStorage
{

	/** @var array */
	private $storage = [];


	/**
	 * @param string $name
	 * @param null|mixed $defaultValue
	 * @return mixed
	 */
	public function getValue($name, $defaultValue = null)
	{
		return array_key_exists($name, $this->storage) ? $this->storage[$name] : $defaultValue;
	}


	/**
	 * @param string $name
	 * @param mixed $value
	 */
	public function setValue($name, $value)
	{
		$this->storage[$name] = $value;
	}


	/**
	 * @param ParameterEntity $parameter
	 */
	public function newParameter(ParameterEntity $parameter)
	{
		$this->storage[$parameter->tid] = $parameter->default;
	}


	/**
	 * @param ParameterEntity $parameter
	 */
	public function updateParameter(ParameterEntity $parameter)
	{
	}


	/**
	 * @param ParameterEntity $parameter
	 * @return bool
	 */
	public function hasParameter(ParameterEntity $parameter)
	{
		return array_key_exists($parameter->tid, $this->storage);
	}

}