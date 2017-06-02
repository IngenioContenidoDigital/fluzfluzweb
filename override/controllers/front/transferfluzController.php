<?php

include_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsModel.php');


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
                
                $username = $this->context->customer->username;
                $this->context->smarty->assign('username', $username);
                
                $id_customer = $this->context->customer->id;
                $this->context->smarty->assign('id_customer', $id_customer);

                $totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
                $pointsAvailable = round(isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0);
                $this->context->smarty->assign('pointsAvailable', $pointsAvailable);
                
                if(Tools::isSubmit('submitFluz')){
                    
                    $point_send = Tools::getValue('pt_parciales');
                    
                }
                
                $this->setTemplate(_PS_THEME_DIR_.'transferfluz.tpl');
	}
        
}
