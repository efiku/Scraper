<?php

use Auditr\Scraper;

class FetchTest extends PHPUnit_Framework_TestCase
{

	public function testCorrectFetch()
	{
		$scraper = new Scraper('https://comandeer.pl');
		$result = $scraper->fetch();

		$this->assertInstanceOf('Auditr\\Scraper\\Response', $result);
	}

	public function testHeadMethod()
	{
		$scraper = new Scraper('https://comandeer.pl');
		$result = $scraper->fetch('head');

		$this->assertInstanceOf('Auditr\\Scraper\\Response', $result);
	}

	/**
	 * @expectedException Auditr\Scraper\Exception
	 */
	public function testUnkownMethod()
	{
		$scraper = new Scraper('https://comandeer.pl');
		$result = $scraper->fetch('hublabubla');

		$this->assertInstanceOf('Auditr\\Scraper\\Response', $result);
	}

	/**
	 * @expectedException Auditr\Scraper\Exception\HTTP404
	 */
	public function test404Error()
	{
		$scraper = new Scraper('https://comandeer.pl/nieistnieje');

		$scraper->fetch();
	}

	/**
	 * @expectedException Auditr\Scraper\Exception\HTTP403
	 */
	public function test403Error()
	{
		$scraper = new Scraper('http://bzdety.comandeer.pl/scraper403');

		$scraper->fetch();
	}
}
