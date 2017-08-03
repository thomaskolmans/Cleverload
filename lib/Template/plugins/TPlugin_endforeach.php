<?php
namespace lib\Template\plugins;

use lib\Template\TemplatePlugin;

class TPlugin_endforeach extends TemplatePlugin{

    public $arguments;
    public $content;
    
    public function __construct($content,$arguments){
        $this->arguments = $arguments;
    }
}
?>