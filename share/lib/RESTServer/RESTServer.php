<?php

namespace WSWG;

require_once('HTTPCodes.php');

class RESTServer { 

  // Request strings
  private $base_uri;
  
  // Request
  public $method;
  public $uri;
  public $query;
  public $params;
  public $contentType;

  // Registered pattern handlers
  private $handlers = array();

  // Registered content types
  private $contentTypes = array();

  // Registered content types
  private $methods = array();

  // Cache Control state
  private $cacheControl = null;

  /**
   * Instantiate a RESTServer
   *
   * @param string $baseuri URI base for web service, not including the 
   *  domain name (e.g. '/services/unitregistry')
   *
   */
  public function __construct($baseuri='/') { 
    $this->base_uri = $baseuri;
  }


  /**
   * Register a URL pattern to a handler
   *
   * @param string $method HTTP request method
   * @param string $pattern PCRE pattern
   * @param mixed $function function reference or name (string) to call
   *
   */
  public function registerHandler($method, $pattern, $function) {
    $this->handlers[$method][] = array ('pattern'  => $pattern,
					'function' => $function);
  }


  /**
   * Registers a content type that can be processed by the server
   *
   * 
   * @param string $contentType: A content type
   */
  public function registerContentType($contentType) {
    $this->contentTypes[] = $contentType;
  }


  /**
   * Register an allowable method for this server
   *
   * @param string $method name of the allowed method
   */
  public function registerMethod($method) { 
    $this->methods[] = $method;
  }

  /**
   * Set cache control
   *
   * @param string $cache cache control state to set
   */
  public function cacheControl($cache) { 
    $this->cacheControl = $cache;
  }

  /**
   * Process a request using the current $_SERVER variables
   *
   */
  public function processCurrentRequest() {
    // Determine method and parameter array
    $request = urldecode($_SERVER['REQUEST_URI']);
    $query = urldecode($_SERVER['QUERY_STRING']);
    $method = $_SERVER['REQUEST_METHOD'];
    $params = $_REQUEST;
    $body = ($method == 'POST' || $method == 'PUT') ? file_get_contents('php://input') : null;

    // Make sure the request is an allowed HTTP method
    if (!$this->isAllowed($method))
      $this->dieRespond(METHOD_NOT_ALLOWED, "Allowed methods:<br />".implode('<br />', $this->methods));

    // Make sure the request has a valid Accept header
    if (!isset($_SERVER['HTTP_ACCEPT']))
      $this->dieRespond(NOT_ACCEPTABLE, "Supported Accept headers:<br />".implode('<br />', $this->contentTypes));

    // Determine best acceptable content type
    if (!$this->contentType = $this->getAcceptableContent($_SERVER['HTTP_ACCEPT']))
      $this->dieRespond(NOT_ACCEPTABLE, "Supported Accept headers:<br />".implode('<br />', $this->contentTypes));

    // Parse resource
    $qpos = strlen($query) == 0 ? strlen($request) : strpos($request, $query);
    $uri = substr($request, strlen($this->base_uri), $qpos - strlen($this->base_uri));
    $uri = rtrim($uri, '/?');

    // Set member data
    $this->method = $method;
    $this->uri = $uri;
    $this->query = $query;
    $this->params = $params;
    $this->body = $body;

    // Select callback function
    list($function, $args) = $this->findHandler($method, $uri);

    // Call function
    $response = call_user_func_array($function, array($this, $args));

    if ($response !== false) {
      $this->sendHeaders(OKAY);
      echo $response;
    }
  }


  /**
   * Process a request using custom variables
   *
   * @param string $accept HTTP accept header to use for this request
   * @param string $method HTTP method to use for this request
   * @param string $uri relative URI for this request, without parameters
   * @param string $query query string
   * @param array $params associative array of parameter name/values
   */
  public function processCustomRequest($accept, $method, $uri, $query, $params) { 
    // NYI
  }


  /**
   * Returns the highest priority registered content type that is also
   *  acceptable by the client
   *
   * @param string $accept Accept string from the client request
   *
   */
  public function getAcceptableContent($accept) {
    // Sort acceptable headers by precedence
    $acceptable = preg_split('/\s*,\s*/', $accept);
    usort($acceptable, function($a, $b) {
	return RESTServer::getMediaPrecedence($a) - RESTServer::getMediaPrecedence($b);
      });

    // Get intersection between acceptable and registered types
    foreach ($acceptable as $a) {
      foreach ($this->contentTypes as $b) {
	list($type, $subtype) = explode('/', $b);
	if (preg_match('/(\*|'.$type.')\/(\*|'.$subtype.')/', $a)) return $b;
      }
    }

    return null;
  }

  /**
   * Returns the precedence of a media type specifier
   *
   * @param string $accept Media type string
   *
   */
  public function getMediaPrecedence($accept) {
    // Separate media type and parameters
    $terms = preg_split('/;\s*/', $accept);
    $type = array_shift($terms);

    // Determine general level of precedence
    if (preg_match('/^[^*]+\/[^*]+$/', $type)) return 0;	// Type and subtype, highest precedence
    if (preg_match('/^[^*]+\/\*$/', $type)) return 1;		// Type only, medium precedence
    if (preg_match('/^\*\/\*$/', $type)) return 2;		// All types, lowest precedence
  }

  /**
   * Find appropriate handler for a request using specific method
   *
   * @param string $method HTTP request method used
   * @return Array [string <functionName>, Array <matchedArguments>] if match is found, otherwise [null, null]
   */
  public function findHandler($method, $uri) {
    $fname = null;
    $fargs = array();

    foreach ($this->handlers[$method] as $handler) { 
      $matches = array();
      preg_match($handler['pattern'], $uri, $matches);

      if (!empty($matches))
	return array($handler['function'], array_slice($matches, 1));
    }    
    return array($fname, $fargs);
  }


  /**
   * Send headers with response code and a message and die
   *
   * @param string $resCode HTTP response code and message
   * @param string $message informative message for client
   *
   */
  public function sendHeaders($resCode = OKAY) { 
    // Content type
    header("Content-type: {$this->contentType}");
	
    // HTTP response code
    header($resCode);

    // Allowed request methods
    header('Allow: '.implode(', ', $this->methods));

    // Cache control
    if (isset($this->cacheControl))	header('Cache-Control: '.$this->cacheControl);
  }


  /**
   * Check to see if the supplied method is allowable
   *
   * @param string $method HTTP method to check
   *
   */
  public function isAllowed($method) { 
    return in_array($method, $this->methods);
  }


  /**
   * Send headers with response code and a message and die
   *
   * @param string $resCode HTTP response code and message
   * @param string $message informative message for client
   *
   */
  public function dieRespond($resCode, $message='') { 
    $this->sendHeaders($resCode);
    echo $message;
    die();
  }
}