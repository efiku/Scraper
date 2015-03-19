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
		if(!self::checkURI($URI))
			throw new Exception('Given URI is not valid');

		$this->URI = $URI;

		$this->curl = new Curl();
	}

	/**
	 * Replaces original cURL
	 *
	 * @param $curl    Curl   Curl instance
	 * @return void
	 */
	public function setCurl(Curl $curl)
	{
		$this->curl = $curl;
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

			return new Response($this->URI, $response);
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

	/**
	 * Checks if URI is correct
	 *
	 * @static
	 * @param $URI    string    URI to check
	 * @return boolean
	 */
	public static function checkURI($URI)
	{
		return (boolean)preg_match('_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS', $URI);
	}
}
