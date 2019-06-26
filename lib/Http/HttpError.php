<?php
namespace lib\http;

class HttpError {

    public $response_code;

    public function __construct($response_code){
        $this->response_code = $response_code;
        http_response_code($response_code === 999 ? 200 : $response_code);
        $this->getError($this->response_code);
    }

    public static function notFound(){
        return new HttpError(404);
    }

    public static function noRoutes(){
        return new HttpError(999);
    }

    public function getError($response_code = 404){
        $this->getDefaultError($response_code);
    }

    public function getDefaultError($response_code){
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