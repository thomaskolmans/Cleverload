<?php
namespace lib\routing;

use lib\exception\InvalidArgument;
use lib\Cleverload;
use Exception;

class Middleware extends Group {
    
    private $before;

    public function __construct($arguments, callable $action){
        $this->setArguments($arguments);
        $this->setAction($action);
    }
	
	public function getBefore(){
		return $this->before;
	}
	
	public function setBefore($before){
		$this->before = $before;
		return $this;
	}

}

?>