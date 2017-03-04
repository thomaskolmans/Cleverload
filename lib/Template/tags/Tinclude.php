<?php

namespace lib\Template\tags;

use lib\Template\TemplateTag;

class Tinclude extends TemplateTag{

    public function execute($node){
        $filepath = trim($node->nodeValue);
        if(file_exists($filepath)){
            $contents = file_get_contents($filepath);
            $contents = $this->extractPHP($contents);
            $node->nodeValue = $contents;
        }
        return $node;
    }
}  
?>