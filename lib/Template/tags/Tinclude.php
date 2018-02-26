<?php

namespace lib\Template\tags;

use lib\Template\TemplateTag;
use lib\Cleverload;

class Tinclude extends TemplateTag{

    public function execute($node){
        $filepath = trim($node->nodeValue);
        $filepath = Cleverload::getInstance()->getViewDir()."/".$filepath;
        if(file_exists($filepath)){
            $contents = file_get_contents($filepath);
            $dom = new \DOMDocument();
            $dom->loadHTML($contents);
            $node->nodeValue = $dom->saveHTML();
        }
        return $node;
    }
}  
?>