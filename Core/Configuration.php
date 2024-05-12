<?php
namespace Domnyus;
class Configuration
{
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