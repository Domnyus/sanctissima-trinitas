<?php
namespace Domnyus;
class Controller
{
    protected array $uri;
    protected string $path;
    protected array $___routes = [
        "uri" => [
            "" => "main_file.php",
            "action" => "file.ext"
        ]
    ];

    public function __construct()
    {
        $this->uri = explode("/", $_SERVER["REQUEST_URI"]);

        if (
            !array_key_exists($this->uri[0], $this->___routes) ||
            (!isset($this->uri[1]) && !array_key_exists("", $this->___routes[$this->uri[0]])) ||
            (isset($this->uri[1]) && !array_key_exists($this->uri[1], $this->___routes[$this->uri[0]]))
        ) {
            //404
        } else {
            $this->path = Constants::VIEWS . "{$this->uri[0]}/" . (isset($this->uri[1]) ? $this->___routes[$this->uri[0]][$this->uri[1]] : $this->___routes[$this->uri[0]][""]);
            if (method_exists($this, $this->path)) {
                $this->{$this->path}();
            } else {
                $this->index();
            }
        }
    }

    private function index() : void
    {
        $data = "0";
        include $this->path;
    }

    private function page() : void
    {
        include $this->path;
    }

    private function form_action() : void
    {
        
    }
}