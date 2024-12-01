<?php

## ############################################################### ##
##  ----------------- Cherry RESTful API Handler ----------------  ##
##                                                                 ##
##  @package     Cherry_RESTful_API_Handler                        ##
##  @author      Marco Fernandez                                   ##
##  @link        marcofdz.com / glitcher.dev / inventtoo.com       ##
##  @link        http://github.com/fdz-marco                       ##
##  @version     0.0.2 (2024.12.01)                                ##
##  @license     http://opensource.org/licenses/MIT                ##
##  @copyright   2024 marcofdz.com / glitcher.dev / inventtoo.com  ##
##                                                                 ##
## ############################################################### ##

class CherryRESTfulAPI {
    private static $_url, $_requestMethod, $_endpoint, $_input;
    private static $_routes = [];
    private static $_authToken = "YOUR_SECRET_API_KEY"; 

    /*** 
	=========================================================
	Initializating 
	========================================================= 
	***/

    // Initalize the RESTful API Handler.
    public static function init() {
        // Set the HTTP Request method
        self::$_requestMethod = $_SERVER['REQUEST_METHOD'];
        // Parse the URL to get the Endpoint
        $url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        self::$_endpoint = explode('/', $url);
        // Get input data
        self::$_input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    }

    /*** 
	=========================================================
	Routing 
	========================================================= 
	***/

    // Add a route for specific HTTP Request Method and path.
    public static function addRoute($request_method, $path, $handler, $requiresAuth = false) {
        self::$_routes[] = [
            'request_method' => strtoupper($request_method),
            'path' => trim($path, '/'),
            'handler' => $handler,
            'requiresAuth' => $requiresAuth
        ];
    }

    // Replace placeholders like {id} with regex to match variables.
    private static function buildRouteRegex($path) {
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $path);
        return "#^$regex$#";
    }

    /*** 
	=========================================================
	Authentication 
	========================================================= 
	***/

    // Check for the presence of an Authorization header.
    private static function isAuthenticated() {
        $headers = getallheaders();
        return isset($headers['Authorization']) && $headers['Authorization'] === "Bearer " . self::$_authToken;
    }

    // Set an API KEY.
    public static function setAuthToken($token){
        self::$_authToken = $token;
    }

    /*** 
	=========================================================
	Proces Request / Response 
	========================================================= 
	***/

    // Process HTTP Request.
    public static function processRequest() {
        foreach (self::$_routes as $route) {
            if ($route['request_method'] === self::$_requestMethod &&
                preg_match(self::buildRouteRegex($route['path']), implode('/', self::$_endpoint), $matches)) {
                // Remove full match and pass route parameters to the handler
                array_shift($matches);
                // Check if the route requires authentication
                if ($route['requiresAuth'] && !self::isAuthenticated()) {
                    self::sendResponse(401, ['error' => 'Unauthorized']);
                    return;
                }
                $response = call_user_func_array($route['handler'], $matches);
                self::sendResponse(200, $response);
                return;
            }
        }
        // Route not found
        self::sendResponse(404, ['error' => 'Endpoint not found']);
    }

    // Send HTTP Response.
    private static function sendResponse($statusCode, $response) {
        header("Content-Type: application/json");
        http_response_code($statusCode);
        echo json_encode($response);
        exit;
    }
}

?>