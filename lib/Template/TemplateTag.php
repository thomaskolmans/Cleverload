<?php

namespace lib\Template;

use lib\Template\Template;
use lib\Template\contracts\ITemplateTag;

abstract class TemplateTag extends Template implements ITemplateTag{

    public $tag;
    public $dom;

    public $taglist = array();

    public function __construct($dom){
        $this->dom = $dom;
        $this->setTag(get_called_class());
        $this->find();
    }
    public function find(){
        $tags = $this->dom->getElementsByTagName($this->tag);
        $this->taglist = $tags;
        if ($tags !== null) {
            foreach ($tags as $tag) {
                $this->execute($tag);
            }  
        }
        $this->dom->saveHTML();
    }
    public function setTag($tag){
        $tags = explode("\\",$tag);
        $tag = end($tags);
        $this->tag = substr($tag,1);
        return $this;
    }
    public function getTag(){
        return $this->tag;
    }
}
?>