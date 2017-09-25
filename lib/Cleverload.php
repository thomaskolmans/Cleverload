<?php
namespace lib;

use lib\Http\Request;

class Cleverload{

    public $request;

    public $staticfilesdir = "./";

    public $start_time;
    public $end_time;

    public static $instance = null;

    public function __construct(Request $request){
        $this->start_time = microtime();
        define("ROOT",__DIR__);
        define("CROOT",getcwd());
        $this->request = $request;
        self::$instance = $this;
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