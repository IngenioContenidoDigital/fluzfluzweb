<?php

class cancelinvitationControllerCore extends FrontController
{
    public $php_self = 'cancelinvitation';
    public $authRedirection = 'cancelinvitation';
    public $ssl = true;

    public function postProcess()
    {

    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'cancelinvitations.css');
    }

    public function initContent()
    {
        parent::initContent();

        $this->setTemplate(_PS_THEME_DIR_.'cancelinvitations.tpl');
    }
}