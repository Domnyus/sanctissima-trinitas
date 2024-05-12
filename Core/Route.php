<?php

namespace Domnyus;

class Route {
    protected ?array $get;
    protected ?array $post;
    protected ?array $put;
    protected ?array $patch;
    protected ?array $delete;
    protected ?array $options;
    protected ?array $head;
    //protected ?array $trace;
    protected ?array $data;
    protected ?bool $success;
    protected ?string $message;
    protected mixed $return;
    protected ?array $errors;
    protected ?int $status_code;
    public function __construct()
    {
        $this->get = [
            "get" => "get"
        ];
        $this->post = [
            "post"=> "post"
        ];
        $this->put = [
            "put"=> "put"
        ];
        $this->patch = [
            "patch"=> "patch"
        ];
        $this->delete = [
            "delete"=> "delete"
        ];
        $this->options = [
            "options"=> "options"
        ];
        $this->head = [
            "head"=> "head"
        ];
        /*$this->trace = [
            "trace"=> "trace"
        ];*/
    }

    public function call (string $http_method, string $route_name)
    {
        if (!isset($this->{strtolower($http_method)})) {
            throw new \Exception("This HTTP method does not exists", Constants::BAD_REQUEST);
        }

        if (strtolower($route_name) === "/") {
            $route_name = strtolower($http_method);
        }

        if (!isset($this->{strtolower($http_method)}[strtolower($route_name)])) {
            throw new \Exception("This Route does not exists", Constants::BAD_REQUEST);
        }

        $method_name = $this->{strtolower($http_method)}[strtolower($route_name)];

        if (!method_exists($this, $method_name)) {
            throw new \Exception("This Method does not exists", Constants::BAD_REQUEST);
        }

        $this->config_data();

        $this->{$method_name}();

        $return = [
            "success" => $this->success,
            "message" => $this->message,
            "return" => $this->return,
            "errors" => $this->errors,
            "status_code" => $this->status_code,
        ];

        return array_filter($return, function ($key) {
            return isset($key);
        });
    }

    private function config_data () : void
    {
        $this->data = array_merge($_GET, $_POST);
        if (!empty(file_get_contents("php://input")) && !empty(json_decode(file_get_contents("php://input")))) {
            $this->data = array_merge($this->data, json_decode(file_get_contents("php://input"), true));
        }

        foreach ($this->data as $key => $value) {
            $this->data[$key] = htmlspecialchars($value);
        }
    }

    protected function validate_data (array $validation_array) : bool
    {
        $exceptions = [];
        $validation = (new Validation());
        foreach ($validation_array as $value) {
            if (!array_key_exists("validation", $value) || empty($value["validation"])) {
                throw new \Exception("The validation must have either a callable or native function name from Validation class!", Constants::INTERNAL_SERVER_ERROR);
            }
            
            if (!array_key_exists("key", $value)) {
                throw new \Exception("The validation must have a key!", Constants::INTERNAL_SERVER_ERROR);
            }

            if (!array_key_exists($value["key"], $this->data)) {
                throw new \Exception("This key {$value["key"]} is missing!", Constants::INTERNAL_SERVER_ERROR);
            }
            
            if (is_callable($value["validation"]) && gettype($value["validation"]) === "object") {
                $value["validation"]($this->data[$value["key"]]);
            } else {

                if (is_array($value["validation"])) {
                    foreach ($value["validation"] as $validation_key) {
                        $pattern = '/\((.*?)\)/';
                        preg_match($pattern, $validation_key, $extras);

                        $validation_name = strtok($validation_key, "(");
                        if (!method_exists($validation, $validation_name)) {
                            throw new \Exception("This validation does not exists!", Constants::INTERNAL_SERVER_ERROR);
                        }
                        $result = $validation->{$validation_name}($this->data[$value["key"]], $value["key"], $value["label"] ?? null, $value["message"] ?? null, $extras ?? null);
                        if (!is_bool($result)) {
                            $exceptions[] = $result;
                        }
                    }
                } else {
                    $pattern = '/\((.*?)\)/';
                    preg_match($pattern, $value["validation"], $extras);

                    $validation_name = strtok($value["validation"], "(");
                    if (!method_exists($validation, $validation_name)) {
                        throw new \Exception("This validation does not exists!", Constants::INTERNAL_SERVER_ERROR);
                    }
                    $result = $validation->{$validation_name}($this->data[$value["key"]], $value["key"], $value["label"] ?? null, $value["message"] ?? null, $extras ?? null);
                    if (!is_bool($result)) {
                        $exceptions[] = $result;
                    }
                }
            }
        }
        if (count($exceptions) > 0) {
            foreach ($exceptions as $exception) {
                $this->errors[] = $exception->getMessage();
            }
            return false;
        }
        return true;
    }

    protected function add_route (string $http_method, string $route_name, $method = null) : void
    {
        if (!isset($this->{strtolower($http_method)})) {
            throw new \Exception("This HTTP method does not exists", Constants::BAD_REQUEST);
        }

        $this->{strtolower($http_method)}[strtolower($route_name)] = isset($method) ? strtolower($method) : strtolower($route_name);
    }

    protected function set_route_response (
        ?string $message = null,
        mixed $return = null,
        ?array $errors = null,
        ?int $status_code = Constants::SUCCESS,
        ?bool $success = true
    ) : Route {
        $this->message = $message;
        $this->return = $return;
        $this->errors = $errors;
        $this->status_code = $status_code;
        $this->success = $success;
        return $this;
    }

    public function options() : void
    {
        $this->set_route_response("OPTIONS", ["GET","POST","PUT","PATCH","DELETE","OPTIONS","HEAD"]);
    }

    public function head() : void
    {
        $this->set_route_response();
    }
}