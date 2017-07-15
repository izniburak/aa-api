<?php
/**
* Anadolu Agency (Anadolu Ajansı) API Client
*
* @author   izni burak demirtaş (@izniburak) <izniburak@gmail.com>
* @web      <http://burakdemirtas.org>
* @url      <https://github.com/izniburak/aa-api>
* @license  The MIT License (MIT) - <http://opensource.org/licenses/MIT>
*/

namespace Buki;

use GuzzleHttp\Client;
use Exception;

class AnadoluAgency implements AnadoluAgencyInterface
{
	/**
   * The client object.
   *
   * @var \GuzzleHttp\Client
   */
	protected $client = null;

	/**
   * Auth information.
   *
   * @var array
   */
	protected $auth = [];

	/**
   * Headers information.
   *
   * @var null
   */
	protected $headers = null;

	/**
   * Response object.
   *
   * @var \GuzzleHttp\Psr7\Response
   */
	protected $response = null;

	/**
   * Files attributes in Response.
   *
   * @var null
   */
	protected $attr = null;

	/**
   * Create a new instance.
   *
   * @param  string  $user
   * @param  string  $pass
	 * @param  float|int  $timeout
   * @return  void
   */
	public function __construct($user, $pass, $timeout = 3.0)
	{
		$this->client = new Client([
			// Base URI is used with relative requests
			'base_uri' => 'https://api.aa.com.tr',
			// You can set any number of default request options.
			'timeout'  => $timeout,
		]);

		$this->auth = [$user, $pass];
	}

	/**
   * Add headers information for request.
   *
	 * @param  string  $key
   * @param  string  $value
   * @return  \Buki\AnadoluAgency
   */
	public function addHeader($key, $value = null)
	{
		if(is_array($key))
			foreach($key as $k => $v)
				$this->headers[$k] = $v;
		else
			$this->headers[$key] = $value;

		return $this;
	}

	/**
   * Add time interval information in request header.
   *
	 * @param  string  $start
	 * @param  string  $end
   * @return  \Buki\AnadoluAgency
   */
	public function time($start = '*', $end = "NOW")
	{
		if($start != '*')
			$start = $this->timeFormat($start);
		if($end != "NOW")
			$end = $this->timeFormat($end);

		$this->addHeader("start_date", $start);
		$this->addHeader("end_date", $end);

		return $this;
	}

	/**
   * Add filter informations in request header.
   *
	 * @param  string  $type
	 *     provider, category, priorty, package,
	 *     type, language, search
	 * @param  array  $value
   * @return  \Buki\AnadoluAgency
   */
	public function filter($type, array $value)
	{
		$value = implode(',', $value);
		if($type == "search")
			$this->addHeader("search_string", $value);
		else
			$this->addHeader("filter_" . $type, $value);

		return $this;
	}

	/**
   * Add limit informations in header.
   *
	 * @param  int  $limit
	 * @param  int  $offset
   * @return  \Buki\AnadoluAgency
   */
	public function limit($limit, $offset = null)
	{
		if(is_null($offset))
		{
			$this->addHeader("limit", $limit);
			$this->addHeader("offset", 0);
		}
		else
		{
			$this->addHeader("limit", $offset);
			$this->addHeader("offset", $limit);
		}

		return $this;
	}

	/**
   * API Discover request.
   *
	 * @param  string  $lang
   * @return  json|\GuzzleHttp\Psr7\Response
   */
	public function discover($lang = "tr_TR")
	{
		return $this->get("/abone/discover/" . $lang);
	}

	/**
   * API Search request.
   *
   * @return  json|\GuzzleHttp\Psr7\Response
   */
	public function search()
	{
		return $this->post("/abone/search/");
	}

	/**
	 * API Document request.
	 *
	 * @param  string  $id
	 * @param  string  $type
	 *     aa:text 	-> newsml29, newsml12
	 *     aa:picture -> print, web, mobile
	 *     aa:video	-> hd, sd, web, mobile
	 * @return  json|\GuzzleHttp\Psr7\Response
	 */
	public function document($id, $type = "newsml29")
	{
		return $this->get( ("/abone/document/" . $id . "/" . $type) );
	}

	/**
	 * Save the files in Response.
	 *
	 * @param  string  $path
	 * @return  json|void
	 */
	public function save($path = null)
	{
		if(is_null($this->attr))
			return;

		if(is_null($path))
			$path = getcwd();

		$target = $path . "/" . $this->attr["filename"];
		try
		{
			$file = fopen($target, 'w+');
			if(!$file)
				throw new Exception;

			fwrite($file, $this->attr["data"]);
			fclose($file);

			return json_encode(
				[ "response"	=> ["success" => true], "data" => ["file" => $target] ]
			);
		}
		catch(Exception $e)
		{
			return json_encode(
				[ "response"	=> ["success" => false] ]
			);
		}
	}

	/**
	 * Get response content.
	 *
	 * @return  string|void
	 */
	public function getContent()
	{
		if(!is_null($this->attr))
			return $this->attr["data"];
	}

	/*
	 * Get response object for request
	 *
	 * @return  \GuzzleHttp\Psr7\Response
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
   * Get request for API.
   *
   * @param  string  $url
   * @return  json|\GuzzleHttp\Psr7\Response
   */
	protected function get($url)
	{
		return $this->request("GET", $url);
	}

	/**
   * Post request for API.
   *
   * @param  string  $url
   * @return  json|\GuzzleHttp\Psr7\Response
   */
	protected function post($url)
	{
		return $this->request("POST", $url);
	}

	/**
   * API Request.
   *
	 * @param  string  $method
   * @param  string  $url
   * @return  json|\GuzzleHttp\Psr7\Response
   */
	protected function request($method, $url)
	{
		$this->response = $this->client->request($method, $url, [
			'auth' => $this->auth,
			'headers' => $this->headers
		]);
		$this->headers = null;
		$this->attr = null;

		if(stripos($this->response->getHeader("Content-Type")[0], "application/json", 0) === 0)
			return $this->response->getBody()->getContents();

		elseif($this->response->hasHeader("Content-Disposition"))
		{
			$disposition = $this->response->getHeader("Content-Disposition")[0];
			$this->attr["filename"] = explode('=', $disposition)[1];
			$this->attr["data"] = (string) $this->response->getBody();

			return json_encode(
				[ "response" => ["success" => true], "data" => [ "file" => $this->attr["filename"] ] ]
			);
		}

		return $this->response;
	}

	/**
   * Time formatting.
   *
	 * @param  string  $time
   * @return  string
   */
	protected function timeFormat($time)
	{
		$timestamp = strtotime($time);
		return sprintf(
			"%sT%sZ",
			date("Y-m-d", $timestamp), date("H:i:s", $timestamp)
		);
	}
}
