<?php
/**
 * Custom overrides and extensions to the HttpRequest compontent
 *
 * @property string $JsonpCallback
 * @property boolean $isJsonpRequest
 */
class LWHttpRequest extends CHttpRequest {

	/**
	 * The callback parameter when performing a jsonp request.
	 * @var string
	 */
	const JSONP_CALLBACK = 'callback';

	/**
	 * Fill this will all the development tld's. Use this to identify if the
	 * current request is running on a dev site, or live site.
	 * @var array
	 */
	public $developmentTlds = array();

	/**
	 * Is this a jsonp request?
	 * @return boolean
	 */
	public function getIsJsonpRequest() {
		$jsonpCallback = $this->getParam(self::JSONP_CALLBACK);
		return isset($jsonpCallback);
	}

	/**
	 * Get the value of the jsonp callback parameter.
	 * @return string
	 */
	public function getJsonpCallback() {
		return $this->getParam(self::JSONP_CALLBACK);
	}

	/**
	 * Get the tld of the current request, e.g. com, local.
	 * @return string
	 */
	public function getTld()
	{
		$parts = pathinfo($this->hostInfo);
		if(isset($parts['extension'])) {
			return $parts['extension'];
		}
		return 'com';
	}

	/**
	 * Is this request from a development domain, or live domain?
	 * @return boolean
	 */
	public function getIsDevelopment()
	{
		return in_array($this->tld, $this->developmentTlds);
	}

	public function getRealIpAddress()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
		  $ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))	//to check ip is pass from proxy
		{
		  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
		  $ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}

?>
