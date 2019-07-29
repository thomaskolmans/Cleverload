<?php
namespace lib\http;

class Response {

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
        $error = new HttpError(404);
        return $error->getResponse();
    }

    public function notAuthorized(){
        $error = new HttpError(401);
        return $error->getResponse();    
    }

    public function notPermitted(){
        $error = new HttpError(403);
        return $error->getResponse();
    }

    public function noRoutes(){
        $error = new HttpError(999);
        return $error->getResponse();
    }

    public function setBody($string){
        $this->body = $string;
    }

    public function sendFile($file){
        print(file_get_contents($file));
    }

    public function sendBody(){
        return print($this->body);
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
        foreach($this->headers as $header => $value) {
            echo "<b>$header:</b> $value <br />";
        }
        print($this->body);
    }
}
?>