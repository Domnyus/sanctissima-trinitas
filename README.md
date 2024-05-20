# sanctissima-trinitas V 0.3
Light PHP API Framework

# Table SQL from exemple
Check SQL file

# Paths
By default paths use :variable to set path variables, but you can change in Path class the character in strstr with ":".

# API Routing
Paths should start with '/' e.g.: "/login"
Method is the HTTP Method
Route is the function to be called in Route file
File is the class name exteding Route
In Route constructor call $this->add_route(HTTP method name, route name, optional method name if the method to be called has a diferrent name from route name) to set your list of methods (how about to use API_ROUTES in foreach to add them?)
Use $this->set_response to send a proper response to the request which parameters are: message is a generic message, return is an array of data built for response, errors is an array of errors of validation or whatever is wrong, status_code by default is 200 (success) and a success is a boolean for fast checkd, so you have a universal checker that you has 100% that'll be there.

# API Validation
Call $this->validate_date with array of arrays, each array has "key" and "validation" keys:
Key: refer to data ti be validated
Validation: method in Validation class, can be an array of method names
Label: front-end response
Message: front-end response

# Models
Extend model sending on constructor an basename in lowercase to parent
Set your custom parser to hide or format the data you want to send
By default model has a native crud built with  PDO

# Controller
Controllers are the views' bridge.
Extend Controller with methods that will "include $this->view"

# View
Views will have 
uri: first parte of url
path: whatever after uri
class: name of controller
method: name of method in controller
view: file path that can be concatenated with Constants::VIEWS . file.php
