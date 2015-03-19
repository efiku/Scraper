<?php

use Auditr\Scraper;
use GuzzleHttp\Message\MessageFactory;
use GuzzleHttp\Exception\RequestException;

class FetchTest extends PHPUnit_Framework_TestCase
{
	private $factory;

	public function setUp()
	{
		$this->factory = new MessageFactory();
	}

	private function createRequest($method = 'GET', $url = 'http://example.com')
	{
		return $this->factory->createRequest($method, $url);
	}

	private function createResponse($status = 200, $body = 'OK', array $headers = ['Content-Type' => 'text/html'])
	{
		return $this->factory->createResponse($status, $headers, $body);
	}

	private function createException($status = 404, $msg = '')
	{
		return new RequestException('', $this->createRequest(), $this->createResponse($status));
	}

	public function testCorrectFetch()
	{
		$scraper = new Scraper('https://comandeer.pl');

		$curl = $this->getMockBuilder('GuzzleHttp\\Client')
				->setMethods([
					'get'
				])
				->getMock();

		$curl->expects($this->once())
			 ->method('get')
			 ->willReturn($this->createResponse());

		$scraper->setCurl($curl);

		$result = $scraper->fetch();

		$this->assertInstanceOf('Auditr\\Scraper\\Response', $result);
	}

	public function testHeadMethod()
	{
		$scraper = new Scraper('https://comandeer.pl');

		$curl = $this->getMockBuilder('GuzzleHttp\\Client')
				->setMethods([
					'head'
				])
				->getMock();

		$curl->expects($this->once())
			 ->method('head')
			 ->willReturn($this->createResponse());

		$scraper->setCurl($curl);

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

		$curl = $this->getMockBuilder('GuzzleHttp\\Client')
				->setMethods([
					'get'
				])
				->getMock();

		$curl->expects($this->once())
			 ->method('get')
			 ->will($this->throwException($this->createException()));

		$scraper->setCurl($curl);

		$scraper->fetch();
	}

	/**
	 * @expectedException Auditr\Scraper\Exception\HTTP403
	 */
	public function test403Error()
	{
		$scraper = new Scraper('http://bzdety.comandeer.pl/scraper403');

		$curl = $this->getMockBuilder('GuzzleHttp\\Client')
				->setMethods([
					'get'
				])
				->getMock();

		$curl->expects($this->once())
			 ->method('get')
			 ->will($this->throwException($this->createException(403)));

		$scraper->setCurl($curl);

		$scraper->fetch();
	}

	
	public function testURIValidation()
	{
		$this->assertTrue(Scraper::checkURI('ftp://whatever.dot'));

		$this->assertFalse(Scraper::checkURI('invalid-URI'));
	}
}
