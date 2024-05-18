<?php
namespace Domnyus\Controllers;
use Domnyus\Controller;
use Domnyus\Models\VIEWS;
class __Home extends Controller
{
    public function index() : void
    {
        if (file_exists($this->view)) {
            include $this->view;
        }
    }
}