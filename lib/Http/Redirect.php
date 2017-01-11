<?php
namespace lib\Http;

use lib\Http\Request;

class Redirect extends Request{
    
    public function to(string $to){
        $this->set("Location: ".$to);
    }

    public function back(){

    }

    public function forward(){

    }
}

?>