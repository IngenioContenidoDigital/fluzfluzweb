<?php

class confirmtransfergiftControllerCore extends FrontController
{
    public $php_self = 'confirmtransfergift';
    public $authRedirection = 'confirmtransfergift';
    public $ssl = true;

    public function postProcess()
    {

    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'confirmtransferfluz.css');
    }

    public function initContent()
    {
        parent::initContent();
        
        $this->setTemplate(_PS_THEME_DIR_.'confirmtransfergift.tpl');
    }
}
