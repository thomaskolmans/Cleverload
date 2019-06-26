<?php
namespace lib\template\plugins;

use lib\template\TemplatePlugin;

class TPlugin_endforeach extends TemplatePlugin{

    public $arguments;
    public $content;
    
    public function __construct($content,$arguments){
        $this->arguments = $arguments;
    }
}
?>