<?php
namespace lib\Routing;

use lib\Template\Template;
use lib\Cleverload;

class Route{

    public $uri;
    public $domain;

    private $file = null;
    private $action = null;

    protected $methods = [];
    protected $parameters = [];
    protected $wheres = [];
    protected $sections;
    protected $sectioncount;

    public function __construct($methods,$uri,$action){
        $this->uri = $uri;
        $this->sections = $this->setSections($this->uri);
        $this->sectioncount = $this->setSectionCount();
        $this->methods = $methods;
        $this->action = $action;
        $this->parameters = $this->setParameters();
    }

    public function where($variable,$regex){
        
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
    public function getParameters(){
        return $this->parameters;
    }
    public function addParameter($parameter){
        $this->parameters[] = $parameter;
        return $this;
    }
    public function addParameters($parameters){
        $this->parameters = [];
        foreach($parameters as $key => $value){
            $this->parameters[$key] = $value;
        }
        return $this;
    }
    public function setParameters(){
        foreach($this->sections as $section){
            if($section->is("value")){
                $this->parameters[] = $section->clean();
            }
        }
        return $this;
    }
    public function setParametersAsGet(){
        foreach($this->parameters as $key => $value){
            $_GET[$key] = $value;
        }
        return $_GET;
    }
    public function load(){
        if(is_callable($this->action)){
            return $this->loadCallable($this->action,$this->getParameters());   
        }
        $this->setParametersAsGet();
        $this->action = Cleverload::$filebase."/".$this->action;    
        return $this->loadFile();
    }

    public function loadCallable($func = null,$values = []){
        return $func(...array_values($values));
    }
    public function loadFile(){
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
        return $this->action;
    }
    public function getSectionCount(){
        return $this->sectioncount;
    }
    public function setAction($action){
        $this->action = $action;
    }
    public function getAction(){
        return $this->action;
    }
    public function getDomain(){
        return $this->domain;
    }
    public function setDomain($domain){
        $this->domain = $domain;
        return $this;
    }
    public function getSection($number){
        return $this->getSections()[$number];
    }
    public function getSections(){
        return $this->sections;
    }
    public function getMethods(){
        return $this->methods;
    }
    public function __call($function,$args){
        Cleverload::getRouter()->call($function,$args);
    }
    public static function __callStatic($function,$args){
        Cleverload::getRouter()->call($function,$args);
    }
}
?>