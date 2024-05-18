<?php

namespace Domnyus\Routes;
use Domnyus\Route;
use Domnyus\Constants;
use Domnyus\Models\Login;
use Domnyus\Models\API_ROUTES;
use Exception;

class _Login extends Route {
    public function __construct()
    {
        parent::__construct();
        $routes = (new API_ROUTES())->select([
            "file" => basename(__CLASS__)
        ]);
        /**
         * @var API_ROUTES $route
         */
        foreach ($routes as $route) {
            $this->add_route($route->get_method() ?? "GET", $route->get_route());
        }
    }

    public function get() : void
    {
        $login = new Login();

        if (isset($this->data["id"])) {
            $data = $login->find_by_id($this->data["id"])->parser();
            if (empty($login->get_id())) {
                throw new Exception("Login not found!", Constants::BAD_REQUEST);
            }
        } else {
            $data = $login->select(use_parser: true, limit: $this->page_size, offset: $this->page_index * $this->page_size);
            $this->page_rows = count($data);
            $this->total_rows = $login->___get_total_count();
        }

        $this->set_route_response(return: $data);
    }

    public function exemple() : void
    {
        $login = new Login();

        if (isset($this->data["id"])) {
            $data = $login->find_by_id($this->data["id"])->parser();
            if (empty($login->get_id())) {
                throw new Exception("Login not found!", Constants::BAD_REQUEST);
            }
        } else {
            $data = $login->select(use_parser: true, limit: $this->page_size, offset: $this->page_index * $this->page_size);
            $this->page_rows = count($data);
            $this->total_rows = $login->___get_total_count();
        }

        $this->set_route_response(return: $data);
    }

    public function post() : void
    {
        $this->validate_data([
            [
                "key" => "username",
                "validation" => "must_be_size_of(>=6)"
            ],
            [
                "key" => "password",
                "validation" => "must_be_not_empty"
            ]
        ]);

        $login = (new Login())
            ->set_username($this->data["username"])
            ->set_password(md5($this->data["password"]))
            ->insert();

        $this->set_route_response("Login successfully created!", [
            "id" => $login->get_id()
        ]);
    }

    public function put() : void
    {
        $this->validate_data([
            [
                "key" => "id",
                "validation" => "must_be_not_empty"
            ],
            [
                "key" => "username",
                "validation" => "must_be_size_of(>=6)"
            ],
            [
                "key" => "password",
                "validation" => "must_be_not_empty"
            ]
        ]);

        $login = (new Login())->find_by_id($this->data["id"]);

        if (empty($login->get_id()))
        {
            throw new Exception("Login not found!", Constants::BAD_REQUEST);
        }

        $login
            ->set_username($this->data["username"])
            ->set_password(md5($this->data["password"]))
            ->update();

        $this->set_route_response("Login successfully updated!");
    }

    public function patch() : void
    {
        if (count($this->data) > 2) {
            throw new Exception("Patch must be used to update only one data at time!", Constants::BAD_REQUEST);
        }

        $validations = [];

        $possible_validations = [
            [
                "key" => "id",
                "validation" => "must_be_not_empty"
            ],
            [
                "key" => "username",
                "validation" => "must_be_size_of(>=6)"
            ],
            [
                "key" => "password",
                "validation" => "must_be_not_empty"
            ]
        ];

        foreach ($this->data as $key => $value)
        {
            foreach ($possible_validations as $validation) {
                if ($validation["key"] == $key) {
                    $validations[] = $validation;
                }
            }
        }

        if (!$this->validate_data($validations)) {
            $this->set_route_response(errors: $this->errors, success: false, status_code: Constants::BAD_REQUEST);
        } else {
            $login = (new Login())->find_by_id($this->data["id"]);
    
            if (empty($login->get_id()))
            {
                throw new Exception("Login not found!", Constants::BAD_REQUEST);
            }
    
            $updated_key = "";
            foreach ($this->data as $key => $value)
            {
                foreach ($possible_validations as $validation) {
                    if ($validation["key"] === "password") {
                        if (!isset($this->data[$validation["key"]])) {
                            continue;
                        }
                        $login->{"set_".$validation["key"]}(md5($this->data[$validation["key"]]));
                        continue;
                    }
                    $login->{"set_".$validation["key"]}($this->data[$validation["key"]]);
                }
                $updated_key = $key;
            }
    
            $login->update();
    
            $this->set_route_response(ucfirst($updated_key) . " successfully updated!");
        }
    }

    public function delete() : void
    {
        $validation = [
            [
                "key" => "id",
                "validation" => "must_be_not_empty"
            ]
        ];

        $this->validate_data($validation);

        $login = (new Login())->find_by_id($this->data["id"]);

        if (empty($login->get_id()))
        {
            throw new Exception("Login not found!", Constants::BAD_REQUEST);
        }

        $login->delete();

        $this->set_route_response("Login successfully deleted!");
    }
}