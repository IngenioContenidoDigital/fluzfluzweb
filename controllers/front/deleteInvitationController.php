<?php

class deleteInvitationControllerCore extends FrontController
{
    public $php_self = 'deleteInvitation';
    public $authRedirection = 'deleteInvitation';
    public $ssl = true;

    public function postProcess()
    {

    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'deleteInvitation.css');
    }

    public function initContent()
    {
        parent::initContent();

        $this->setTemplate(_PS_THEME_DIR_.'invitationDelete.tpl');
    }
}