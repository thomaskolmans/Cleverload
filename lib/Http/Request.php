<?php
namespace lib\Http;

use lib\Routing\Router;

class Request{

    public $router;
    public $request;

    public $path;
    public $domain;
    public $method;
    public $userip;
    public $serverip;
    public $time;

    public function __construct($request){
        $this->request = $request;
        $this->path = $this->request["REQUEST_URI"];
        $this->domain = $this->request["SERVER_NAME"];
        $this->method = $this->request["REQUEST_METHOD"];
        $this->userip = $this->request["REMOTE_ADDR"];
        $this->serverip = $this->request["SERVER_ADDR"];

        $this->router = new Router($this);
    }

    public function getRouter(){
        return $this->router;
    }
    public function getPath(){
        return $this->path;
    }
    public function getServerip(){
        return $this->serverip;
    }
    public function getUserip(){
        return $this->userip;
    }
    public function getMethod(){
        return $this->method;
    }
    public function getDomain(){
        return $this->domain;
    }
}


?>