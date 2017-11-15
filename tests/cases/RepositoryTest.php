<?php

namespace Tests\Cases;

require __DIR__ . '/../bootstrap.php';

use Grapesc\GrapeFluid\Configuration\Repository;
use Nette\Neon\Neon;
use Tester\TestCase;
use Tester\Assert;
use Tests\Fixtures\DummyCrypt;
use Tests\Fixtures\MemoryStorage;


class RepositoryTest extends TestCase
{

	/** @var array */
	private $configuration = [];

	/** @var Repository */
	private $repository;


	public function __construct()
	{
		$this->configuration = Neon::decode(file_get_contents(__DIR__ . '/../fixtures/configuration.neon'));
	}


	public function setUp()
	{
		$this->repository = new Repository(new MemoryStorage(), new DummyCrypt());
		$this->repository->loadConfiguration($this->configuration);
	}


	public function testGetParameters()
	{
		Assert::count(2, $this->repository->getParameters());
    }


	/**
	 * @dataProvider ../fixtures/strings.ini
	 */
    public function testSecuredParameter($string)
	{
		$this->repository->setValue('test.string.secured', $string);
		$this->repository->clearMemoryCache();

		Assert::same($string, $this->repository->getValue('test.string.secured'));
	}

}

(new RepositoryTest)->run();