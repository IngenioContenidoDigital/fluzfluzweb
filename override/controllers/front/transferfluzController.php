<?php

require_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsModel.php');

class transferfluzController extends FrontController{

    public $auth = true;
    public $php_self = 'transferfluz';
    public $authRedirection = 'transferfluz';
    public $ssl = true;
      
    
      public function setMedia(){
         parent::setMedia();
         $this->addCSS(_THEME_CSS_DIR_.'transferfluz.css');
      }

      public function initContent()
	{
		parent::initContent();

                $this->setTemplate(_PS_THEME_DIR_.'transferfluz.tpl');
	}
      
}
