<?php

namespace Domnyus;

use Domnyus\Controllers\__Login;
use Domnyus\Models\VIEWS;
use Domnyus\Models\API_AUTH;

class Configuration
{
    private array $tokens = [
        "ZXg6ZXg="
    ];
    private array $views = [
        [
            "uri" => "",
            "path" => "",
            "class" => "__Home",
            "method" => "index",
            "view" => Constants::VIEWS . "index.php"
        ]
    ];

    public function __construct(
        bool $use_authentication = false,
        bool $auth_from_array = true,
        bool $auth_from_database = false,
        bool $view_from_array = true,
        bool $view_from_database = false
    ) {

        $config = [
            "" => function ($api_auth_id) {
                (new API($api_auth_id));
            },
            "system" => function () use ($view_from_array, $view_from_database) {
                $this->redirect($view_from_array, $view_from_database);
            }
        ];

        $url = $_SERVER["HTTP_HOST"];

        $target = strtok($url, ".");
        $ignore_headers = false;

        if (empty($target) || !array_key_exists($target, $config)) {
            $authenticated = !$use_authentication;

            if (isset($_SERVER["HTTP_AUTHORIZATION"])) {
                $token = explode(" ", $_SERVER["HTTP_AUTHORIZATION"])[0];
            } else if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"])) {
                $token = base64_encode("{$_SERVER["PHP_AUTH_USER"]}:{$_SERVER["PHP_AUTH_PW"]}");
            } else if ($use_authentication) {
                exit("Not allowed!");
            }

            $api_auth_id = null;

            if ($use_authentication) {
                if ($auth_from_array && in_array($token, $this->tokens)) {
                    $authenticated = true;
                }

                if ($auth_from_database) {
                    $user = $_SERVER["PHP_AUTH_USER"] ?? null;
                    $password = $_SERVER["PHP_AUTH_PW"] ?? null;

                    $auths = (new API_AUTH())
                        ->select([
                            "user" => $user,
                            "password" => $password
                        ]);

                    if (empty($auths)) {
                        $auths = (new API_AUTH())
                            ->select([
                                "token" => $token
                            ]);
                    }

                    if (!empty($auths)) {
                        $authenticated = true;
                    }

                    $api_auth_id = $auths[0]->get_id();
                }

                if (!$authenticated) {
                    exit("Not allowed!");
                }
            }

            (new API($api_auth_id));
        } else {
            $ignore_headers = true;
            $config[$target]();
        }

        $origins = [
            "*"
        ];

        $blocked_origins = [];

        $headers = [
            "Content-Type",
            "Authorization",
            "User-Agent",
            "Accept",
            "Postman-Token",
            "Host",
            "Accept-Encoding",
            "Connection",
            "Content-Length",
            "Origin",
            "Cache-Control"
        ];

        $blocked_headers = [];

        $methods = [
            "GET",
            "POST",
            "PUT",
            "DELETE",
            "OPTIONS",
            "HEAD"
        ];

        $blocked_methods = [];

        $headers_sent = getallheaders();

        if (isset($headers_sent["Origin"])) {
            if (!in_array("*", $origins) && (!in_array($headers_sent["Origin"], $origins) || in_array($headers_sent["Origin"], $blocked_origins))) {
                throw new \Exception("Origin {$headers_sent["Origin"]} not allowed!", Constants::BAD_REQUEST);
            }
        }

        if (!in_array($_SERVER["REQUEST_METHOD"], $methods) || in_array($_SERVER["REQUEST_METHOD"], $blocked_methods)) {
            throw new \Exception("Method {$_SERVER["REQUEST_METHOD"]} not allowed!", Constants::BAD_REQUEST);
        }

        foreach ($headers_sent as $key => $value) {
            if (!$ignore_headers && !in_array($key, $headers) || in_array($key, $blocked_headers)) {
                throw new \Exception("Header '$key' not allowed!", Constants::BAD_REQUEST);
            }
        }
    }

    private function redirect(
        bool $view_from_array = true,
        bool $view_from_database = false
    ) : void {
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $uri = explode("/", $uri);

        $views = [];

        if ($view_from_array) {
            $views = array_merge($this->views, $views);
        }
        
        if ($view_from_database) {
            $vs = (new VIEWS())->select([
                "uri" => $uri[0]
            ], use_parser: true);

            foreach ($vs as $v) {
                $v["from"] = "database";
                $views[] = $v;
            }
        }

        $matches = array_filter($views, function ($view) use ($uri) {
            return isset($uri[0]) && $uri[0] === $view["uri"];
        });

        if (empty($matches)) {
            exit("Not Found!");
            //include 404
        }

        $paths = [];
        $routes = [];

        foreach ($matches as $match) {
            $paths[$uri[0] . $match["path"]] = $uri[0] . $match["path"];
            if (!in_array($uri[0] . $match["path"], $routes)) {
                $routes[$uri[0] . $match["path"]] = [
                    "class" => (Constants::NAMESPACE_CONTROLLERS . $match["class"]),
                    "view" => $match["view"],
                    "method" => $match["method"],
                    "from" => $match["from"] ?? "array"
                ];
            }
        }

        $path = (new Path($paths))->check_path();
        if (empty($paths)) {
            exit("Not Found!");
            //404
        } else {
            $view = (new ($routes[$path]["class"])());
            $view->set_view($routes[$path]["view"]);
            $view->{$routes[$path]["method"]}();
        }
    }
}
