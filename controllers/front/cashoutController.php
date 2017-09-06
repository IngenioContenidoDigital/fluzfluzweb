<?php
require_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsModel.php');
class cashoutControllerCore extends FrontController{

    public $auth = true;
    public $php_self = 'cashout';
    public $authRedirection = 'cashout';
    public $ssl = true;
      
    
      public function setMedia(){
         parent::setMedia();
         $this->addCSS(_THEME_CSS_DIR_.'cashout.css');
         $this->addJS(array(
            _PS_JS_DIR_.'js/jquery/plugins/jquery.creditCardValidator.js',
            _PS_JS_DIR_.'js/validate.js'
        ));
         
      }

      public function initContent()
	{
		parent::initContent();

		$id_template = (int)MyConf::getIdTemplate('core', $this->context->customer->id);
                $popup = Tools::getValue('popup');
                
		// récupère le nombre de crédits convertibles
		$totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
                $totalGlobal = round(isset($totals['total']) ? (float)$totals['total'] : 0);
		$totalConverted = isset($totals[RewardsStateModel::getConvertId()]) ? (float)$totals[RewardsStateModel::getConvertId()] : 0;
                $totalAvailable = round(isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0);
                $pago = round(RewardsModel::getMoneyReadyForDisplay($totalAvailable, (int)$this->context->currency->id));
		$totalPending = (isset($totals[RewardsStateModel::getDefaultId()]) ? (float)$totals[RewardsStateModel::getDefaultId()] : 0) + (isset($totals[RewardsStateModel::getReturnPeriodId()]) ? $totals[RewardsStateModel::getReturnPeriodId()] : 0);
		$totalWaitingPayment = isset($totals[RewardsStateModel::getWaitingPaymentId()]) ? (float)$totals[RewardsStateModel::getWaitingPaymentId()] : 0;
		$totalPaid = isset($totals[RewardsStateModel::getPaidId()]) ? (float)$totals[RewardsStateModel::getPaidId()] : 0;
		$totalForPaymentDefaultCurrency = round($totalAvailable * MyConf::get('REWARDS_PAYMENT_RATIO', null, $id_template) / 100, 2);
                
                $totalAvailableCurrency=RewardsModel::getmoneyReadyForDisplay($totalAvailableCurrency,(int)$this->context->currency->id);
                $this->context->smarty->assign('totalAvailable', $totalAvailable);
                $this->context->smarty->assign('totalAvailableCurrency', $totalAvailableCurrency);
		$currency = Currency::getCurrency((int)$this->context->currency->id);
                
                $costoTransferencia = Tools::convertPrice(7000);
                $this->context->smarty->assign('costoTransferencia', $costoTransferencia);
                
		$totalAvailableUserCurrency = Tools::convertPrice($totalAvailable, $currency);
		$voucherMininum = (float)MyConf::get('REWARDS_VOUCHER_MIN_VALUE_'.(int)$this->context->currency->id, null, $id_template) > 0 ? (float)MyConf::get('REWARDS_VOUCHER_MIN_VALUE_'.(int)$this->context->currency->id, null, $id_template) : 0;
		$paymentMininum = (float)MyConf::get('REWARDS_PAYMENT_MIN_VALUE_'.(int)$this->context->currency->id, null, $id_template) > 0 ? (float)MyConf::get('REWARDS_PAYMENT_MIN_VALUE_'.(int)$this->context->currency->id, null, $id_template) : 0;

		$voucherAllowed = RewardsModel::isCustomerAllowedForVoucher((int)$this->context->customer->id);
		$paymentAllowed = RewardsModel::isCustomerAllowedForPayment((int)$this->context->customer->id);
                
		/* transform credits into voucher if needed */
		if ($voucherAllowed && Tools::getValue('transform-credits') == 'true' && $totalAvailableUserCurrency >= $voucherMininum && Tools::getValue('ajax') == 'false')
		{
			RewardsModel::createDiscount($totalAvailable);
			//Tools::redirect($this->context->link->getModuleLink('allinone_rewards', 'rewards', array(), true));
			Tools::redirect($this->context->link->getPageLink('discount', true));
                        
		}
                
                if (!$popup) {
				$statistics = RewardsSponsorshipModel::getStatistics(true);
				$reward_order_allowed = (int)MyConf::get('RSPONSORSHIP_REWARD_ORDER', null, $id_template) || ($statistics['direct_rewards_orders1']+$statistics['direct_rewards_orders2']+$statistics['direct_rewards_orders3']+$statistics['direct_rewards_orders4']+$statistics['direct_rewards_orders5']+$statistics['indirect_rewards']) > 0;
				$reward_registration_allowed = (int)MyConf::get('RSPONSORSHIP_REWARD_REGISTRATION', null, $id_template) || ($statistics['direct_rewards_registrations1']+$statistics['direct_rewards_registrations2']+$statistics['direct_rewards_registrations3']+$statistics['direct_rewards_registrations4']+$statistics['direct_rewards_registrations5']) > 0;

				$params_s = explode(',', MyConf::get('RSPONSORSHIP_REWARD_TYPE_S', null, $id_template));
				$multilevel = count($params_s) > 1 || MyConf::get('RSPONSORSHIP_UNLIMITED_LEVELS', null, $id_template) || (float)$statistics['indirect_rewards'] > 0;
				$smarty_values = array(
					'statistics' => $statistics,
					'reward_order_allowed' => $reward_order_allowed,
					'reward_registration_allowed' => $reward_registration_allowed,
					'multilevel' => $multilevel
				);
				$this->context->smarty->assign($smarty_values);
			}
               
		if ($paymentAllowed && Tools::isSubmit('submitPayment') && $totalAvailableUserCurrency >= $paymentMininum && $totalForPaymentDefaultCurrency > 0) {
                    
                    
                    $name = Tools::getValue('nombre-customer');
                    $lastname = Tools::getValue('lastname-customer');
                    $num = Tools::getValue('numero_tarjeta');
                    $bank = Tools::getValue('bank_cash');
                    $bank_account = Tools::getValue('bank_account');
                    $nit_cedula = Tools::getValue('nit_cedula');
                    $point_used = Tools::getValue('pt_parciales');
                    $point_total = Tools::getValue('pto_total');
                    
                    $validacion = Tools::getValue('radio');
                    $pago_parcial = round(RewardsModel::getMoneyReadyForDisplay($point_used, (int)$this->context->currency->id));
                    
                    $mailVars = array(
                    '{username}'=> $this->context->customer->username,
                    '{nombre_del_solicitante}' => $name,
                    '{numero_de_cuenta}'=> $num,
                    '{tipo_de_cuenta}' => $bank_account,
                    '{nombre_del_banco}' => $bank,
                    '{fluz_utilizados}' => $point_used,
                    '{cantidad_a_pagar}' => Tools::displayPrice($pago_parcial, 1, false),
                    );
                    
                    $email = $this->context->customer->email;
                    
                    $template = 'cashout_conf';
                    $prefix_template = '16-cashout_conf';

                    $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
                    $row_subject = Db::getInstance()->getRow($query_subject);
                    $message_subject = $row_subject['subject_mail'];

                    $allinone_rewards = new allinone_rewards();
                    
                    if($validacion == 0){
                        
                        $query2 = "INSERT INTO "._DB_PREFIX_."rewards_payment (nit_cedula, id_customer, nombre, apellido, numero_tarjeta,tipo_cuenta, banco, points, credits, detail, invoice, paid, date_add, date_upd)"
                                    . "                          VALUES ('".$nit_cedula."',".(int)$this->context->customer->id.",'".$name."','".$lastname."','\'".$num."','".$bank_account."','".$bank."',".(int)$point_total.",".((int)$pago-7000).",'0','0',".(-1*$pago+7000).",'".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s")."')";
                        Db::getInstance()->execute($query2);
                        
                        $id_cashout = Db::getInstance()->getRow('SELECT id_rewards_payment FROM ps_rewards_payment 
                                                                 WHERE id_customer='.(int)$this->context->customer->id.' ORDER BY date_add DESC');
                        
                        $query1 = "INSERT INTO "._DB_PREFIX_."rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_cashout)"
                                    . "                          VALUES ('2', ".(int)$this->context->customer->id.", 0,".(int)$this->context->cart->id.",'0','0',".-1*$totalForPaymentDefaultCurrency.",'loyalty','cashout','".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."',".$id_cashout['id_rewards_payment']." )";
                        Db::getInstance()->execute($query1);
                        
                        $allinone_rewards->sendMail(1, $template, $allinone_rewards->getL($message_subject), $mailVars, $email, $this->context->customer->firstname.' '.$this->context->customer->lastname);
                        Tools::redirect($this->context->link->getPageLink('my-account', true));
                    }
                    else if ($validacion == 1){
                        
                        $query2 = "INSERT INTO "._DB_PREFIX_."rewards_payment (nit_cedula,id_customer, nombre, apellido, numero_tarjeta,tipo_cuenta, banco, points, credits, detail, invoice, paid, date_add, date_upd)"
                                    . "                          VALUES ('".$nit_cedula."',".(int)$this->context->customer->id.",'".$name."' ,'".$lastname."','\'".$num."','".$bank_account."','".$bank."',".(int)$point_used.",".((int)$pago_parcial-7000).",'0','0',".(-1*$pago_parcial+7000).",'".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s")."')";
                        Db::getInstance()->execute($query2);
                        
                        $id_cashout = Db::getInstance()->getRow('SELECT id_rewards_payment FROM '._DB_PREFIX_.'rewards_payment 
                                                                 WHERE id_customer='.(int)$this->context->customer->id.' ORDER BY date_add DESC');
                        
                        $query1 = "INSERT INTO "._DB_PREFIX_."rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd,id_cashout)"
                                    . "                          VALUES ('2', ".(int)$this->context->customer->id.", 0,".(int)$this->context->cart->id.",'0','0',".-1*$point_used.",'loyalty','cashout','".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."',".$id_cashout['id_rewards_payment'].")";
                        Db::getInstance()->execute($query1);
                        
                        $allinone_rewards->sendMail(1, $template, $allinone_rewards->getL($message_subject), $mailVars, $email, $this->context->customer->firstname.' '.$this->context->customer->lastname);
                        Tools::redirect($this->context->link->getPageLink('my-account', true));}
		}
                
		$link = $this->context->link->getModuleLink('allinone_rewards', 'rewards', array(), true);
		$rewards = RewardsModel::getAllByIdCustomer((int)$this->context->customer->id);
		$displayrewards = RewardsModel::getAllByIdCustomer((int)$this->context->customer->id, false, false, true, ((int)(Tools::getValue('n')) > 0 ? (int)(Tools::getValue('n')) : 10), ((int)(Tools::getValue('p')) > 0 ? (int)(Tools::getValue('p')) : 1), $this->context->currency->id, true);
                
		$this->context->smarty->assign(array(
			'return_days' => (Configuration::get('REWARDS_WAIT_RETURN_PERIOD') && Configuration::get('PS_ORDER_RETURN') && (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS') > 0) ? (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS') : 0,
			'rewards_duration' => (int)Configuration::get('REWARDS_DURATION'),
			'rewards' => $rewards,
                        'pago'=>$pago,
                        'displayrewards' => $displayrewards,
			'pagination_link' => $link . (strpos($link, '?') !== false ? '&' : '?'),
                        'totalGlobal' => RewardsModel::getRewardReadyForDisplay($totalGlobal, (int)$this->context->currency->id),
			'totalConverted' => RewardsModel::getRewardReadyForDisplay($totalConverted, (int)$this->context->currency->id),
			'totalAvailable' =>$totalAvailable,
			'totalAvailableCurrency' => round(Tools::convertPrice($totalAvailable, $currency), 2),
			'totalPending' => RewardsModel::getRewardReadyForDisplay($totalPending, (int)$this->context->currency->id),
			'totalWaitingPayment' => RewardsModel::getRewardReadyForDisplay($totalWaitingPayment, (int)$this->context->currency->id),
			'totalPaid' => RewardsModel::getRewardReadyForDisplay($totalPaid, (int)$this->context->currency->id),
			'convertColumns' => ($voucherAllowed || $totalConverted > 0) ? true : false,
			'paymentColumns' => ($paymentAllowed || $totalPaid > 0 || $totalWaitingPayment > 0) ? true : false,
			'totalForPaymentDefaultCurrency' => $totalForPaymentDefaultCurrency,
			'payment_currency' => Configuration::get('PS_CURRENCY_DEFAULT'),
			'voucherMinimum' => RewardsModel::getRewardReadyForDisplay($voucherMininum, (int)$this->context->currency->id),
			'voucher_minimum_allowed' => $voucherAllowed && $voucherMininum > 0 ? true : false,
			'voucher_button_allowed' => $voucherAllowed && $totalAvailableUserCurrency >= $voucherMininum && $totalAvailableUserCurrency > 0,
			'paymentMinimum' => RewardsModel::getRewardReadyForDisplay($paymentMininum, (int)$this->context->currency->id),
			'payment_minimum_allowed' => $paymentAllowed && $paymentMininum > 0 ? true : false,
			'payment_button_allowed' => $paymentAllowed && $totalAvailableUserCurrency >= $paymentMininum && $totalForPaymentDefaultCurrency > 0,
			'payment_txt' => MyConf::get('REWARDS_PAYMENT_TXT', (int)$this->context->language->id, $id_template),
			'general_txt' => MyConf::get('REWARDS_GENERAL_TXT', (int)$this->context->language->id, $id_template),
			'payment_details' => Tools::getValue('payment_details'),
			'payment_invoice' => (int)MyConf::get('REWARDS_PAYMENT_INVOICE', null, $id_template),
			'page' => ((int)(Tools::getValue('p')) > 0 ? (int)(Tools::getValue('p')) : 1),
			'nbpagination' => ((int)(Tools::getValue('n') > 0) ? (int)(Tools::getValue('n')) : 10),
			'nArray' => array(10, 20, 50),
			'max_page' => floor(sizeof($rewards) / ((int)(Tools::getValue('n') > 0) ? (int)(Tools::getValue('n')) : 10))
		));
                
                $this->setTemplate(_PS_THEME_DIR_.'cashout.tpl');
	}
      
}
