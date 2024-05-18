<?php

namespace Domnyus\Models;

use Domnyus\Model;

class API_ROUTES extends Model
{
    protected ?int $id;
    protected ?string $path;
    protected ?string $route;
    protected ?string $file;
    protected ?string $method;
    public function __construct()
    {
        parent::__construct(strtolower(basename(__CLASS__)));
        $this->id = null;
        $this->path = null;
        $this->route = null;
        $this->file = null;
        $this->method = null;
    }

    public function parser(?bool $short_version = false): array
    {
        return [
            "id"=> $this->id,
            "path"=> $this->path,
            "route"=> $this->route,
            "file"=> $this->file,
            "method"=> $this->method
        ];
    }

    public function get_id () : int|null { return $this->id ; }
    public function set_id (?int $id) : API_ROUTES { $this->id = $id; return $this; }
    public function get_path () : string|null { return $this->path ; }
    public function set_path (?string $path) : API_ROUTES { $this->path = $path; return $this; }
    public function get_route () : string | null { return $this->route ; }
    public function set_route (?string $route)  : API_ROUTES { $this->route = $route; return $this; }
    public function get_file () : string | null { return $this->file ; }
    public function set_file (?string $file)  : API_ROUTES { $this->file = $file; return $this; }
    public function get_method () : string | null { return $this->method ; }
    public function set_method (?string $method)  : API_ROUTES { $this->method = $method; return $this; }
}