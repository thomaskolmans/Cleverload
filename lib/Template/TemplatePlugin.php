<?php
namespace lib\Template;

use lib\Template\plugins\PluginCollection;
use lib\Template\contracts\ITemplatePlugin;

abstract class TemplatePlugin extends PluginCollection implements ITemplatePlugin{
    
    public $code;

    public function setCode($code){
        $this->code = $code;
        return $this;
    }
}