<?php

namespace lib\Routing;

class Section{
    
    public $section;
    public $type;

    public function __construct($section,$type = "uri"){
        $this->section = $this->trimSlash($section);
        $this->type = $type;
    }
    public function is($string){
        switch($string){
            case 'value':
                $pathar = str_split($this->section);
                if($pathar[0] == "{" && end($pathar) == "}"){
                    return true;
                }else{
                    return false;
                }
            break;
            default:
                $pathar = str_split($path);
                if($pathar[0] == "{" && end($pathar) == "}"){
                    return false;
                }else{
                    return true;
                }
            break;
        }
    }
    public function clean(){
        return $this->trimSlash(str_replace(array("{","}"), "", $this->section));
    }
    public function get(){
        return $this->section;
    }
    public function trimSlash($string){
        return ltrim($string,'/');
    }
}
?>