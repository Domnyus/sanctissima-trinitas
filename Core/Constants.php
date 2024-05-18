<?php
namespace Domnyus;
class Constants
{
    public CONST SUCCESS = 200;
    public CONST BAD_REQUEST = 400;
    public CONST INTERNAL_SERVER_ERROR = 500;
    public CONST DBHOST = "localhost:3306";
    public CONST DBNAME = "db";
    public CONST DBUSER = "root";
    public CONST DBPASS = "root";
    public CONST VIEWS = __DIR__ . "/Views/";
    public CONST NAMESPACE_CONTROLLERS = "Domnyus\\Controllers\\";
    public CONST PUBLIC = __DIR__ . "/Public/";
}
