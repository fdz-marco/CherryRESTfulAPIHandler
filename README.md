### Welcome to Cherry RESTful API Handler

# What is this?
![Cherry RESTful API Handler](https://raw.github.com/fdz-marco/CherryRESTfulAPI/master/design/CherryRESTfulAPIHandler_mini.png "Cherry RESTful API") 

*Cherry RESTful API Handler* is a small class to create RESTful APIs in PHP.

This project have a MIT License, so you can modify it, redistribute it, print it or whatever you want.

# Configuration

## How to setup Cherry RESTful API Handler?

To implement Cherry RESTful API Handler you only need to load and call the class directly using the static functions of the class:
```php
<?php
// Load Class
require_once("Cherry_RESTful_API_Handler.php");

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
```

# Documentation

## Functions

| Function Name 	                                                            | Access Type | Description 	    								            |
| ------------------------------------------------------------------------------| --------- | ----------------------------------------------------------------- |
| **->init()** 		                                                            | public    | Initalize the RESTful API Handler. 	                            |
| **->addRoute($request_method, $path, $handler, $requiresAuth = false)** 		| public    | Add a route for specific HTTP Request Method and path.            |
| **->bildRouteRegex($path)** 		                                            | private   | Replace placeholders like {id} with regex to match variables.     |
| **->isAuthenticated()** 		                                                | private   | Check for the presence of an Authorization header.	            |
| **->setAuthToken($token)** 	                                            	| public    | Set an API KEY.                                                   |
| **->processRequest()** 		                                                | public    | Process HTTP Request.                                             |
| **->sendResponse($statusCode, $response)** 	                                | private 	| Send HTTP Response.	                                            |

## Testing Authentication

### Without Authentication
```bash
curl -X GET http://yourdomain.com/users
```
Output:
```json
{
    "error": "Unauthorized"
}
```


### With Authentication
```bash
curl -X GET http://yourdomain.com/users -H "Authorization: Bearer YOUR_SECRET_API_KEY"
```
Output:
```json
{
    "message": "List of users"
}
```



## Server Configurations

### Apache Server

If API is located in root folder:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    # Redirect all requests to index.php if the requested file or directory doesn't exist
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

If API is located in child folder, for example /api:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /api/

    # Redirect all requests to index.php if the requested file or directory doesn't exist
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

### Nginx Server

If API is located in root folder:
```nginx
server {
    listen 80;
    server_name yourdomain.com;

    root /path/to/your/project;
    index index.php;

    location / {
        try_files $uri /index.php;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock; # Adjust for your PHP version
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

If API is located in child folder, for example /api:
```nginx
server {
    listen 80;
    server_name yourdomain.com;

    root /path/to/your/project;
    index index.php;

    location /api/ {
        try_files $uri /api/index.php;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock; # Adjust for your PHP version
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```