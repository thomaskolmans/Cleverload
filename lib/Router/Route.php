<?php
namespace lib\Router;

class Route{
    
    public $path;
    public $file;

    public function __construct($path,$file){
        $this->add($path,$file);
    }
    /**
     * Public static functions
     */
    public static function get($path, $closure){

    }
    public static function group($argument, $closure){

    }
    /**
     * Public functions
     */

    public function add($path,$file){
        $this->path = $path;
        $this->file = $file;
        if($this->validate($this->path)){
            return true;
        }
        return false;
    }
    public function decode_path($path){
        $output = array(
            "sections" => array(),
            "values" => array(),
        );
        $sections = $this->getSections($path);
        for($i = 0; $i < count($sections); $i++){
            $section = $sections[$i];
                $pathar = str_split($path);
                if($pathar[0] == "{" && end($pathar) == "}"){
                array_push($output["values"],str_replace(array("{","}"), "", $section));
            }else{
                array_push($output["sections"],$section);
            }
        }
        return $output;
    }
    public function getSection($path,$number){
        $sections = $this->getSections($path);
        if($number == 2){
            var_dump($sections);
        }
        return $this->removeSlash($sections[$number]);
    }
    public function decode($route){
        if($this->validate($route)){
            return $this->getSections($route);
        }else{
            return false;
        }
    }
    public function is($path,$string){
        switch($string){
            case 'value':
                $pathar = str_split($path);
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
    public function validate($path){
        if(preg_match("/[a-zA-Z\/}{]*/", $path)){
            return true;
        }
        return false;
    }

    public function getPath(){
        return $this->path;
    }

    public function getFile(){
        return $this->file;
    }

    /**
     * Private functions
     */
    
    private function getSections($path){
        return array_values(array_filter(explode("/",$path)));
    }
    private function countSections($path){
        return count($this->getSections());
    }
    private function removeSlash($string){
        return ltrim($string,'/');
    }
}
?>