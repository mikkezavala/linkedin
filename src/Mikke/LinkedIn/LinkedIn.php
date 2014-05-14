<?php namespace Mikke\LinkedIn;
class LinkedIn{
	/**
	* The Application APi URL.
	*
	* @var string
	*/
	protected $api_url = 'https://api.linkedin.com/';
	/**
	* The API Scope.
	*
	* @var string
	*/
	protected $api_scope = array('r_fullprofile r_emailaddress r_network r_contactinfo rw_nus');
	/*
	* Fields
	*/
	protected $api_fields = array(
		'id','email-address','picture-url','picture-urls::(original)', 'public-profile-url', 'first-name', 'last-name', 
		'date-of-birth','headline','location:(country:(code))', 'positions','skills',
		'industry', 'educations' 
	);
	/**
	* The Application APi URL.
	*
	* @var string
	*/
	protected $api_callback = '';
	/**
	* The Application APi Method.
	*
	* @var string
	*/
	protected $api_method = 'POST';	
	/**
	* The Application Current Endpoint.
	*
	* @var string
	*/
	protected $api_endpoint = '/';
	/**
	* The Application Current Request URL.
	*
	* @var string
	*/
	protected $api_request_url = '';
	/**
	* The Application Api Key.
	*
	* @var string
	*/
	protected $api_key = '';
	/**
	* The Application Api Secret.
	*
	* @var string
	*/
	protected $api_secret = '';
	/**
	* The Application Access Token.
	*
	* @var string
	*/
	protected $api_token = '';
	/**
	* The Application Access Token Secret.
	*
	* @var string
	*/
	protected $api_token_secret = '';	
	/**
	* The Request Header.
	*
	* @var mixed
	*/
	protected $api_headers = array();
	/**
	* The Api Body.
	*
	* @var mixed
	*/
	protected $api_body = array();
	
		
	/**
	* The Setting up with user credentials.
	*
	* @var array
	*/	
	public function __construct($setup = array()){
		if(is_array($setup) && sizeof($setup) > 0){
			$this->api_key = array_get($setup, 'api_key');
			$this->api_secret = array_get($setup, 'api_secret');
			$this->api_token = array_get($setup, 'api_token');
			$this->api_token_secret = array_get($setup, 'api_token_secret');
			$this->api_callback = array_get($setup, 'api_callback');
			
			$this->api_url = array_get($setup, 'api_url', $this->api_url);
			$this->time_stamp = time();
		}else{
			throw new \Exception('Incomplete Settings');
		}
	}
	public function authorize(){
		$params = array(
			'client_id'		=> $this->api_key,
			'response_type' => 'code',
			'scope'			=> $this->api_scope,
			'state' 		=> csrf_token(),
			'redirect_uri'	=> $this->api_callback
		);
		
		$this->api_endpoint = 'https://www.linkedin.com/uas/oauth2/authorization?';
		$this->api_request_url = $this->api_endpoint.http_build_query($params, '', '&', PHP_QUERY_RFC3986);
		
		//Clean Buffers... its healthy
		if(ob_get_length()){ ob_clean();}
		header('Location: '.$this->api_request_url);
		exit;
	}
	/*! 
	* Usually you want to save this credentials!
	* use this Tokens response to make call to LI API
	* This already saves it for this instance to call the services 
	* in this wrapper
	*/
	public function access_token($code = ''){			
		$params = array(
			'grant_type'		=> 'authorization_code',
			'client_id'			=> $this->api_key,
			'client_secret'		=> $this->api_secret,							
			'code'				=> $code,
			'redirect_uri'		=> $this->api_callback
		);
		$this->api_endpoint = 'https://www.linkedin.com/uas/oauth2/accessToken?';
		$this->api_request_url = $this->api_endpoint.http_build_query($params, '', '&', PHP_QUERY_RFC3986);
				
		$tokens = json_decode($this->callService());
		if(is_object($tokens) && sizeof($tokens) > 0){			
			$this->api_token = $tokens->access_token;
		}
		return ($this->api_token);
	}
	public function get_user(){
		if(empty($this->api_token)){ $this->authorize(); }	
		$params = array(
			'format' => 'json',
			'oauth2_access_token' => $this->api_token,
		);
		
		$this->api_endpoint = 'https://api.linkedin.com/v1/people/~:(' . implode(',', $this->api_fields) . ')?';
		$this->api_request_url	= $this->api_endpoint.http_build_query($params, '', '&', PHP_QUERY_RFC3986);
		
		$this->api_method = 'GET';
		
		$user_data = json_decode($this->callService());
		return ($user_data);
	}
	/*!
	 * Call to the server
	 *
	 * @param  array  $parameters
	 * @return mixed
	 */
	private function callService($params = array()){
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $this->api_request_url);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->api_headers);
		
		if($this->api_method == 'POST'){
			curl_setopt($ch, CURLOPT_POST, true);
		}else{
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

		}
		if($this->api_body != ''){
			curl_setopt($ch,CURLOPT_POSTFIELDS, $this->api_body);
		}
		
		$res = curl_exec($ch);		
		
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($res, 0, $header_size);
		$body = substr($res, $header_size);

		
		curl_close($ch);
		
		return($body);
	}
}