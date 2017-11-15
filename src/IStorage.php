<?php

namespace Grapesc\GrapeFluid\Configuration;

/**
 * @author Mira Jakes <jakes@grapesc.cz>
 */
interface IStorage
{

	/**
	 * @param string $name
	 * @param null|mixed $defaultValue
	 * @return mixed
	 */
	public function getValue($name, $defaultValue = null);

	/**
	 * @param string $name
	 * @param mixed $value
	 */
	public function setValue($name, $value);

	/**
	 * @param ParameterEntity $parameter
	 */
	public function newParameter(ParameterEntity $parameter);

	/**
	 * @param ParameterEntity $parameter
	 */
	public function updateParameter(ParameterEntity $parameter);

	/**
	 * @param ParameterEntity $parameter
	 * @return bool
	 */
	public function hasParameter(ParameterEntity $parameter);

}