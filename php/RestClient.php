<?php
namespace Jokes\One;

class RestClient {

  private $baseurl = "https://api.jokes.one";
  private $api_key = null;
  private $rate_limit_remaining = null;
  private $rate_limit_limit = null;

  function __construct($api_key=null)
  {
    $this->api_key = $api_key;
  }

  function joke_of_the_day($category=null,$lang="en")
  {
     $url  = $this->baseurl . "/jod";
     $data = null;
     if ( ! empty($category) )
     {
        $data = array( 'category' => $category, 'lang' => $lang);
     } 

     return self::call_rest_endpoint("GET",$url,$data);
  }

  function joke_of_the_day_categories($lang="en")
  {
     $url  = $this->baseurl . "/jod/categories";
     $data = array('lang' => $lang);

     return self::call_rest_endpoint("GET",$url,$data);
  }

  function random($lang="en")
  {
     $url  = $this->baseurl . "/joke/random";
     $data = array('lang' => $lang);
     return self::call_rest_endpoint("GET",$url,$data);
  }

  function rate_limit_limit()
  {
     if ( $this->rate_limit_limit == null )
        self::joke_of_the_day();

     return $this->rate_limit_limit;
  }

  function rate_limit_remaining()
  {
     if ( $this->rate_limit_remaining == null )
        self::joke_of_the_day();

     return $this->rate_limit_remaining ;
  }

  private function call_rest_endpoint ($method, $url, $data = null)
  {
    $curl = curl_init();
    switch ($method)
    {
	case "POST":
	    curl_setopt($curl, CURLOPT_POST, 1);

	    if (!empty($data))
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    break;
	case "PUT":
	    curl_setopt($curl, CURLOPT_PUT, 1);
	    break;
	default:
	    if (!empty($data))
		$url = sprintf("%s?%s", $url, http_build_query($data));
    }

    $headers = [
	'Content-Type: application/json'
	];
    if ( !empty($this->api_key))
	$headers[] = 'X-JokesOne-Api-Secret: '. $this->api_key;

    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $response_headers = [];
    curl_setopt($curl, CURLOPT_HEADERFUNCTION,
	    function($curl, $header) use (&$response_headers)
	    {
	      $len = strlen($header);
	      $header = explode(':', $header, 2);
	      if (count($header) < 2) // ignore invalid headers
	      return $len;

	      $response_headers[strtolower(trim($header[0]))][] = trim($header[1]);

	      return $len;
	    }
	);

    $result = curl_exec($curl);

    if ( $result === false )
    {
       $error = curl_error($curl);
       throw new Exception("Error talking to Jokes one service" . $error);
    }

    $this->rate_limit_remaining = $response_headers[strtolower('X-RateLimit-Remaining')][0] ;
    $this->rate_limit_limit = $response_headers[strtolower('X-RateLimit-Limit')][0] ;

    curl_close($curl);
    return json_decode($result,true);
  }

};

?>
