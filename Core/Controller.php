<?php
namespace Domnyus;
class Controller
{
    protected ?string $view;
    public function get_view(): ?string { return $this->view; }
    public function set_view(string $view): void { $this->view = $view; }
}