<?php

namespace lib\Template;

use lib\Cleverload;
use lib\Template\Template;

class TemplateLoader extends Template,Cleverload{

    private $filepath;

    public function __construct($filepath){
        $this->filepath = $filepath;
    }

}