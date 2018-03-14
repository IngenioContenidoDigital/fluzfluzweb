<?php

class calculatorFluzController extends FrontController {
    
    public $php_self = 'calculatorfluz';
    public $authRedirection = 'calculatorfluz';
    public $ssl = true;
    
    public function setMedia()
    {
        FrontController::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'calculatorFluz.css');
    }
    
    public function initContent() {
        parent::initContent();
        
        $this->setTemplate(_PS_THEME_DIR_.'calculatorFluzfluz.tpl');
    }
}
