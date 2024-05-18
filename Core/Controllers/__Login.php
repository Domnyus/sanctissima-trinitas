<?php
namespace Domnyus\Controllers;
use Domnyus\Controller;
use Domnyus\Models\VIEWS;
class __Login extends Controller
{
    public function index() : void
    {
        if (file_exists($this->view)) {
            include $this->view;
        }
    }

    private function page() : void
    {
        if (file_exists($this->view)) {
            include $this->view;
        }
    }

    private function form_action() : void
    {
        
    }
}