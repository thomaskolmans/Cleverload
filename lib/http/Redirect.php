<?php
namespace lib\http;

use lib\http\Request;
use lib\http\HttpError;

class Redirect extends Request{
    
    public static function to(string $to){
        header("Location: $to");exit;
    }

    public function back(){

    }

    public function forward(){

    }
    
    public static function error($errortype){
        $httperror = new HttpError();
        return $httperror->get($code);
    }
}

?>