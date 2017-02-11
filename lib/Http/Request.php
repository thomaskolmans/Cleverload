<?php

namespace lib\Http;

class Request{

    public $header;
    public $history;

    public function __construct($headers = ""){
        $this->header = $headers;
        $this->getHistory();
    }
    public function set($header){
        $this->header .= $header;
    }
    public function setHistory($path){
        return $_SESSION["history"] = $path;
    }
    private function getHistory(){
        if(isset($_SESSION["history"])){
            return $this->history = $_SESSION["history"];
        }
        return $this->history = null;
    }   
    public function send(){
        return header($this->header);
    }
}


?>