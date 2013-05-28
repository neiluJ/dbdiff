<?php
namespace DbDiff\Actions;

use Fwk\Core\Preparable;

class Home implements Preparable
{
    protected $form;
    protected $renderer;
    
    public function prepare()
    {
        $this->renderer = new \Fwk\Form\Renderer();
    }
    
    public function show()
    {
        if ($_SERVER['REQUEST_METHOD'] !== "POST") {
           return "success";
        }
        
        $form = $this->getForm();
        $form->submit($_POST);
        
        if (!$form->validate()) {
            return "success";
        }
    }
    
    public function getForm() {
        if (!isset($this->form)) {
            $this->form = new \DbDiff\DbConnectionForm();
        }
        
        return $this->form;
    }

    public function getRenderer() {
        return $this->renderer;
    }
}