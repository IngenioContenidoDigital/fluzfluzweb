<?php
/**
 * All-in-one Rewards Module
 *
 * @category  Prestashop
 * @category  Module
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2015 Yann BONNAILLIE - ByWEB (http://www.prestaplugins.com)
 * @license   Commercial license see license.txt
 * Support by mail  : contact@prestaplugins.com
 * Support on forum : Patanock
 * Support on Skype : Patanock13
 */

class Allinone_rewardsRewardsModuleFrontController extends ModuleFrontController
{
	public function init()
	{
		if (!$this->context->customer->isLogged())
			Tools::redirect('index.php?controller=authentication');
		parent::init();
	}
        
        public function setMedia()
	{
		parent::setMedia();
		if (!Tools::getValue('checksponsor')) {
			$this->addJqueryPlugin(array('idTabs'));
		}
                $this->addJS(array(
            
                _THEME_JS_DIR_.'authentication.js',
                _PS_JS_DIR_.'jquery/plugins/jquery.creditCardValidator.js',
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
		$totalPending = (isset($totals[RewardsStateModel::getDefaultId()]) ? (float)$totals[RewardsStateModel::getDefaultId()] : 0) + (isset($totals[RewardsStateModel::getReturnPeriodId()]) ? $totals[RewardsStateModel::getReturnPeriodId()] : 0);
		$totalWaitingPayment = isset($totals[RewardsStateModel::getWaitingPaymentId()]) ? (float)$totals[RewardsStateModel::getWaitingPaymentId()] : 0;
		$totalPaid = isset($totals[RewardsStateModel::getPaidId()]) ? (float)$totals[RewardsStateModel::getPaidId()] : 0;
		$totalForPaymentDefaultCurrency = round($totalAvailable * MyConf::get('REWARDS_PAYMENT_RATIO', null, $id_template) / 100, 2);
                
                $totalAvailableCurrency=RewardsModel::getmoneyReadyForDisplay($totalAvailableCurrency,(int)$this->context->currency->id);
                $this->context->smarty->assign('totalAvailable', $totalAvailable);
                $this->context->smarty->assign('totalAvailableCurrency', $totalAvailableCurrency);
		$currency = Currency::getCurrency((int)$this->context->currency->id);
		$totalAvailableUserCurrency = Tools::convertPrice($totalAvailable, $currency);
		$voucherMininum = (float)MyConf::get('REWARDS_VOUCHER_MIN_VALUE_'.(int)$this->context->currency->id, null, $id_template) > 0 ? (float)MyConf::get('REWARDS_VOUCHER_MIN_VALUE_'.(int)$this->context->currency->id, null, $id_template) : 0;
		$paymentMininum = (float)MyConf::get('REWARDS_PAYMENT_MIN_VALUE_'.(int)$this->context->currency->id, null, $id_template) > 0 ? (float)MyConf::get('REWARDS_PAYMENT_MIN_VALUE_'.(int)$this->context->currency->id, null, $id_template) : 0;

		$voucherAllowed = RewardsModel::isCustomerAllowedForVoucher((int)$this->context->customer->id);
		$paymentAllowed = RewardsModel::isCustomerAllowedForPayment((int)$this->context->customer->id);
                
                $ajax=Tools::getValue('ajax');
                
                if($_GET['transform-credits'] == 'true' && $_GET['ajax'] == 'true'){
                    $money=$_GET['credits'];
                    $cartValue=$_GET['price'];
                    $points=$_GET['points'];
                    $use=$_GET['use'];
                     
                    if($points>0){
                        
                       if($use>0 && $use<=$points){
                            $money= RewardsModel::getMoneyReadyForDisplay($use, (int)$this->context->currency->id);
                       }else{
                           $cartpoints=RewardsModel::getRewardReadyForDisplay($cartValue, (int)$this->context->currency->id);
                           if($points>=$cartpoints){
                              $money= RewardsModel::getMoneyReadyForDisplay($cartpoints, (int)$this->context->currency->id);
                           }else{
                              $money= RewardsModel::getMoneyReadyForDisplay($points, (int)$this->context->currency->id);
                           }
                           
                       }
                       
                       
                       
                        $response=RewardsModel::createDiscount($money);
                        $realmoney= RewardsModel::getMoneyReadyForDisplay($points, (int)$this->context->currency->id);
                        
                        if($money<$realmoney){
                            
                            $rs="SELECT "._DB_PREFIX_."rewards.id_reward AS last_reward, 
                            "._DB_PREFIX_."rewards.id_customer,
                            "._DB_PREFIX_."rewards.id_order,
                            "._DB_PREFIX_."rewards.credits
                            FROM "._DB_PREFIX_."rewards
                            WHERE "._DB_PREFIX_."rewards.id_customer=".(int)$this->context->customer->id." ORDER BY "._DB_PREFIX_."rewards.id_reward DESC";
                            
                            if ($row = Db::getInstance()->getRow($rs)){
                                $rw = $row['last_reward'];
                            }
                            
                            $query = "UPDATE "._DB_PREFIX_."rewards AS R SET R.id_reward_state=2, R.credits=".($points-$use)." WHERE R.id_reward=".$rw;
                            Db::getInstance()->execute($query);
                        }
                       
                        echo $response;
                    }
                    exit;
                }
                
                $queryMax = 'SELECT MAX(n.credits) AS points, c.firstname AS name 
                    FROM ps_rewards n 
                    LEFT JOIN ps_customer c ON c.id_customer = n.id_customer';
                $rowMax = Db::getInstance()->getRow($queryMax);
                $pointMax = $rowMax['points'];
                $nameMax = $rowMax['name'];
                $this->context->smarty->assign('pointMax', $pointMax);
                $this->context->smarty->assign('nameMax', $nameMax);
                
                $queryMin = 'SELECT MIN(n.credits) AS points, c.firstname AS name 
                    FROM ps_rewards n 
                    LEFT JOIN ps_customer c ON c.id_customer = n.id_customer ';
                $rowMin = Db::getInstance()->getRow($queryMin);
                $pointMin = $rowMin['points'];
                $nameMin = $rowMin['name'];
                $this->context->smarty->assign('pointMin', $pointMin);
                $this->context->smarty->assign('nameMin', $nameMin);

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
			if (Tools::getValue('payment_details') && (!MyConf::get('REWARDS_PAYMENT_INVOICE', null, $id_template) || (isset($_FILES['payment_invoice']['name']) && !empty($_FILES['payment_invoice']['tmp_name'])))) {
				if (RewardsPaymentModel::askForPayment($totalForPaymentDefaultCurrency, Tools::getValue('payment_details'), $_FILES['payment_invoice']))
					Tools::redirect($this->context->link->getModuleLink('allinone_rewards', 'rewards', array(), true));
				else
					$this->context->smarty->assign('payment_error', 2);
			} else
				$this->context->smarty->assign('payment_error', 1);
		}
                
                
                $datosGraph = 'SELECT  o.credits as point FROM ps_rewards as o
                    WHERE SUBSTRING(o.date_add FROM 1 FOR 7) =  SUBSTRING(CURRENT_date - INTERVAL 1 MONTH FROM 1 FOR 7) LIMIT 0,4';
                $result=Db::getInstance()->executeS($datosGraph);
                $graph=array();
                foreach($result as $x){
                    array_push($graph,$x['point']);
                }
                
                $serie=array('Week 4','Week 3','Week 2','This Week');
                
		$link = $this->context->link->getModuleLink('allinone_rewards', 'rewards', array(), true);
		$rewards = RewardsModel::getAllByIdCustomer((int)$this->context->customer->id);
		$displayrewards = RewardsModel::getAllByIdCustomer((int)$this->context->customer->id, false, false, true, ((int)(Tools::getValue('n')) > 0 ? (int)(Tools::getValue('n')) : 10), ((int)(Tools::getValue('p')) > 0 ? (int)(Tools::getValue('p')) : 1), $this->context->currency->id, true);
                $activityRecent = $this->recentActivity(false, true, ((int)(Tools::getValue('n')) > 0 ? (int)(Tools::getValue('n')) : 10), ((int)(Tools::getValue('p')) > 0 ? (int)(Tools::getValue('p')) : 1));
                
		$this->context->smarty->assign(array(
			'return_days' => (Configuration::get('REWARDS_WAIT_RETURN_PERIOD') && Configuration::get('PS_ORDER_RETURN') && (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS') > 0) ? (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS') : 0,
			'rewards_duration' => (int)Configuration::get('REWARDS_DURATION'),
			'rewards' => $rewards,
                        'activityRecent' => $activityRecent,
                        'arrayGraph'=> $graph,
                        'arraySeries'=> $serie,
			'displayrewards' => $displayrewards,
			'pagination_link' => $link . (strpos($link, '?') !== false ? '&' : '?'),
                        'topNetwork'=> $this->TopNetwork(),
                        'topWorst'=> $this->TopWorst(),
                        'activityNet'=> $this->recentActivity(),
			'totalGlobal' => $this->module->getRewardReadyForDisplay($totalGlobal, (int)$this->context->currency->id),
			'totalConverted' => $this->module->getRewardReadyForDisplay($totalConverted, (int)$this->context->currency->id),
			'totalAvailable' => $this->module->getRewardReadyForDisplay($totalAvailable, (int)$this->context->currency->id),
			'totalAvailableCurrency' => round(Tools::convertPrice($totalAvailable, $currency), 2),
			'totalPending' => $this->module->getRewardReadyForDisplay($totalPending, (int)$this->context->currency->id),
			'totalWaitingPayment' => $this->module->getRewardReadyForDisplay($totalWaitingPayment, (int)$this->context->currency->id),
			'totalPaid' => $this->module->getRewardReadyForDisplay($totalPaid, (int)$this->context->currency->id),
			'convertColumns' => ($voucherAllowed || $totalConverted > 0) ? true : false,
			'paymentColumns' => ($paymentAllowed || $totalPaid > 0 || $totalWaitingPayment > 0) ? true : false,
			'totalForPaymentDefaultCurrency' => $totalForPaymentDefaultCurrency,
			'payment_currency' => Configuration::get('PS_CURRENCY_DEFAULT'),
			'voucherMinimum' => $this->module->getRewardReadyForDisplay($voucherMininum, (int)$this->context->currency->id),
			'voucher_minimum_allowed' => $voucherAllowed && $voucherMininum > 0 ? true : false,
			'voucher_button_allowed' => $voucherAllowed && $totalAvailableUserCurrency >= $voucherMininum && $totalAvailableUserCurrency > 0,
			'paymentMinimum' => $this->module->getRewardReadyForDisplay($paymentMininum, (int)$this->context->currency->id),
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
		$this->setTemplate('rewards.tpl');
	}
        
        public function TopNetwork() {
            
            $queryTop = 'SELECT c.username AS username, c.firstname AS name, s.product_name AS purchase, n.credits AS points,  n.date_add AS time FROM '._DB_PREFIX_.'rewards n 
                          LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = n.id_customer) 
                          LEFT JOIN '._DB_PREFIX_.'order_detail s ON (s.id_order = n.id_order) ORDER BY n.credits DESC LIMIT 0,5';
          
            $top=Db::getInstance()->executeS($queryTop);
            
            return $top;    
            
        }
        
        public function TopWorst() {
            
            $queryWorst = 'SELECT c.username AS username, c.firstname AS name, s.product_name AS purchase, n.credits AS points, n.date_add AS time FROM '._DB_PREFIX_.'rewards n 
                          LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = n.id_customer) 
                          LEFT JOIN '._DB_PREFIX_.'order_detail s ON (s.id_order = n.id_order) ORDER BY n.credits ASC LIMIT 0,5';
                
            $worst=Db::getInstance()->executeS($queryWorst);
            return $worst;    
            
        }
        
        public function recentActivity($onlyValidate = false,$pagination = false, $nb = 10, $page = 1) {
            
            $query = 'SELECT c.username AS username, c.firstname AS name, s.product_name AS purchase, n.credits AS points, n.date_add AS time FROM '._DB_PREFIX_.'rewards n 
                          LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = n.id_customer) 
                          LEFT JOIN '._DB_PREFIX_.'order_detail s ON (s.id_order = n.id_order) WHERE n.id_customer='.(int)$this->context->customer->id;
            if ($onlyValidate === true)
		$query .= ' AND n.id_reward_state = '.(int)RewardsStateModel::getValidationId();
		$query .= ' GROUP BY n.id_reward ORDER BY n.date_add DESC '.
		($pagination ? 'LIMIT '.(((int)($page) - 1) * (int)($nb)).', '.(int)$nb : '');
             
            $activity=Db::getInstance()->executeS($query);
            return $activity;
            
        }
}