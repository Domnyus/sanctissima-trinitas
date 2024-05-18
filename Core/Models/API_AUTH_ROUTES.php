<?php

namespace Domnyus\Models;

use Domnyus\Model;

class API_AUTH_ROUTES extends Model
{
    protected ?int $api_auth;
    protected ?int $api_route;
    public function __construct()
    {
        parent::__construct(strtolower(basename(__CLASS__)));
        $this->api_auth = null;
        $this->api_route = null;
    }

    public function parser(?bool $short_version = false): array
    {
        return [
            "api_auth"=> $this->api_auth,
            "api_route"=> $this->api_route
        ];
    }

    public function get_api_auth () : int|null { return $this->api_auth ; }
    public function set_api_auth (?int $api_auth) : API_AUTH_ROUTES { $this->api_auth = $api_auth; return $this; }
    public function get_api_route () : int|null { return $this->api_route ; }
    public function set_api_route (?int $api_route) : API_AUTH_ROUTES { $this->api_route = $api_route; return $this; }
}