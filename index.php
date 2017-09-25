<?php
    use lib\Cleverload;
    use lib\Http\Request;
    
    require_once("autoloader.php");

    $cleverload = new Cleverload(new Request($_SERVER));
    $cleverload->getRequest()->getRouter()->getResponse();
?>