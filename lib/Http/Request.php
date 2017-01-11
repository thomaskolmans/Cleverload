<?php

namespace lib\Http;

class Request{

    public $header;

    public function __construct(array $headers = array()){
        $this->header = implode(" /r/n",$headers);
    }

    public function set($header){
        $this->header .= $header." /r/n";
    }
}


?>