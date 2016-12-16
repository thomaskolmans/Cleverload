<?php
namespace lib\Router;

use lib\Cleverload;
use lib\Http\Redirector;

class Router extends Redirector{
    
    public $path;
    public $base;

    private $filetype;
    private $pages;

    public function __construct($path){
        $this->path = $path;
        $this->base = Cleverload::$base;
        $this->pages = Cleverload::getPages();
        parent::__construct();
    }
    public function getFile(){

    }
    public function getRouterDefaults(){
        foreach($this->pages as $page){

        }
    }
    public function route($route,$file){
        $this->pages[$route] = $file;
    }
    public function to(string $to){
        return $this->to($to);
    }

    private function getPage($item,$key = false){
        foreach($this->pages as $keys => $value){
            if($keys == $item){
                if($key){
                    return $keys;
                }
                return $value;
            }
        }
    }
}
?>