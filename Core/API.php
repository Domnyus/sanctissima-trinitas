<?php

namespace Domnyus;

use Exception;
use PDOException;
use Domnyus\Routes\_Login;

class API {
    private array $routes;
    private Path $path;
    public function __construct()
    {
        $this->routes = [];
        $response = [];
        $status_code = Constants::SUCCESS;
        try
        {
            $configuration = new Configuration();
            
            $this->path = new Path([
                "login/:id" => "/"
            ]);

            $this->routes["login"] = new _Login();

            $response = $this->call();
            $status_code = $response["status_code"];
        }
        catch (PDOException | Exception $exception)
        {
            $response = [
                "success" => false,
                "message" => $exception->getMessage()
            ];

            if (is_integer( $exception->getCode())) {
                $status_code = $exception->getCode();
            }
        }
        finally
        {
            header("Content-Type: application/json");
            echo json_encode($response);
            http_response_code($status_code);
        }
    }

    public function call ()
    {
        $uri = explode("/", $_SERVER["REQUEST_URI"]);
        if (count($uri) < 2) {
            return $this->routes[$uri[0]]->call($_SERVER["REQUEST_METHOD"], $uri[1] ?? "/");
        }
        return $this->routes[$uri[0]]->call($_SERVER["REQUEST_METHOD"], $this->path->check_path());
    }
}