<?php
    use lib\Cleverload;
    require_once("autoloader.php");
    new Cleverload($_SERVER["REQUEST_URI"],__FILE__);
?>