<?php

namespace Domnyus\Models;

use Domnyus\Model;

class VIEWS extends Model
{
    protected ?int $id;
    protected ?string $uri;
    protected ?string $method;
    protected ?string $class;
    protected ?string $view;
    public function __construct()
    {
        parent::__construct(strtolower(basename(__CLASS__)));
        $this->id = null;
        $this->uri = null;
        $this->method = null;
        $this->class = null;
        $this->view = null;
    }

    public function parser(?bool $short_version = false): array
    {
        return [
            "id"=> $this->id,
            "uri"=> $this->uri,
            "class"=> $this->class,
            "view"=> $this->view,
            "method"=> $this->method
        ];
    }

    public function get_id () : int|null { return $this->id ; }
    public function set_id (?int $id) : VIEWS { $this->id = $id; return $this; }
    public function get_uri () : string|null { return $this->uri ; }
    public function set_uri (?string $uri) : VIEWS { $this->uri = $uri; return $this; }
    public function get_method () : string | null { return $this->method ; }
    public function set_method (?string $method)  : VIEWS { $this->method = $method; return $this; }
    public function get_class () : string | null { return $this->class ; }
    public function set_class (?string $class)  : VIEWS { $this->class = $class; return $this; }
    public function get_view () : string | null { return $this->view ; }
    public function set_view (?string $view)  : VIEWS { $this->view = $view; return $this; }
}