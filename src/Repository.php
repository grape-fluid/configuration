<?php

namespace Grapesc\GrapeFluid\Configuration;

use Grapesc\GrapeFluid\Configuration\Crypt\ICrypt;
use Nette\Caching\Cache;
use Nette\Caching\IStorage as ICacheStorage;


/**
 * @author Mira Jakes <jakes@grapesc.cz>
 */
class Repository
{

	/** @var IStorage */
	private $storage;

	/** @var array */
	private $configuration = [];

	/** @var ParameterEntity[] */
	private $parameters = [];

	/** @var bool */
	private $isBuilt = false;

	/** @var ICacheStorage|null */
	private $cacheStorage = null;

	/** @var array */
	private $memoryCache = [];
	
	/** @var array */
	private $gettingCount = [];

	/** @var ICrypt */
	private $crypt;


	/**
	 * Repository constructor.
	 * @param IStorage $storage
	 * @param ICrypt $crypt
	 * @param ICacheStorage|null $cacheStorage
	 */
	public function __construct(IStorage $storage, ICrypt $crypt, ICacheStorage $cacheStorage = null)
	{
		$this->storage      = $storage;
		$this->crypt        = $crypt;
		$this->cacheStorage = $cacheStorage;
	}


	/**
	 * @param string $tid
	 * @param null $defaultValue
	 * @return mixed
	 */
	public function getValue($tid, $defaultValue = null)
	{
		if (array_key_exists($tid, $this->gettingCount)) {
			$this->gettingCount[$tid]++;
		} else {
			$this->gettingCount[$tid] = 1;  
		}
		
		if (array_key_exists($tid, $this->memoryCache)) {
			$value = $this->memoryCache[$tid];
		} else {
			$value = $this->retype($tid, $this->storage->getValue($tid, $defaultValue));
			$this->memoryCache[$tid] = $value;
		}

		if (array_key_exists($tid, $this->parameters) && $this->parameters[$tid]->secured) {
			$value = $this->crypt->decrypt($value);
		}

		return $value;
	}


	/**
	 * @param string $tid
	 * @param $value
	 */
	public function setValue($tid, $value)
	{
		$value = $this->retype($tid, $value);

		if (array_key_exists($tid, $this->parameters) && $this->parameters[$tid]->secured) {
			$value = $this->crypt->encrypt($value);
		}

		$this->memoryCache[$tid] = $value;
		$this->storage->setValue($tid, $value);
	}


	/**
	 * Get parameters, for using in neon parameters use @c::val(parameter.tid)
	 * @param string $tid
	 * @param null $defaultValue
	 * @return mixed
	 */
	public function val($tid, $defaultValue = null)
	{
		return $this->getValue($tid, $defaultValue);
	}


	/**
	 * Concat parameters with @c::val(), for using in neon parameters use @c::con(%test.config.value%, withSuffix, %test.config.value2%, ...)
	 * @param array ...$strings
	 * @return string
	 */
	public function con(...$strings)
	{
		return implode("", $strings);
	}


	/**
	 * @param array $configuration
	 */
	public function loadConfiguration($configuration)
	{
		$this->configuration = $configuration;
		$this->build();
	}


	/**
	 * @return ParameterEntity[]
	 */
	public function getParameters()
	{
		return $this->parameters;
	}


	/**
	 * @return array
	 * @internal
	 */
	public function getMemoryCache()
	{
		return $this->memoryCache;
	}


	/**
	 * @return void
	 */
	public function clearMemoryCache()
	{
		$this->memoryCache = [];
	}
	

	/**
	 * @return array
	 * @internal
	 */
	public function getGettingCount()
	{
		return $this->gettingCount;
	}


	/**
	 * @return void
	 */
	protected function checkStorageConsistency()
	{
		foreach ($this->parameters AS $parameter) {
			if (!$this->storage->hasParameter($parameter)) {
				$this->storage->newParameter($parameter);
			} else {
				$this->storage->updateParameter($parameter);
			}
		}
	}


	/**
	 * @param $tid
	 * @param $val
	 * @return bool|float|int|string|mixed
	 */
	protected function retype($tid, $val)
	{
		if (key_exists($tid, $this->parameters)) {
			$parameter = $this->parameters[$tid];
			switch ($parameter->type) {
				case ParameterEntity::TYPE_BOOLEAN:
				case ParameterEntity::TYPE_BOOL:
					if ((is_null($val) || ($val != true && $val != false)) && $parameter->nullable) {
						$val = null;
					} else {
						$val = (bool) $val;
					}
					break;
				case ParameterEntity::TYPE_STRING:
				case ParameterEntity::TYPE_TEXT:
					if ((is_null($val) || $val == "") && $parameter->nullable) {
						$val = null;
					} else {
						$val = (string)$val;
					}
					break;
				case ParameterEntity::TYPE_INTEGER:
				case ParameterEntity::TYPE_INT:
					if ((is_null($val) || $val == "") && $parameter->nullable) {
						$val = null;
					} else {
						$val = (int) $val;
					}
					break;
				case ParameterEntity::TYPE_FLOAT:
					if ((is_null($val) || $val == "") && $parameter->nullable) {
						$val = null;
					} else {
						$val = (float)$val;
					}
					break;
				default:
			}
		}

		return $val;
	}


	/**
	 * @return void
	 */
	protected function build()
	{
		if ($this->isBuilt) {
			return;
		}

		$this->isBuilt = true;

		if ($this->cacheStorage) {
			$cache = new Cache($this->cacheStorage, 'Fluid.Configuration');
			if ($load = $cache->load('parameters' . md5(serialize($this->configuration)))) {
				$this->parameters = $load;
				return;
			}
		}

		$this->checkStorageConsistency();

		foreach ($this->configuration AS $tid => $parameter) {
			$entity = new ParameterEntity();
			$entity->tid = $tid;

			foreach ($parameter AS $key => $value) {
				if (property_exists($entity, $key)) {
					$entity->{$key} = $value;
				}
			}

			if (!$entity->nullable AND is_null($entity->default)) {
				if (in_array($entity->type, [ParameterEntity::TYPE_STRING, ParameterEntity::TYPE_TEXT])) {
					$exploded = explode(".", $entity->tid);
					end($exploded);
					$entity->default = ucfirst(current($exploded));
				} elseif (in_array($entity->type, [ParameterEntity::TYPE_INT, ParameterEntity::TYPE_INTEGER])) {
					$entity->default = 0;
				} elseif (in_array($entity->type, [ParameterEntity::TYPE_BOOL, ParameterEntity::TYPE_BOOLEAN])) {
					$entity->default = false;
				} elseif (in_array($entity->type, [ParameterEntity::TYPE_FLOAT])) {
					$entity->default = 0.0;
				}
			}

			$this->parameters[$tid] = $entity;
			$this->checkStorageConsistency();
		}

		if ($this->cacheStorage) {
			$cache->save('parameters' . md5(serialize($this->configuration)), $this->parameters);
		}
	}

}