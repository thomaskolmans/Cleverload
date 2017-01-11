<?php
    use lib\Cleverload;
    require_once("autoloader.php");
    $c = new Cleverload($_SERVER["REQUEST_URI"],__FILE__);
    
?>