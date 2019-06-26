<?php
namespace lib\http;

class Request{

    public $router;
    public $request;

    public $uri;
    public $path;
    public $domain;
    public $method;
    public $userip;
    public $serverip;
    public $time;
    public $query;

    public function __construct($request){
        $this->request = $request;
        $this->document_root = $this->request["DOCUMENT_ROOT"];
        $this->query = $this->request["QUERY_STRING"];
        $this->uri = preg_replace('/\?.*/', '', $this->request["REQUEST_URI"]);
        $this->path = str_replace($this->getDepth(), "", $this->uri);
        $this->domain = $this->request["SERVER_NAME"];
        $this->method = $this->request["REQUEST_METHOD"];
        $this->userip = $this->request["REMOTE_ADDR"];
        $this->serverip = $this->request["SERVER_ADDR"];
    }
    public function getDepth(){
        return str_replace($this->document_root,"",str_replace("\\","/",getcwd())."/");
    }
    public function setRouter($router){
        $this->router = $router;
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
    public function getRequest(){
        return $this->request;
    }
    public function getUri(){
        return $this->uri;
    }
}


?>