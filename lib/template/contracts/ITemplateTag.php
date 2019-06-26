<?php
namespace lib\Template\contracts;

interface ITemplateTag{

    public function execute($node);
    public function find();
    /*getters and setters */

    public function getTag();
    public function setTag($tag);
}
?>