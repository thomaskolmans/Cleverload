<?php
    use lib\Cleverload;
    use lib\Routing\Route;
    require_once("autoloader.php");
    $c = new Cleverload;

    $c->group(["domain" => "{id}.localhost"], function($id) use ($c){
        $c->get("/question/{id}",function($username) use ($id){
            echo "hey, this is the userid: ".$id;
        });
    });

?>