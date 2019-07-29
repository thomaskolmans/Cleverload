<?php
namespace lib\http;

use lib\Cleverload;

class HttpError {

    public $response_code;
    public $response;

    public function __construct($response_code){
        $this->response_code = $response_code;
        http_response_code($response_code === 999 ? 200 : $response_code);
        $this->response = $this->getError($this->response_code);
    }

    public static function notFound(){
        $error = new HttpError(404);
        return $error->getResponse();
    }

    public static function noRoutes(){
        $error = new HttpError(999);
        return $error->getResponse();
    }

    public function getResponse() {
        return $this->response;
    }

    private function getError($response_code = 404){
        $statusRoutes = Cleverload::getInstance()->getRequest()->getRouter()->statusRoutes;
        if (array_key_exists($response_code, $statusRoutes)){
            return $statusRoutes[$response_code];
        } else {
            $this->getDefaultError($response_code);
        }
    }

    private function getDefaultError($response_code){
        switch($response_code){
            case 404:
              printf("<center style='margin-top: 33vh;font-size: 40px; font-family: sans-serif;'><h1 style='margin: 15px 0'>404 error</h1></center><br/><center style='font-family: Open-sans, Times New Roman; font-size: 35px;'><i> Ooops! </i>You seem to be lost.</center>");
              exit;
            break;
            case 401:
                printf("<center style='margin-top: 33vh;font-size: 40px; font-family: sans-serif;'><h1 style='margin: 15px 0'>401 error</h1></center><br/><center style='font-family: Open-sans, Times New Roman; font-size: 35px;'>Not authorized.</center>");
                exit;
            case 403:
                printf("<center style='margin-top: 33vh;font-size: 40px; font-family: sans-serif;'><h1 style='margin: 15px 0'>403 error</h1></center><br/><center style='font-family: Open-sans, Times New Roman; font-size: 35px;'>Not permitted.</center>");
                exit;
            case 999:
                printf("
                    <center style='margin-top: 15vh;'><img style='height: 250px; width: 250px;' src='/cleverload.svg' /></center>
                    <center style='margin-top: 35px; font-size: 30px; font-family: sans-serif;'><h1>Welcome to Cleverload!</h1></center>
                    <center style='margin-top: 35px; font-size: 20px; font-family: sans-serif;'><h2>Get started by adding routes in the 'routes' folder. Or read the <a href='https://github.com/thomaskolmans/Cleverload/tree/master/docs'>docs</a></h2></center>
                    ");
                exit;
                break;
            default:
                printf("<center style='margin-top: 33vh;font-size: 40px; font-family: sans-serif;'><h1 style='margin: 15px 0'>".$response_code." error</h1></center><br/><center style='font-family: Open-sans, Times New Roman; font-size: 35px;'><i> Ooops! </i>Something has gone wrong.</center>");
                exit;
            break;
        }
    }
}

?>