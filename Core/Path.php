<?php
namespace Domnyus;
class Path
{
    private array $paths = [];

    public function __construct(array $paths = [])
    {
        $this->paths = $paths;
    }

    public function check_path() : string
    {
        foreach ($this->paths as $key => $value) {
            $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
            $uri = explode("/", $uri);
            $path_uri = explode("/", $key);
            $path_data = [];
            $matches = true;

            for ($i = 0; $i < count($uri); $i++)
            {
                if (!array_key_exists($i, $path_uri)) {
                    $matches = false;
                    continue;
                }
                if (strstr($path_uri[$i], ":"))
                {
                    $path_data[str_replace(":", "", $path_uri[$i])] = $uri[$i];
                    continue;
                }

                if ($path_uri[$i] !== $uri[$i]) {
                    $matches = false;
                    break;
                }
            }

            if ($matches) {
                $_POST = array_merge($_POST, $path_data);
                return $value;
            }
        }
        throw new \Exception("Invalid path!", Constants::BAD_REQUEST);
    }
}