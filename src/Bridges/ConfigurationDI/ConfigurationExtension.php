<?php

namespace Grapesc\GrapeFluid\Configuration\Bridges\ConfigurationDI;

use Grapesc\GrapeFluid\Configuration\ParameterEntity;
use Grapesc\GrapeFluid\Configuration\Repository;
use InvalidArgumentException;
use Nette;


/**
 * Fluid Configuration extension for Nette DI.
 */
class ConfigurationExtension extends Nette\DI\CompilerExtension
{

	/** @var array */
	private $schema = [
		'type'        => ParameterEntity::TYPE_STRING,
		'description' => "",
		'default'     => null,
		'nullable'    => false,
		'secured'     => false,
		'enum'        => []
	];


	/** {@inheritdoc} */
	public function loadConfiguration()
	{
		$builder       = $this->getContainerBuilder();
		$configuration = $this->getConfig();

		foreach ($configuration AS $name => &$parameter) {
			if (!$name || preg_match('#[^a-z0-9-._]#i', $name)) {
				throw new InvalidArgumentException("Parameter name must be non-empty alphanumeric string (with '+._'), '$name' given.");
			}

			$parameter = $this->validateConfig($this->schema, $parameter, "parameter.$name");
		}

		$builder->addDefinition($this->prefix('repository'))
			->setFactory(Repository::class)
			->addSetup('loadConfiguration', [$configuration]);

		$builder->addAlias('c', '@' . $this->prefix('repository'));
	}

}
