<?php

use Auditr\Scraper\Response;
use GuzzleHttp\Message\MessageFactory;
use GuzzleHttp\Exception\RequestException;

class ResponseTest extends PHPUnit_Framework_TestCase
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

	private function createResponse($status = 200, $body = 'OK', array $headers = ['Content-Type' => 'text/html;charset=UTF-8'])
	{
		return $this->factory->createResponse($status, $headers, $body);
	}

	private function createException($status = 404, $msg = '')
	{
		return new RequestException('', $this->createRequest(), $this->createResponse($status));
	}


	public function testStatus()
	{
		$response = new Response('http://example.com', $this->createResponse());

		$this->assertInternalType('integer', $response->getStatus());
	}

	public function testType()
	{
		$response = new Response('http://example.com', $this->createResponse());

		$this->assertEquals('text/html', $response->getType());
	}

	public function testHeader()
	{
		$response = new Response('http://example.com', $this->createResponse(200, 'OK', [
			'Content-Type' => 'text/html'
			,'Vary' => 'User-Agent'
		]));

		$this->assertInternalType('array', $response->getHeader('Vary'));
	}

	public function testHTMLResponse()
	{
		$response = new Response('http://example.com', $this->createResponse());

		$this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $response->getBody());
	}

	public function testJSONResponse()
	{
		$response = new Response('http://example.com', $this->createResponse(200, '{
			"status": "OK"
		}', [
			'Content-Type' => 'application/json'
		]));

		$this->assertInternalType('array', $response->getBody());
	}

	public function testOtherResponse()
	{
		$response = new Response('http://example.com', $this->createResponse(200, 'OK', [
			'Content-Type' => 'plain/text'
		]));

		$this->assertInternalType('string', $response->getBody());
	}

	public function testDetectRedirect()
	{
		$mock = $this->getMockBuilder('GuzzleHttp\Message\Response')
				->disableOriginalConstructor()
				->setMethods([
					'getEffectiveURL'
				])
				->getMock();

		$mock->expects($this->once())
			 ->method('getEffectiveURL')
			 ->willReturn('http://example.com/redirected');

		$response = new Response('http://example.com', $mock);

		$this->assertTrue($response->isRedirected());
	}
}
