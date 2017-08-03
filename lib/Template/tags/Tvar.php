<?php

namespace lib\Template\tags;

use lib\Template\TemplateTag;

class Tvar extends TemplateTag{

    public function execute($node){
        $variable = $node->nodeValue;
        
    }
}  
?>