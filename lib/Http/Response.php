<?php
namespace lib\Http;

class Response{

    public $response_code = 200;

    public $headers = [];
    public $body;

    public function __construct(){
        ob_start("ob_gzhandler");
    }
    public static function redirect($path){
        return new Redirect();
    }
    public function send(){
        $this->sendHeaders();
        $this->sendBody();
        exit;
    }
    public function notFound(){
        return HttpError::notFound();
    }
    public function notAutherized(){
        return new HttpError(401);
    }
    public function notPermitted(){
        return new HttpError(403);
    }
    public function noRoutes(){
        return new HttpError(999);
    }
    public function setBody($string){
        $this->body = $string;
    }
    public function sendFile($file){
        $this->setBody(file_get_contents($file));
        $this->send();
    }
    public function sendBody(){
        return printf($this->body);
    }
    public function addHeader($header,$value){
        $this->headers[$header] = $value;
        return $this;
    }
    public function setHeader($headers){
        $this->headers = $headers;
        return $this;
    }
    public function sendHeaders(){
        foreach($this->headers as $header => $value){
            header("$header: $value");
        }
        return $this;
    }
    public function toString(){
        
    }
}
?>