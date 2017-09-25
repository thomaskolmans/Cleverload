<?php
namespace lib\Http;

class Response{

    public $responsecode;

    public $headers = [];
    public $body;

    public function __construct(){

    }
    public static function redirect($path){
        return header("Location: ".$path);
    }

    public function toString(){
        
    }
}
?>