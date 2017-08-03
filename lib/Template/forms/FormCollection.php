<?php
namespace lib\Tempalte;

class FormCollection{
    
    public $forms = array()

    public function __construct($forms = array()){
        $this->forms = $forms;
    }
    public function getforms(){
        return $this->forms;
    }
    public function setforms($forms){
        $this->forms = $forms;
        return $this;
    }
    public function addName($form){
        $this->forms[] = $form;
        return $this;
    }
}