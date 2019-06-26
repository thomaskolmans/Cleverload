<?php

namespace lib\template\tags;

use lib\template\TemplateTag;

class Tvar extends TemplateTag{

    public function execute($node){
        $variable = $node->nodeValue;
    }
}  
?>