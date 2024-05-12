<?php

require __DIR__ . "/vendor/autoload.php";

use Domnyus\API;

$_SERVER["REQUEST_URI"] = ltrim($_SERVER["REQUEST_URI"], "/Domnyus");

(new API());
