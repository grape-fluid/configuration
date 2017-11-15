<?php

namespace Grapesc\GrapeFluid\Configuration;


/**
 * @author Mira Jakes <jakes@grapesc.cz>
 */
class ParameterEntity
{

	CONST TYPE_TEXT = "text";
	CONST TYPE_STRING = "string";

	CONST TYPE_INT = "int";
	CONST TYPE_INTEGER = "integer";

	CONST TYPE_BOOL = "bool";
	CONST TYPE_BOOLEAN = "boolean";

	CONST TYPE_FLOAT = "float";

	/** @var string */
	public $tid;

	/** @var string */
	public $type = self::TYPE_STRING;

	/** @var string */
	public $description = "";

	/** @var mixed */
	public $default;

	/** @var bool */
	public $nullable = false;

	/** @var array  */
	public $enum = [];

	/** @var bool */
	public $secured = false;

}