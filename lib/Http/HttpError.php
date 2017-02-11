<?php
namespace lib\Http;

class HttpError{

    public function error($errortype = "404"){
        $this->getDefaultError($errortype);
    }

    public function getDefaultError($errortype){
        switch($errortype){
            default:
                printf("<center style='margin-top: 33vh;font-size: 40px;'><h1>404 error</h1></center><br/><center style='font-family: Open-sans, Times New Roman; font-size: 35px;'><i> Ooops! </i>You seem to be lost.</center>");
                exit;
            break;
        }
    }
}
?>