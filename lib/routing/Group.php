<?php
namespace lib\routing;

use lib\exception\InvalidArgument;
use lib\Cleverload;
use Exception;

class Group {

    private $arguments = [];
    private $action;

    public function __construct($arguments, callable $action){
        $this->arguments = $arguments;
        $this->action = $action;
    }


	public function getArguments(){
		return $this->arguments;
	}
	
	
	public function setArguments($arguments){
		$this->arguments = $arguments;
		return $this;
	}
	

	public function getAction(){
		return $this->action;
	}
	
	
	public function setAction($action){
		$this->action = $action;
		return $this;
	}
	
}

?>