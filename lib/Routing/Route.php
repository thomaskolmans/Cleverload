<?php
namespace lib\Routing;

use lib\Template\Template;

class Route{

    public $uri;
    public $domain;

    private $file = null;
    private $action = null;

    protected $methods = [];
    protected $parameters = [];
    protected $sections;
    protected $sectioncount;

    public function __construct($methods,$uri,$action){
        $this->uri = $uri;
        $this->sections = $this->setSections($this->uri);
        $this->sectioncount = $this->setSectionCount();
        $this->methods = $methods;
        if(is_callable($this->action)){
            $this->action = $action;
        }else{
            $this->file = $action;
        }
    }

    public function countSectionVariables(){
        $count = 0;
        foreach($this->getSections() as $section){
            if($section->is("value")){
                $count++;
            }
        }
        return $count;
    }
    public function setSectionCount(){
        return count($this->getSections());
    }
    public function getDomain(){
        return $this->domain;
    }
    public function setDomain($domain){
        return $this->domain = $domain;
    }
    public function getSection($number){
        return $this->getSections()[$number];
    }
    public function getSections(){
        return $this->sections;
    }
    public function setSections(){
        $result = $this->setURISections($this->uri);
        array_push($result, $this->setDomainSections($this->domain));
        $result = array_filter($result);
        return $result;
    }
    public function setURISections($path){
        return $this->explodeIntoSections("/",$path,"uri");
    }
    public function setDomainSections($domain){
        return $this->explodeIntoSections(".", $domain,"domain");
    }
    public static function explodeIntoSections($divider,$string,$type){
        $arr = [];
        foreach(array_filter(array_values(explode($divider,$string))) as $section){ 
            if(!empty($section)){
                $arr[] = new Section($section);
            }
        }
        return $arr;
    }
    public function load(){
        if(is_callable($this->action)){
            return $this->loadCallable($this->action,$this->parameters);   
        }
        return $this->loadFile($this->file);
    }

    public function loadCallable($func = null,$values = []){
        return $func(...array_values($values));
    }
    public function loadFile($file){
        return new Template($this);
    }
    public function isValid(){
        if(preg_match("/[a-zA-Z\/}{]*/", $path)){
            return true;
        }
        return false;
    }
    public function getURI(){
        return $this->URI;
    }
    public function getFile(){
        return $this->file;
    }
    public function getSectionCount(){
        return $this->sectioncount;
    }
    public function setAction($action){
        if(is_callable($this->action)){
            $this->action = $action;
        }else{
            $this->file = $action;
        }
    }
    public function getAction(){
        if(isset($this->action)){
            return $this->action;
        }
        return $this->file;
    }
}
?>