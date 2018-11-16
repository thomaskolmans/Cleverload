<?php
namespace lib;

use lib\Http\Request;
use lib\Routing\Router;

class Cleverload{

    public $request;
    public $root;
    public $sroot;

    public $template = true;
    public $staticfilesdir = "./";
    public $viewdir = "./";

    public $caseSensitive = false;

    public $start_time;
    public $end_time;

    public static $instance = null;

    public function __construct(Request $request){
        $this->start_time = microtime();
        $this->request = $request;
        $this->root = getcwd();
        $this->sroot = $this->request->getRequest()["DOCUMENT_ROOT"];
        self::$instance = $this;
        $this->request->setRouter(new Router($this->request));
    }

    public static function getInstance(){
        if(isset(self::$instance)){
            return self::$instance;
        }
        return null;
    }

    public static function getPages(){
        return include(__DIR__."/../pages.php");
    }

    public static function getConfig($item, $key = false){
        $config = include(__DIR__."/../config.php");  
        foreach($config as $keys => $value){
            if($keys == $item){
                if($key){
                    return $keys;
                }
                return $value;
            }
        }
    }

    public function cors($origins = [], $headers = [], $methods = []){
        if (empty($origins)){
            $origins = ["*"];
        }
        if (empty($headers)){
            $origins = ["X-Requested-With", "Content-Type", "Origin", "Cache-Control", "Pragma", "Authorization", "Accept", "Accept-Encoding"];
        }
        if (empty($methods)){
            $methods = ["PUT", "POST", "GET", "OPTIONS", "DELETE"];
        }

        header("Access-Control-Allow-Origin: ".implode(", ", $origins));
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Max-Age: 1000");
        header("Access-Control-Allow-Headers: ".implode(", ", $headers));
        header("Access-Control-Allow-Methods: ".implode(", ", $methods));
    }

    public function getExcecutiontime(){
        return  $this->end_time - $this->start_time;
    }
    public function setTemplate($boolean){
        $this->template = $boolean;
    }
    public function getTemplate(){
        return $this->template;
    }
    public function setViewDir($dir){
        $this->viewdir = $dir;
        return $this;
    }
    public function getViewDir(){
        return $this->viewdir;
    }
    public function setStaticFilesDir($dir){
        $this->staticfilesdir = $dir;
        return $this;
    }
    public function getStaticFilesDir(){
        return $this->staticfilesdir;
    }
    public function setCaseSensitive($bool){
        $this->caseSensitive = $bool;
        return $this;
    }
    public function getCaseSensitive(){
        return $this->caseSensitive;
    }
    public function forceHttps(){
        if($_SERVER["HTTPS"] != "on"){
            header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
            exit;
        }
    }
    public function getRequest(){
        return $this->request;
    }
}
?>