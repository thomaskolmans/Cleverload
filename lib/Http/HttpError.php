<?php
namespace lib\Http;

class HttpError{

    public $response_code;

    public function __construct($response_code){
        $this->response_code = $response_code;
        http_response_code($response_code);
        $this->getError($this->response_code);
    }
    public static function notFound(){
        return new HttpError(404);
    }
    public function getError($response_code = 404){
        $this->getDefaultError($response_code);
    }
    public function getDefaultError($response_code){
        switch($response_code){
            case 404:
              printf("<center style='margin-top: 33vh;font-size: 40px;'><h1>404 error</h1></center><br/><center style='font-family: Open-sans, Times New Roman; font-size: 35px;'><i> Ooops! </i>You seem to be lost.</center>");
                exit;
            break;
            default:
                printf("<center style='margin-top: 33vh;font-size: 40px;'><h1>404 error</h1></center><br/><center style='font-family: Open-sans, Times New Roman; font-size: 35px;'><i> Ooops! </i>You seem to be lost.</center>");
                exit;
            break;
        }
    }
}

?>