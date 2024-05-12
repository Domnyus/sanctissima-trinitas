<?php

namespace Domnyus\Model;

use Domnyus\Model;

class Login extends Model
{
    protected ?int $id;
    protected ?string $username;
    protected ?string $password;
    public function __construct()
    {
        parent::__construct(strtolower(basename(__CLASS__)));
        $this->id = null;
        $this->username = null;
        $this->password = null;
    }

    public function parser(?bool $short_version = false): array
    {
        return [
            "id"=> $this->id,
            "username"=> $this->username
        ];
    }

    public function get_id(): ?int { return $this->id; }
    public function get_username(): ?string { return $this->username; }
    public function get_password(): ?string { return $this->password; }
    public function set_id(?int $id): Login { $this->id = $id; return $this; }
    public function set_username(?string $username): Login { $this->username = $username; return $this; }
    public function set_password(?string $password): Login { $this->password = $password; return $this; }
}