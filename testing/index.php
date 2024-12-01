<?php

require_once("../Cherry_RESTful_API_Handler.php");

// Initialize
CherryRESTfulAPI::init();
CherryRESTfulAPI::setAuthToken('YOUR_SECRET_API_KEY');

// Add Routes
CherryRESTfulAPI::addRoute('GET', '/users', function () {
    return ['message' => 'List of users'];
}, true); // Requires authentication
CherryRESTfulAPI::addRoute('GET', '/users/{id}', function ($id) {
    return ['message' => 'User details', 'id' => $id];
});
CherryRESTfulAPI::addRoute('GET', '/users/{id}/{id2}', function ($id, $id2) {
    return ['message' => 'User details', 'id' => $id, 'id2' => $id2];
});
CherryRESTfulAPI::addRoute('POST', '/users', function () {
    return ['message' => 'User created'];
});
CherryRESTfulAPI::addRoute('PUT', '/users/{id}', function ($id) {
    return ['message' => 'User updated', 'id' => $id];
}, true); // Requires authentication
CherryRESTfulAPI::addRoute('DELETE', '/users/{id}', function ($id) {
    return ['message' => 'User deleted', 'id' => $id];
}, true); // Requires authentication

// Process Request
CherryRESTfulAPI::processRequest();

?>