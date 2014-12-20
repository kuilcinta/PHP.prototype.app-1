<?php if ( !defined('BASEPATH')) header('Location:404');
/**
 * cURLs CLASS
 * Handling request data from external url/site domain
 * With 2 type method: _checkDomainURL() and _ProcessDataCurl()
 *
 * @since v1.0
 * @author Ofan Ebob
 */
class cURLs {
	protected $_url;
	protected $_type;
	protected $_args;

	public function __construct($arr)
	{
		$this->_url = isset($arr['url']) ? $arr['url'] : false;
		$this->_type = isset($arr['type']) ? $arr['type'] : false;
		$this->_args = isset($arr['args']) ? $arr['args'] : array();
	}

	/*
	 * access_curl public function
	 * Handling function call from other resource/function
	 */
	public function access_curl()
	{
		$type = $this->_type;

		if($type == false)
		{
			return false;
		}
		else{
			switch($type) {
				case 'domain':
					return $this->_checkDomainCURL();
				break;
				case 'data':
					return $this->_ProcessDataCurl();
				break;
				default:
					return $this->_ProcessDataCurl();
				break;
			}
		}
	}

	/*
	 * _chcekDOmainCURL()
	 * getting information for domain exist from url defined
	 * return true/false
	 */
	protected function _checkDomainCURL()
	{
		$url = $this->_url;
		$args = $this->_args;
		$timeout_con = isset($args['timeout_con']) ? $args['timeout_con'] : 5;
		$timeout_res = isset($args['timeout_res']) ? $args['timeout_res'] : 10;
		$max_redir = isset($args['max_redir']) ? $args['max_redir'] : 3;

		if($url == false)
		{
			return false;
		}
		else
		{
			if(extension_loaded('curl'))
			{
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_TIMEOUT, $timeout_res);
				curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout_con);
				curl_setopt($curl, CURLOPT_MAXREDIRS, $max_redir);
				curl_setopt($curl, CURLOPT_HEADER, true);
				curl_setopt($curl, CURLOPT_NOBODY, true);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

				if(isset($args['uAgent']))
				{
					curl_setopt($curl, CURLOPT_USERAGENT, $args['uAgent']); // who am i
				}
				
				$result = curl_exec($curl);

				if($result !== false)
				{

					$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); 

					if( in_array($statusCode, range(200,306)) )
					{
						return false;
					}
					else{
						return true;
					}
				}
				else{
					return false;
				}

				curl_close($curl);
			}
			else
			{
				return false;
			}
		}
	}

	/*
	 * _ProcessDataCurl()
	 * grab data json/txt/html or basic text from url defition
	 * return $result (data) or null
	 */
	protected function _ProcessDataCurl()
	{
		$url = $this->_url;
		$args = $this->_args;
		$encod = isset($args['encod']) ? $args['encod'] : "gzip";
		$timeout_con = isset($args['timeout_con']) ? $args['timeout_con'] : 5;
		$timeout_res = isset($args['timeout_res']) ? $args['timeout_res'] : 10;
		$max_redir = isset($args['max_redir']) ? $args['max_redir'] : 3;

		if($url == false)
		{
			return null;
		}
		else
		{
			if(extension_loaded('curl'))
			{
				$options = array(
					CURLOPT_RETURNTRANSFER => true, // return web page
					CURLOPT_HEADER => false, // don't return headers
					CURLOPT_FOLLOWLOCATION => true, // follow redirects
					CURLOPT_ENCODING => $encod, // handle all encodings
					CURLOPT_AUTOREFERER => true, // set referer on redirect
					CURLOPT_CONNECTTIMEOUT => $timeout_con, // timeout on connect
					CURLOPT_TIMEOUT => $timeout_res, // timeout on response
					CURLOPT_MAXREDIRS => $max_redir, // stop after 10 redirects
					CURLOPT_SSL_VERIFYHOST => 0, // disable SSL verification host
					CURLOPT_SSL_VERIFYPEER => false, // skip SSL verifier
				);

				$curl = curl_init();
				
				if(isset($args['uAgent']))
				{
					$options[CURLOPT_USERAGENT] = $args['uAgent']; // who am i
				}

				if(isset($args['refer']))
				{
					$options[CURLOPT_REFERER] = $args['refer']; // detect domain come from request
				}

				if(isset($args['auth']))
				{
					$options[CURLOPT_USERPWD] = $args['auth']; // detect domain come from request
					$options[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC; // set HTTP authorization
				}

				$options[CURLOPT_URL] = $url;  
				curl_setopt_array($curl, $options);

				$result = curl_exec($curl);

				if($result !== false)
				{

					$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); 

					if( in_array($statusCode, range(200,306)) )
					{
						return $result;
					}
					else{
						return null;
					}
				}
				else
				{
					return null;
				}
				curl_close($curl);
			}
			else
			{
				return null;
			}
		}
	}
}
?>