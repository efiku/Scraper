<?php

use Auditr\Scraper;

class ResponseTest extends PHPUnit_Framework_TestCase
{
	public function testStatus()
	{
		$response = (new Scraper('http://httpbin.org/redirect-to?url=http://www.google.com'))->fetch();

		$this->assertInternalType('integer', $response->getStatus());
	}

	public function testType()
	{
		$response = (new Scraper('https://comandeer.pl'))->fetch();

		$this->assertEquals('text/html', $response->getType());
	}

	public function testHeader()
	{
		$response = (new Scraper('https://comandeer.pl'))->fetch();

		$this->assertInternalType('array', $response->getHeader('Vary'));
	}

	public function testHTMLResponse()
	{
		$response = (new Scraper('https://comandeer.pl'))->fetch();

		$this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $response->getBody());
	}

	public function testJSONResponse()
	{
		$response = (new Scraper('http://bzdety.comandeer.pl/scraper.json'))->fetch();

		$this->assertInternalType('array', $response->getBody());
	}

	public function testOtherResponse()
	{
		$response = (new Scraper('http://bzdety.comandeer.pl/scraper.txt'))->fetch();

		$this->assertInternalType('string', $response->getBody());
	}
}
