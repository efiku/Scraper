<?php
/**
 * Scraper
 * It scrapes websites
 *
 * @package Auditr/Scraper
 * @author Comandeer
 * @copyright  (c) 2015 Comandeer
 * @license MIT
 */

namespace Auditr;

use GuzzleHttp\Client as Curl;
use GuzzleHttp\Exception\RequestException;
use Auditr\Scraper\Response;
use Auditr\Scraper\Exception;
use Auditr\Scraper\Exception\HTTP403;
use Auditr\Scraper\Exception\HTTP404;

class Scraper
{
	/**
	 * URI of page being scraped
	 *
	 * @var string
	 */
	private $URI;

	/**
	 * CURL wrapper
	 *
	 * @var GuzzleHttp\Client
	 */
	private $curl;

	/**
	 * Constructor
	 *
	 * @param $URI    string    URI of page to be scraped
	 * @throws \Auditr\Scraper\Exception
	 * @return void
	 */
	public function __construct($URI)
	{
		$this->URI = $URI;

		$this->curl = new Curl();
	}

	/**
	 * Fetches the resource
	 *
	 * @param $method    string    HTTP method to use
	 * @throws Auditr\Scraper\Exception
	 * @throws Auditr\Scraper\Exception\HTTP{code}
	 * @return Auditr\Scraper\Response
	 */
	public function fetch($method = 'get')
	{
		try
		{
			if(!method_exists($this->curl, $method))
				throw new Exception('Unknown HTTP method');

			$response = $this->curl->$method($this->URI);

			return new Response($response);
		}
		catch(RequestException $e)
		{
			$response = $e->getResponse();

			if(in_array($response->getStatusCode(), [403, 404]))
			{
				$error = 'Auditr\\Scraper\\Exception\\HTTP' . $response->getStatusCode();
				throw new $error();
			}
		}
	}
}
