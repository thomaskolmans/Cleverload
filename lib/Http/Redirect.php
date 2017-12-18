<?php
namespace lib\Http;

use lib\Http\Request;
use lib\Http\HttpError;

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