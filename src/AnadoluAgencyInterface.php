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

interface AnadoluAgencyInterface
{
    /**
     * Add headers information for request.
     *
	   * @param  string  $key
     * @param  string  $value
     * @return \Buki\AnadoluAgency
     */
    public function addHeader($key, $value);
}
