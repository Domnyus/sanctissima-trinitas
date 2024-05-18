<?php

namespace Domnyus\Models;

use Domnyus\Model;

class API_AUTH extends Model
{
    protected ?int $id;
    protected ?string $user;
    protected ?string $password;
    protected ?string $token;
    public function __construct()
    {
        parent::__construct(strtolower(basename(__CLASS__)));
        $this->id = null;
        $this->user = null;
        $this->password = null;
        $this->token = null;
    }

    public function parser(?bool $short_version = false): array
    {
        return [
            "id"=> $this->id,
            "user"=> $this->user,
            "token"=> $this->token
        ];
    }

    public function get_id(): ?int { return $this->id; }
    public function get_user(): ?string { return $this->user; }
    public function get_password(): ?string { return $this->password; }
    public function get_token(): ?string { return $this->token; }
    public function set_id(?int $id): API_AUTH { $this->id = $id; return $this; }
    public function set_user(?string $user): API_AUTH { $this->user = $user; return $this; }
    public function set_password(?string $password): API_AUTH { $this->password = $password; return $this; }
    public function set_token(?string $token): API_AUTH { $this->token = $token; return $this; }
}