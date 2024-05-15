<?php

require __DIR__ . "/vendor/autoload.php";

use Domnyus\Configuration;

$_SERVER["REQUEST_URI"] = str_replace("/Domnyus/", "", $_SERVER["REQUEST_URI"]);

(new Configuration())->start(true, true, true);

