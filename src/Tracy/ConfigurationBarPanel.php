<?php

namespace Grapesc\GrapeFluid\Configuration\Tracy;

use Grapesc\GrapeFluid\Configuration\Repository;
use Tracy;


/**
 * @author Mira Jakes <jakes@grapesc.cz>
 */
class ConfigurationBarPanel implements Tracy\IBarPanel
{

	/** @var Repository */
	private $repository;


	/**
	 * ConfigurationBarPanel constructor.
	 * @param Repository $repository
	 */
	public function __construct(Repository $repository)
	{
		$this->repository = $repository;
	}


	/**
	 * @return string
	 */
	public function getTab()
	{
		return 	"<span title='Configuration'>" .
				"<img src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4QgfEDkINb/IdwAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAACIUlEQVQ4y52TTUiUURSGn3vn0+b7jKzEGWlS+iETRBIqiChpUQS2C3IholDiwk0gCEKbiDYua90PBJUW1KCQEoSN0Q9JWS0MraiFMkajM47OjOPM3NNGR5uxkF64i3vOed9zD/c9ynvwbGCL7a5CwGBA2BCUUkSTyWlrs+NUP+s6WRIorTAHnBpdqPSGBBzbzekL7R7LpQzl9S00wcaYa2BZ/0HKRZ5A/+AQE99+ZO9vRj4QiyeYCv7kTo//3wLvP47R0NpJz6MniBgAGlo7ufewj8DLEc5fvMzwq5E/FSrrGkMiIkuplGzdc1Sa27tkdiYkwWBQeh8PSGnlcZmcmpS5SFguXb0m7p2H5FdoVkREak61SPYFLg1Veyt4/uIdscQi0eg8bR1X6L3RDQILsTgP/IP4vCUszM/lj6C1xeun9/GUbqP7+i3Gxr+zy1fGvt0VANzu6SNjDEP+m1hWAdG5MAKoyrrG0HjgbsmKUGIxSSQSRozgUopUJr3cQLMQi+PYNiAUOW6ONXTk/4Lt3oTX48GYTJYMYIzBsd3kWnVdH2it8fl2oLVGa/XXAworlU7zaewrieRSjtchk84wEw6jyF8Rp7CApVQK5Tl8rnkxHqtCqWxSxJSbWLSJ5S6IwXKK/Wj9ebVGsG1nUq03Qlntmdp4JDQKakWQou3e+unR/oG8rcwNFO8/8VYyKUdlTPVqWsDlmhClw9Evw0fW1v8Gwa3ivtptzvQAAAAASUVORK5CYII=\" />" .
				" Configuration (" . count($this->repository->getParameters()) . ")" .
				"</span>";
	}


	/**
	 * @return string
	 */
	function getPanel()
	{
		$innerHtml    = "";
		$memCache     = $this->repository->getMemoryCache();
		$gettingCount = $this->repository->getGettingCount();
		$parameters   = $this->repository->getParameters();

		arsort($gettingCount);

		$tids = array_merge(array_keys($gettingCount), array_diff(array_keys($parameters), array_keys($gettingCount)));

		foreach ($tids AS $tid) {
			$parameter = $parameters[$tid];
			$innerHtml.= "<tr><td>{$parameter->tid}</td><td>{$parameter->type}</td>";
			$innerHtml.= "<td>" . (array_key_exists($parameter->tid, $memCache) ? $memCache[$parameter->tid] : '?') . "</td>";
			$innerHtml.= "<td>{$parameter->default}</td>";
			$innerHtml.= "<td>" . (array_key_exists($parameter->tid, $gettingCount) ? $gettingCount[$parameter->tid] : 0) . "x</td></tr>";
		}

		$html = "<h1>Getting parameter: " . array_sum($gettingCount) . "x, Without cache: " . count($memCache) . "x</h1>";
		$html.= "<div class='tracy-inner'>";

		$html.= "<table>";
		$html.= "<tr><th>Tid</th><th>Type</th><th>Current Value</th><th>Default Value</th><th>Getting</th></tr>";

		$html.= $innerHtml;

		$html.= "<table>";
		$html.= "</div>";

		return $html;
	}

}