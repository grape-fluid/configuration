<?php

namespace Tests\Fixtures;

use Grapesc\GrapeFluid\Configuration\Crypt\ICrypt;


/**
 * @author Mira Jakes <jakes@grapesc.cz>
 */
class DummyCrypt implements ICrypt
{

	CONST PREFIX = "prefix_";

	CONST SUFFIX = "_suffix";

	/**
	 * @param mixed $data
	 * @return mixed
	 */
	public function encrypt($data)
	{
		return self::PREFIX . $data . self::SUFFIX;
	}

	/**
	 * @param mixed $data
	 * @return mixed
	 */
	public function decrypt($data)
	{
		$data = preg_replace('/^' . self::PREFIX . '/', '', $data);
		return preg_replace('/' . self::SUFFIX . '$/', '', $data);
	}

}