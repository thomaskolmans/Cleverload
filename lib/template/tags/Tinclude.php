<?php

namespace lib\template\tags;

use lib\template\TemplateTag;
use lib\template\Template;
use lib\Cleverload;

class Tinclude extends TemplateTag {

    public function execute($node){
        $filepath = trim($node->nodeValue);
        $filepath = Cleverload::getInstance()->getViewDir()."/".$filepath;
        if(file_exists($filepath)){
            $contents = file_get_contents($filepath);
            $dom = new \DOMDocument();
            $dom->loadHTML($contents);

            $template = new Template($dom->saveHTML());
            $node->nodeValue = $template->load();
        }
        return $node;
    }
}  
?>