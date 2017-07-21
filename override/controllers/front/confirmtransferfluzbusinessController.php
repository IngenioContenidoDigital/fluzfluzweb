<?php

class confirmtransferfluzbusinessControllerCore extends FrontController
{
    public $php_self = 'confirmtransferfluzbusiness';
    public $authRedirection = 'confirmtransferfluzbusiness';
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
        
        $smarty_values = array(
            'popup' => ( Tools::getValue("popup") != "" ) ? Tools::getValue("popup") : false,
        );
        $this->context->smarty->assign($smarty_values);
        
        $this->setTemplate(_PS_THEME_DIR_.'confirmtransferfluzbusiness.tpl');
    }
}
