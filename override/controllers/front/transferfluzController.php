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
               
                $this->setTemplate(_PS_THEME_DIR_.'transferfluz.tpl');
	}
        
    public function postProcess() {
        switch ( Tools::getValue('action') ) {
            case 'transferfluz':
                $point_send = Tools::getValue('point_part');
                $id_sponsor = Tools::getValue('sponsor_identification');
                
                $query_sponsor = "INSERT INTO "._DB_PREFIX_."rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, date_add, date_upd)"
                                    . "                          VALUES ('2', ".(int)$this->context->customer->id.", 0,NULL,'0','0',".-1*$point_send.",'loyalty','".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."')";
                Db::getInstance()->execute($query_sponsor);
                
                $query_sponsor = "INSERT INTO "._DB_PREFIX_."rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, date_add, date_upd)"
                                    . "                          VALUES ('2', ".(int)$id_sponsor.", 0,NULL,'0','0',".$point_send.",'loyalty','".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."')";
                Db::getInstance()->execute($query_sponsor);
                break;
            default:
                break;
        }
    }
        
}
