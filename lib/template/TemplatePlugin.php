<?php
namespace lib\template;

use lib\template\plugins\PluginCollection;
use lib\template\contracts\ITemplatePlugin;

abstract class TemplatePlugin extends PluginCollection implements ITemplatePlugin{
    
    public $code;

    public function setCode($code){
        $this->code = $code;
        return $this;
    }
}