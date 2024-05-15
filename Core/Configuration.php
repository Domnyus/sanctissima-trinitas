<?php
namespace Domnyus;
use Domnyus\Models\API_AUTH;
class Configuration
{
    private array $tokens = [
        "ZXg6ZXg="
    ];

    public function start(
        bool $use_authentication = false,
        bool $auth_from_array = true,
        bool $auth_from_database = false
    ) : void
    {
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

        $config = [
            "" => function ($api_auth_id) {
                (new API($api_auth_id));
            },
            "system" => function () {
                (new Controller());
            }
        ];

        $url = $_SERVER["HTTP_HOST"];
        
        $target = strtok($url,".");

        if (empty($target) || !array_key_exists($target, $config)) {
            (new API($api_auth_id));
        } else {
            $config[$target]();
        }
    }

    public function __construct()
    {
        $origins = [
            "*"
        ];

        $blocked_origins = [
            
        ];

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
            "Origin"
        ];

        $blocked_headers = [
            
        ];

        $methods = [
            "GET",
            "POST",
            "PUT",
            "DELETE",
            "OPTIONS",
            "HEAD"
        ];

        $blocked_methods = [
            
        ];

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
            if (!in_array($key, $headers) || in_array($key, $blocked_headers)) {
                throw new \Exception("Header '$key' not allowed!", Constants::BAD_REQUEST);
            }
        }
    }
}