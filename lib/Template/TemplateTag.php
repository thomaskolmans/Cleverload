<?php

namespace lib\Template;

use lib\Template\Template;

abstract class TemplateTag extends Template{

    public $tag;

    public function __construct(){
        $this->setTag(__CLASS__);
    }
    public function setTag($tag){
        $this->tag = $tag;
    }
}
?>