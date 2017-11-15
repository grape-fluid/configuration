<?php

namespace Grapesc\GrapeFluid\Configuration\Crypt;

/**
 * @author Mira Jakes <jakes@grapesc.cz>
 */
interface ICrypt
{

	/**
	 * @param mixed $data
	 * @return mixed
	 */
	public function encrypt($data);

	/**
	 * @param mixed $data
	 * @return mixed
	 */
	public function decrypt($data);

}