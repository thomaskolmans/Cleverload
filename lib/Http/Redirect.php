<?php
namespace lib\Http;

use lib\Http\Request;
use lib\Http\HttpError;

class Redirect extends Request{
    
    public function to(string $to){
        $this->set("Location: ".$to);
        $this->send();
    }

    public function back(){

    }
    public function forward(){

    }
    public function error($errortype){
        $httperror = new HttpError();
        return $httperror->get($code);
    }
}

?>