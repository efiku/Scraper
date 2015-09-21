<?php
/**
 * Scraper's response
 * Thin wrapper around Guzzle's response object
 *
 * @package Auditr/Scraper
 * @author Comandeer
 * @copyright  (c) 2015 Comandeer
 * @license MIT
 */


namespace Auditr\Scraper;

use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response as Resource;
use Symfony\Component\DomCrawler\Crawler;

class Response
{
    /**
     * Stores requested URI
     *
     * @var string
     */
    private $URI;

    /**
     * Stores original response from Guzzle
     *
     * @var Resource
     */
    private $resource;

    /**
     * Constructs scraper's response
     *
     * @param $URI    string    requested URI
     * @param $resource  \GuzzleHttp\Message\Response  original Guzzle's response
     */
    public function __construct($URI, Resource $resource)
    {
        $this->URI = $URI;
        $this->resource = $resource;
    }

    /**
     * Get status code of resource
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->resource->getStatusCode();
    }

    /**
     * Get parsed body of response
     *
     * @return Crawler | array | string
     */
    public function getBody()
    {
        return $this->parseBody();
    }

    /**
     * Parse body of response
     *
     * @return Crawler | array | string
     */
    private function parseBody()
    {
        $type = $this->getType();
        $body = (string)$this->resource->getBody();

        if (!$body) {
            return '';
        }

        if (in_array($type, ['text/html', 'text/xml'])) {
            return new Crawler($body);
        }

        if ($type === 'application/json') {
            return $this->resource->json();
        }

        return $body;
    }

    /**
     * Get type of response
     *
     * @return string
     */
    public function getType()
    {
        $type = $this->getHeader('Content-Type');

        return $type[0][0];
    }

    /**
     * Get header
     *
     * @param $header    string    header's name
     * @return array
     */
    public function getHeader($header)
    {
        return Request::parseHeader($this->resource, $header);
    }

    /**
     * Checks if request was redirected
     *
     * @return boolean
     */
    public function isRedirected()
    {
        return mb_strtolower($this->getURI()) !== mb_strtolower($this->getURI(true));
    }

    /**
     * Get URI
     *
     * @param $real    boolean   if method should return real URI, otherwise it returns requested URI
     * @return string
     */
    public function getURI($real = false)
    {
        if ($real) {
            return $this->resource->getEffectiveURL();
        }

        return $this->URI;
    }
}
