<?php
namespace lib;

use lib\Http\Request;
use lib\Routing\Router;

class Cleverload{

    public $request;
    public $root;

    public $template = true;
    public $staticfilesdir = "./";

    public $start_time;
    public $end_time;

    public static $instance = null;

    public function __construct(Request $request){
        $this->start_time = microtime();
        $this->request = $request;
        $this->root = getcwd();
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
    public function getExcecutiontime(){
        return  $this->end_time - $this->start_time;
    }
    public function setTemplate($boolean){
        $this->template = $boolean;
    }
    public function getTemplate(){
        return $this->template;
    }
    public function setStaticFilesDir($dir){
        $this->staticfilesdir = $dir;
        return $this;
    }
    public function getStaticFilesDir(){
        return $this->staticfilesdir;
    }
    public function getRequest(){
        return $this->request;
    }
}
?>