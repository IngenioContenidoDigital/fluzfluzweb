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
                
                $name_member = Tools::getValue("name_member");
                $this->context->smarty->assign('name_member', $name_member);
                
                $id_member = Tools::getValue("id_member");
                $this->context->smarty->assign('id_member', $id_member);
                
                $smarty_values = array(
                    'popup' => ( Tools::getValue("popup") != "" ) ? Tools::getValue("popup") : false,
                );
                $this->context->smarty->assign($smarty_values);
               
                $this->setTemplate(_PS_THEME_DIR_.'transferfluz.tpl');
	}
        
    public function postProcess() {
        switch ( Tools::getValue('action') ) {
            case 'transferfluz':
                $point_send = Tools::getValue('point_part');
                $id_sponsor = Tools::getValue('sponsor_identification');
                
                Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."transfers_fluz (id_customer, id_sponsor_received, date_add)
                                            VALUES (".(int)$this->context->customer->id.", ".(int)$id_sponsor.",'".date("Y-m-d H:i:s")."')");
                
                $query_t = 'SELECT id_transfers_fluz FROM '._DB_PREFIX_.'transfers_fluz WHERE id_customer='.(int)$this->context->customer->id.' ORDER BY id_transfers_fluz DESC';
                $row_t = Db::getInstance()->getRow($query_t);
                $id_transfer = $row_t['id_transfers_fluz'];
                
                Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                                    . "                          VALUES ('2', ".(int)$this->context->customer->id.", 0,NULL,'0','0',".-1*$point_send.",'loyalty', 'TransferFluz','".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', ".(int)$id_transfer.")");
                
                Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                                    . "                          VALUES ('2', ".(int)$id_sponsor.", 0,NULL,'0','0',".$point_send.",'loyalty','TransferFluz','".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', ".(int)$id_transfer.")");
                
                break;
            default:
                break;
        }
    }
        
}
