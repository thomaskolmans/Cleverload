<?php
namespace lib\exception;

use \Exception;
use \ReflectionClass;

abstract class CleverloadException extends Exception implements IException {

    protected $message = 'Unknown exception';  
    private   $string;                            
    protected $code    = 0;         
    protected $file;       
    protected $line;       
    private   $trace;            

    public function __construct($message = null, $code = 0){
        if (!$message){
            throw new $this('Unknown '. get_class($this));
        }
        parent::__construct($message, $code);
    }

    private function getClassname() {
        return (new ReflectionClass($this))->getShortName();
    }

    public function __toString(){
        return "". $this->getClassname().": {$this->message} ";
    }
}