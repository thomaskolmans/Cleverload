<?php
namespace lib\template\plugins;

use lib\template\TemplatePlugin;

class TPlugin_foreach extends TemplatePlugin{

    public $arguments;

    public function __construct($arguments){
        $this->arguments = $arguments;
    }
}
?>
