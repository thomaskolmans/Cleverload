<?php
use lib\Cleverload;
use lib\Http\Request;
use lib\Routing\Router;

require_once("autoloader.php");


$request = new Request($_SERVER);
$cleverload = new Cleverload($request);
$cleverload->setStaticFilesDir("./view/");
$cleverload->getRequest()->getRouter()->getResponse();
?>