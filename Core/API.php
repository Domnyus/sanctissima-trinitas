<?php

namespace Domnyus;

use Domnyus\Models\API_AUTH;
use Domnyus\Models\API_AUTH_ROUTES;
use Domnyus\Models\API_ROUTES;
use Exception;
use PDOException;
use Domnyus\Routes\_Login;

class API {
    private array $routes;
    private Path $path;
    public function __construct(?int $api_auth_id = null)
    {
        $this->routes = [];
        $response = [];
        $status_code = Constants::SUCCESS;
        try
        {
            $api_auth = (new API_AUTH())->find_by_id($api_auth_id);
            $api_auth_routes = (new API_AUTH_ROUTES)->select([
                "api_auth" => $api_auth->get_id()
            ]);

            $paths = [];

            foreach ($api_auth_routes as $api_auth_route)
            {
                $route = (new API_ROUTES())->find_by_id($api_auth_route->get_api_route());
                $paths[$route->get_path()] = $route->get_route();

                if (!array_key_exists($route->get_route(), $this->routes)) {
                    $this->routes[$route->get_route()] = new ("Domnyus\Routes\\" . $route->get_file())();
                }
            }

            $this->path = new Path($paths);

            $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
            $uri = explode("/", $uri);
            
            $response = $this->call($uri);
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

    public function call ($uri)
    {
        if (count($uri) < 2) {
            return $this->routes[$uri[0]]->call($_SERVER["REQUEST_METHOD"], $uri[1] ?? "/");
        }

        return $this->routes[$this->path->check_path()]->call($_SERVER["REQUEST_METHOD"], $this->path->check_path());
    }
}