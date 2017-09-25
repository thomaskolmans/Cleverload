<?php
namespace lib\Http;

class Response{

    public $response_code;

    public $headers = [];
    public $body;

    public function __construct(){

    }
    public static function redirect($path){
        return new Redirect();
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
    public function setBody($string){
        $this->body = $string;
    }
    public function toString(){
        
    }
}
?>