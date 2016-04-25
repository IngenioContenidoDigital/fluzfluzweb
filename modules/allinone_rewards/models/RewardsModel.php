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

if (!defined('_PS_VERSION_'))
	exit;

require_once(dirname(__FILE__).'/RewardsAccountModel.php');

class RewardsModel extends ObjectModel
{
	public $id_reward_state;
	public $id_customer;
	public $id_order;
	public $id_cart_rule;
	public $id_payment;
	public $credits;
        public $points;
	public $plugin;
	public $reason;
	public $date_add;
	public $date_upd;

	public static $definition = array(
		'table' => 'rewards',
		'primary' => 'id_reward',
		'fields' => array(
			'id_reward_state' =>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_customer' =>		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_cart_rule' =>		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_payment' =>			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_order' =>			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'credits' =>			array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
			'plugin' =>				array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 20),
			'reason' =>				array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 80),
			'date_add' =>			array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' =>			array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		)
	);

	public function save($historize = true, $nullValues = false, $autodate = true)
	{
		if (parent::save($nullValues, $autodate)) {
			// create the account first time a reward is created for that customer
			$rewardsAccount = new RewardsAccountModel($this->id_customer);
			if (!Validate::isLoadedObject($rewardsAccount)) {
				$rewardsAccount->id_customer = $this->id_customer;
				$rewardsAccount->save();
			}

			if ($historize)
				$this->historize();
			return true;
		}
		return false;
	}

	public static function isNotEmpty() {
		Db::getInstance()->ExecuteS('SELECT 1 FROM `'._DB_PREFIX_.'rewards`');
		return (bool)Db::getInstance()->NumRows();
	}

	public static function importFromLoyalty() {
		$pointValue = (float)Configuration::get('PS_LOYALTY_POINT_VALUE');
		if ($pointValue > 0) {
			Db::getInstance()->Execute('
				INSERT INTO `'._DB_PREFIX_.'rewards` (id_reward, id_reward_state, id_customer, id_order, id_cart_rule, credits, plugin, date_add, date_upd)
				SELECT id_loyalty, id_loyalty_state, id_customer, id_order, id_cart_rule, points * ' . $pointValue. ', \'loyalty\', date_add, date_upd FROM `'._DB_PREFIX_.'loyalty`');
			Db::getInstance()->Execute('
				INSERT INTO `'._DB_PREFIX_.'rewards_history` (id_reward, id_reward_state, credits, date_add)
				SELECT id_loyalty, id_loyalty_state, points * ' . $pointValue. ', date_add FROM `'._DB_PREFIX_.'loyalty_history`');
			$row = Db::getInstance()->getRow('SELECT IFNULL(MAX(id_reward),0)+1 AS nextid FROM `'._DB_PREFIX_.'rewards`');
			Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'rewards` AUTO_INCREMENT=' . $row['nextid']);
			Db::getInstance()->Execute('INSERT IGNORE INTO `'._DB_PREFIX_.'rewards_account` (id_customer, date_last_remind, date_add, date_upd) SELECT DISTINCT id_customer, NULL, date_add, NOW() FROM `'._DB_PREFIX_.'rewards` GROUP BY id_customer ORDER BY date_add ASC');
		}
	}

	public static function getByOrderId($id_order)
	{
		if (!Validate::isUnsignedId($id_order))
			return false;

		$result = Db::getInstance()->getRow('
		SELECT r.id_reward
		FROM `'._DB_PREFIX_.'rewards` r
		WHERE r.plugin=\'loyalty\' AND r.id_order = '.(int)$id_order);

		return isset($result['id_reward']) ? $result['id_reward'] : false;
	}

	// renvoie le prix total avec produits promo et sans produits promo d'une commande dans la devise du panier
	public static function getOrderTotalsForReward($order, $allowedCategories = NULL)
	{
		if (!Validate::isLoadedObject($order))
			return false;

		$orderDetails = $order->getProductsDetail();

		$gifts = array();
		$discount = 0;
		$discount_vat_excl = 0;
		foreach ($order->getCartRules() AS $rule) {
			$cart_rule = new CartRule($rule['id_cart_rule']);
			if ($cart_rule->gift_product)
				$gifts[$cart_rule->gift_product.'_'.$cart_rule->gift_product_attribute] = 1;
			if ((float)$cart_rule->reduction_percent != 0 || (float)$cart_rule->reduction_amount != 0) {
				$discount += (float)$rule['value'];
				$discount_vat_excl += (float)$rule['value_tax_excl'];
			}
		}

		$totals = array('tax_incl' => array('with_discounted' => 0, 'without_discounted' => 0), 'tax_excl' => array('with_discounted' => 0, 'without_discounted' => 0));
		if (is_array($orderDetails)) {
			foreach($orderDetails as $detail) {
				// si le produit n'est pas dans les catégories autorisées
				if (is_array($allowedCategories) && !Product::idIsOnCategoryId($detail['product_id'], $allowedCategories))
					continue;
				$quantity = $detail['product_quantity'] - $detail['product_quantity_refunded'] - (isset($gifts[$detail['product_id'].'_'.$detail['product_attribute_id']]) ? 1 : 0);
				$totals['tax_incl']['with_discounted'] += $quantity * $detail['unit_price_tax_incl'];
				$totals['tax_excl']['with_discounted'] += $quantity * $detail['unit_price_tax_excl'];
				// s'il n'y a pas eu de promo sur ce produit (prix dégressifs, prix forcés et prix de groupe ne sont pas des promos)
				if ((float)$detail['reduction_amount'] == 0 && (float)$detail['reduction_percent'] == 0) {
					$totals['tax_incl']['without_discounted'] += $quantity * $detail['unit_price_tax_incl'];
					$totals['tax_excl']['without_discounted'] += $quantity * $detail['unit_price_tax_excl'];
				}
			}
		}
		$totals['tax_incl']['with_discounted'] = ($totals['tax_incl']['with_discounted'] - $discount) < 0 ? 0 : $totals['tax_incl']['with_discounted'] - $discount;
		$totals['tax_incl']['without_discounted'] = ($totals['tax_incl']['without_discounted'] - $discount) < 0 ? 0 : $totals['tax_incl']['without_discounted'] - $discount;
		$totals['tax_excl']['with_discounted'] = ($totals['tax_excl']['with_discounted'] - $discount_vat_excl) < 0 ? 0 : $totals['tax_excl']['with_discounted'] - $discount_vat_excl;
		$totals['tax_excl']['without_discounted'] = ($totals['tax_excl']['without_discounted'] - $discount_vat_excl) < 0 ? 0 : $totals['tax_excl']['without_discounted'] - $discount_vat_excl;
		return $totals;
	}

	// indique si un produit bénéficie d'une réduction. Les prix dégressifs renvoient faux pour donner quand même des récompenses.
	public static function isDiscountedProduct($id_product, $id_product_attribute=0)
	{
		$context = Context::getContext();
		$cart_quantity = !$context->cart ? 0 : Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT SUM(`quantity`)
			FROM `'._DB_PREFIX_.'cart_product`
			WHERE `id_product` = '.(int)$id_product.' AND `id_cart` = '.(int)$context->cart->id.' AND `id_product_attribute` = '.(int)$id_product_attribute
		);
		$quantity = $cart_quantity ? $cart_quantity : 1;
		$ids = Address::getCountryAndState((int)$context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
		$id_country = (int)($ids['id_country'] ? $ids['id_country'] : Configuration::get('PS_COUNTRY_DEFAULT'));

		$row = SpecificPrice::getSpecificPrice((int)$id_product, $context->shop->id, (int)$context->currency->id, $id_country, $context->customer->id_default_group, $quantity, (int)$id_product_attribute, 0, 0, $quantity);
		if ($row && ($row['from'] != '0000-00-00 00:00:00' || $row['to'] != '0000-00-00 00:00:00' || $row['from_quantity'] == 1))
			return true;
		return false;
	}

	public static function getCurrencyValue($credits, $idCurrencyTo)
	{
		return round(Tools::convertPrice($credits, Currency::getCurrency((int)$idCurrencyTo)), 2);
	}

	public static function getAllTotalsByCustomer($id_customer)
	{
		$rewards = array();
		$rewards['total'] = 0;
		$rewards[RewardsStateModel::getConvertId()] = 0;
		$rewards[RewardsStateModel::getValidationId()] = 0;
		$rewards[RewardsStateModel::getDefaultId()] = 0;
		$rewards[RewardsStateModel::getReturnPeriodId()] = 0;
		$rewards[RewardsStateModel::getWaitingPaymentId()] = 0;
		$rewards[RewardsStateModel::getPaidId()] = 0;
		$query = '
		SELECT id_reward_state, SUM(r.credits) AS credits
		FROM `'._DB_PREFIX_.'rewards` r
		WHERE r.id_customer = '.(int)$id_customer.'
		GROUP BY id_reward_state';
		$totals = Db::getInstance()->ExecuteS($query);
		foreach($totals as $total) {
			$rewards[$total['id_reward_state']] = (float) $total['credits'];
			if ((int)$total['id_reward_state'] != RewardsStateModel::getCancelId() && (int)$total['id_reward_state'] != RewardsStateModel::getDiscountedId())
				$rewards['total'] += $rewards[$total['id_reward_state']];
		}
		return $rewards;
	}
        
        public static function getRewardReadyForDisplay($reward,$id_currency,$id_lang=NULL){
            
                $context = Context::getContext();
		$currency = Currency::getCurrency((int)$id_currency);

		$id_template=0;
		if (isset($customer)) {
			$id_template = (int)MyConf::getIdTemplate('core', $context->customer->id);
			if (is_null($id_lang) && version_compare(_PS_VERSION_, '1.5.4.0', '>=') && !empty($context->customer->id_lang))
				$id_lang = $context->customer->id_lang;
		}

		if (is_null($id_lang))
			$id_lang = $context->language->id;

		if ((float)MyConf::get('REWARDS_VIRTUAL', null, $id_template)) {
			$reward = round($reward*(float)MyConf::get('REWARDS_VIRTUAL_VALUE_'.$currency['id_currency'], null, $id_template), 2);
			// on ajoute les décimales que si ce n'est pas un entier
			if ($reward != (int)$reward)
				$reward = number_format($reward, 2, '.', '');
                        $points=$reward;
			return $reward;//.' '.MyConf::get('REWARDS_VIRTUAL_NAME', $id_lang, $id_template);
		} else
			return Tools::displayPrice(round(Tools::convertPrice((float)$reward, $currency), 2), (int)$currency['id_currency']);
        }
        
        public static function getMoneyReadyForDisplay($money,$id_currency,$id_lang=NULL){
            
                $context = Context::getContext();
		$currency = Currency::getCurrency((int)$id_currency);

		$id_template=0;
		if (isset($customer)) {
			$id_template = (int)MyConf::getIdTemplate('core', $context->customer->id);
			if (is_null($id_lang) && version_compare(_PS_VERSION_, '1.5.4.0', '>=') && !empty($context->customer->id_lang))
				$id_lang = $context->customer->id_lang;
		}

		if (is_null($id_lang))
			$id_lang = $context->language->id;

		if ((float)MyConf::get('REWARDS_VIRTUAL', null, $id_template)) {
			$money = round($money/(float)MyConf::get('REWARDS_VIRTUAL_VALUE_'.$currency['id_currency'], null, $id_template), 2);
			// on ajoute les décimales que si ce n'est pas un entier
			if ($money != (int)$money)
				$money = number_format($money, 2, '.', '');
                        //$points=$money;
			return $money;//.' '.MyConf::get('REWARDS_VIRTUAL_NAME', $id_lang, $id_template);
		} else
			return Tools::displayPrice(round(Tools::convertPrice((float)$money, $currency), 2), (int)$currency['id_currency']);
        }

	public static function getAllByIdCustomer($id_customer, $admin = false, $onlyValidate = false, $pagination = false, $nb = 10, $page = 1, $currency = NULL, $readyForDisplay = false)
	{
		$context = Context::getContext();

		$query = '
		SELECT r.id_order AS id_order, r.id_customer, r.id_reward_state, r.date_add AS date, DATE_ADD(r.date_upd, INTERVAL '.(int)Configuration::get('REWARDS_DURATION').' DAY) AS validity, ROUND((o.total_paid - o.total_shipping), 2) AS total_without_shipping, o.id_currency, r.credits, r.id_reward, r.id_reward_state, r.plugin, r.reason, rsl.name AS state
		FROM `'._DB_PREFIX_.'rewards` r
		LEFT JOIN `'._DB_PREFIX_.'orders` o USING (id_order)
		LEFT JOIN `'._DB_PREFIX_.'rewards_state_lang` rsl ON (r.id_reward_state = rsl.id_reward_state AND rsl.id_lang = '.(int)$context->language->id.')
		WHERE r.id_customer = '.(int)($id_customer);
		if ($onlyValidate === true)
			$query .= ' AND r.id_reward_state = '.(int)RewardsStateModel::getValidationId();
		$query .= ' GROUP BY r.id_reward ORDER BY r.date_add DESC '.
		($pagination ? 'LIMIT '.(((int)($page) - 1) * (int)($nb)).', '.(int)$nb : '');

		$module = new allinone_rewards();
		$rewards = Db::getInstance()->ExecuteS($query);
		foreach($rewards as $key => $reward) {
			if ($readyForDisplay)
				$rewards[$key]['credits'] = $module->getRewardReadyForDisplay($reward['credits'], $currency);
			else if ($currency != NULL)
				$rewards[$key]['credits'] = self::getCurrencyValue($reward['credits'], $currency);
			if ($reward['plugin'] != 'free') {
				$rewards[$key]['detail'] = html_entity_decode($module->{$reward['plugin']}->getDetails($reward, $admin));
			} else {
				$rewards[$key]['detail'] = html_entity_decode($reward['reason']);
			}
		}

		return $rewards;
	}

	public static function createDiscount($credits)
	{
		$context = Context::getContext();
		$id_template = (int)MyConf::getIdTemplate('core', (int)$context->customer->id);

		/* Generate a discount code */
		$code = NULL;
		do $code = MyConf::get('REWARDS_VOUCHER_PREFIX', null, $id_template).Tools::passwdGen(6);
		while (CartRule::cartRuleExists($code));

		/* Voucher creation and affectation to the customer */
		$cartRule = new CartRule();
		$cartRule->id_customer = (int)$context->customer->id;
		$cartRule->date_from = date('Y-m-d H:i:s', time() - 1); /* remove 1s because of a strict comparison between dates in getCustomerCartRules */
		$cartRule->date_to = date('Y-m-d H:i:s', time() + (int)MyConf::get('REWARDS_VOUCHER_DURATION', null, $id_template)*24*60*60);
		$cartRule->description = MyConf::get('REWARDS_VOUCHER_DETAILS', (int)$context->language->id, $id_template);
		$cartRule->quantity = 1;
		$cartRule->quantity_per_user = 1;
		$cartRule->highlight = (int)MyConf::get('REWARDS_DISPLAY_CART', null, $id_template);
		$cartRule->partial_use = (int)MyConf::get('REWARDS_VOUCHER_BEHAVIOR', null, $id_template);
		$cartRule->code = $code;
		$cartRule->active = 1;
		$cartRule->reduction_amount = self::getCurrencyValue($credits, $context->currency->id);
		$cartRule->reduction_tax = (int)MyConf::get('REWARDS_VOUCHER_TAX', null, $id_template);
		$cartRule->reduction_currency = (int)$context->currency->id;
		$cartRule->minimum_amount = (float)MyConf::get('REWARDS_VOUCHER_MIN_ORDER_'.$context->currency->id, null, $id_template);
		$cartRule->minimum_amount_tax = (int)MyConf::get('REWARDS_MINIMAL_TAX', null, $id_template);
		$cartRule->minimum_amount_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
		$cartRule->minimum_amount_shipping = (int)MyConf::get('REWARDS_MINIMAL_SHIPPING', null, $id_template);
		$cartRule->cart_rule_restriction = (int)(!(bool)MyConf::get('REWARDS_VOUCHER_CUMUL_S', null, $id_template));

		$languages = Language::getLanguages(true);
		$default_text = MyConf::get('REWARDS_VOUCHER_DETAILS', (int)Configuration::get('PS_LANG_DEFAULT'), $id_template);
		foreach ($languages AS $language)
		{
			$text = MyConf::get('REWARDS_VOUCHER_DETAILS', (int)$language['id_lang'], $id_template);
			$cartRule->name[(int)($language['id_lang'])] = $text ? $text : $default_text;
		}

		$all_categories = (int)MyConf::get('REWARDS_ALL_CATEGORIES', null, $id_template);
		$categories = explode(',', MyConf::get('REWARDS_VOUCHER_CATEGORY', null, $id_template));
		if (!$all_categories && is_array($categories) && count($categories) > 0)
			$cartRule->product_restriction = 1;
		$cartRule->add();

		/* if this discount is only available for a list of categories */
		if ($cartRule->product_restriction) {
			/* cart must contain 1 product from 1 of the selected categories */
			Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`) VALUES ('.(int)$cartRule->id.', 1)');
			$id_product_rule_group = Db::getInstance()->Insert_ID();

			/* create the category rule */
			Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule` (`id_product_rule_group`, `type`) VALUES ('.(int)$id_product_rule_group.', \'categories\')');
			$id_product_rule = Db::getInstance()->Insert_ID();

			/* insert the list of categories */
			$values = array();
			foreach($categories as $category)
				$values[] = '('.(int)$id_product_rule.','.(int)$category.')';
			$values = array_unique($values);
			if (count($values))
				Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES '.implode(',', $values));
		}

		// If the discount has no cart rule restriction, then it must be added to the white list of the other cart rules that have restrictions
		if ((int)MyConf::get('REWARDS_VOUCHER_CUMUL_S', null, $id_template))
		{
			Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`) (
				SELECT id_cart_rule, '.(int)$cartRule->id.' FROM `'._DB_PREFIX_.'cart_rule` WHERE cart_rule_restriction = 1
			)');
		}

		/* Register order(s) which contributed to create this discount */
		self::registerDiscount($cartRule);
                return $cartRule->code;
	}

	public static function registerDiscount($cartRule)
	{
		if (!Validate::isLoadedObject($cartRule))
			die(Tools::displayError('Incorrect object Discount.'));
		$items = self::getAllByIdCustomer((int)$cartRule->id_customer, false, true);
		foreach($items AS $item)
		{
			$r = new RewardsModel((int)$item['id_reward']);
			$r->id_cart_rule = (int)$cartRule->id;
			$r->id_reward_state = (int)RewardsStateModel::getConvertId();
			$r->save();
		}
	}

	public static function registerPayment($payment)
	{
		$context = Context::getContext();

		if (!Validate::isLoadedObject($payment))
			die(Tools::displayError('Incorrect object RewardsPaymentModel.'));
		$items = self::getAllByIdCustomer((int)$context->customer->id, false, true);
		foreach($items AS $item)
		{
			$r = new RewardsModel((int)$item['id_reward']);
			$r->id_payment = (int)$payment->id;
			$r->id_reward_state = (int)RewardsStateModel::getWaitingPaymentId();
			$r->save();
		}
	}

	public static function acceptPayment($id_payment)
	{
		$query = 'SELECT * FROM `'._DB_PREFIX_.'rewards` r WHERE r.id_payment='.(int)$id_payment.' AND r.id_reward_state='.(int)RewardsStateModel::getWaitingPaymentId();
		$items = Db::getInstance()->ExecuteS($query);
		foreach($items AS $item)
		{
			$r = new RewardsModel((int)$item['id_reward']);
			$r->id_reward_state = (int)RewardsStateModel::getPaidId();
			$r->save();
		}
		return $items[0]['id_customer'];
	}

	// Convert rewards in ReturnPeriodId or ValidationId state if return date is over
	// Cancel rewards if validity has expired (based on date_upd)
	public static function checkRewardsStates() {
		$rewardStateValidation = new RewardsStateModel(RewardsStateModel::getValidationId());
		// rewards waiting for the end of the return period or rewards not validated automatically (expeditor_inet for example)
		// TODO : add the check of the date for rewards not validated automatically in case of return period activated
		$query = '
		SELECT r.id_reward
		FROM `'._DB_PREFIX_.'rewards` r
		LEFT JOIN `'._DB_PREFIX_.'orders` o USING (id_order)
		WHERE (id_reward_state=1 AND o.current_state IN ('.implode(',', $rewardStateValidation->getValues()).'))
		OR (r.id_reward_state = '.(int)RewardsStateModel::getReturnPeriodId();

		// rewards which have been in return period since time > return period nb days
		if (Configuration::get('REWARDS_WAIT_RETURN_PERIOD') && Configuration::get('PS_ORDER_RETURN') && (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS') > 0) {
			$query .= '
			AND (
				DATE_ADD(r.date_upd, INTERVAL '.(int)Configuration::get('PS_ORDER_RETURN_NB_DAYS').' DAY) < NOW()
				OR EXISTS (
					SELECT id_reward
					FROM `'._DB_PREFIX_.'rewards_history` rh
					WHERE rh.id_reward = r.id_reward
					AND rh.id_reward_state = '.(int)RewardsStateModel::getReturnPeriodId().'
					AND DATE_ADD(date_add, INTERVAL '.(int)Configuration::get('PS_ORDER_RETURN_NB_DAYS').' DAY) < NOW()
				)
			)';
		}
		$query .= ')';

		$rows = Db::getInstance()->ExecuteS($query);
		if (is_array($rows)) {
			foreach ($rows AS $row)	{
				$reward = new RewardsModel((int)$row['id_reward']);
				$reward->id_reward_state = (int)RewardsStateModel::getValidationId();
				$reward->save();
			}
		}

		// rewards with expired validity
		if (Configuration::get('REWARDS_DURATION')) {
			$query = '
			SELECT r.id_reward
			FROM `'._DB_PREFIX_.'rewards` r
			WHERE r.id_reward_state = '.(int)RewardsStateModel::getValidationId().'
			AND DATE_ADD(r.date_upd, INTERVAL '.(int)Configuration::get('REWARDS_DURATION').' DAY) < NOW()'.
			(Configuration::get('REWARDS_USE_CRON') ? '' : ' LIMIT 20');
			$rows = Db::getInstance()->ExecuteS($query);
			if (is_array($rows)) {
				foreach ($rows AS $row)	{
					$reward = new RewardsModel((int)$row['id_reward']);
					$reward->id_reward_state = (int)RewardsStateModel::getCancelId();
					$reward->save();
				}
			}
		}
	}

	public function getUnlockDate() {
		$query = '
			SELECT DATE_ADD(date_add, INTERVAL '.(int)Configuration::get('PS_ORDER_RETURN_NB_DAYS').' DAY) AS unlock_date
			FROM `'._DB_PREFIX_.'rewards_history` rh
			WHERE rh.id_reward = '.(int)$this->id.'
			AND rh.id_reward_state = '.(int)RewardsStateModel::getReturnPeriodId().'
			ORDER BY date_add ASC';
		$result = Db::getInstance()->getRow($query);
		return $result['unlock_date'];
	}

	// Register all transaction in a specific history table
	private function historize()
	{
		Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'rewards_history` (`id_reward`, `id_reward_state`, `credits`, `date_add`)
		VALUES ('.(int)$this->id.', '.(int)$this->id_reward_state.', '.(float)$this->credits.', NOW())');
	}

	// check if customer is in a group which is allowed to transform rewards into vouchers or ask for payment
	static public function isCustomerAllowedForVoucher($id_customer)
	{
		return self::_isCustomerAllowed($id_customer, 'REWARDS_VOUCHER');
	}

	static public function isCustomerAllowedForPayment($id_customer)
	{
		return self::_isCustomerAllowed($id_customer, 'REWARDS_PAYMENT');
	}

	static private function _isCustomerAllowed($id_customer, $key) {
		$customer = new Customer($id_customer);
		if (Validate::isLoadedObject($customer)) {
			$id_template = (int)MyConf::getIdTemplate('core', $customer->id);
			// if the customer is linked to a template, then it overrides the groups setting
			if (MyConf::get($key, null, $id_template)) {
				if ($id_template)
					return true;
				$allowed_groups = explode(',', Configuration::get($key.'_GROUPS'));
				$customer_groups = $customer->getGroups();
				return sizeof(array_intersect($allowed_groups, $customer_groups)) > 0;
			}
		}
		return false;
	}

	// get all statistics for BO
	static public function getAdminStatistics() {
		$result = array('total_rewards' => 0, 'nb_rewards' => 0, 'nb_customers' => 0, 'credits' => 0,
					'total_rewards'.RewardsStateModel::getDefaultId() => 0, 'total_rewards'.RewardsStateModel::getValidationId() => 0,
					'total_rewards'.RewardsStateModel::getCancelId() => 0, 'total_rewards'.RewardsStateModel::getConvertId() => 0,
					'total_rewards'.RewardsStateModel::getReturnPeriodId() => 0, 'total_rewards'.RewardsStateModel::getWaitingPaymentId() => 0,
					'total_rewards'.RewardsStateModel::getPaidId() => 0,
					'nb_rewards'.RewardsStateModel::getDefaultId() => 0, 'nb_rewards'.RewardsStateModel::getValidationId() => 0,
					'nb_rewards'.RewardsStateModel::getCancelId() => 0, 'nb_rewards'.RewardsStateModel::getConvertId() => 0,
					'nb_rewards'.RewardsStateModel::getReturnPeriodId() => 0, 'nb_rewards'.RewardsStateModel::getWaitingPaymentId() => 0,
					'nb_rewards'.RewardsStateModel::getPaidId() => 0,
					'nb_customers'.RewardsStateModel::getDefaultId() => 0, 'nb_customers'.RewardsStateModel::getValidationId() => 0,
					'nb_customers'.RewardsStateModel::getCancelId() => 0, 'nb_customers'.RewardsStateModel::getConvertId() => 0,
					'nb_customers'.RewardsStateModel::getReturnPeriodId() => 0, 'nb_customers'.RewardsStateModel::getWaitingPaymentId() => 0,
					'nb_customers'.RewardsStateModel::getPaidId() => 0,
					'nb_rewardsfree' => 0, 'nb_customersfree' => 0, 'total_rewardsfree' => 0,
					'customers' => array());

		$module = new allinone_rewards();
		foreach($module->plugins as $plugin) {
			if (!$plugin instanceof RewardsCorePlugin) {
				$result['total_rewards'.$plugin->name] = 0;
				$result['nb_rewards'.$plugin->name] = 0;
				$result['nb_customers'.$plugin->name] = 0;
			}
		}

		$query = '
			SELECT id_reward_state, plugin, COUNT(*) AS nb_rewards, COUNT(DISTINCT id_customer) AS nb_customers, SUM(credits) AS credits
			FROM `'._DB_PREFIX_.'rewards`
			GROUP BY id_reward_state, plugin';
		$rows = Db::getInstance()->executeS($query);
		foreach ($rows AS $row) {
			// by id_reward_state
			$result['total_rewards'.$row['id_reward_state']] += (float)$row['credits'];
			$result['nb_rewards'.$row['id_reward_state']] += (int)$row['nb_rewards'];
			$result['nb_customers'.$row['id_reward_state']] += (int)$row['nb_customers'];
			if ($row['id_reward_state'] != RewardsStateModel::getCancelId()) {
				// by plugin
				$result['total_rewards'.$row['plugin']] += (float)$row['credits'];
				$result['nb_rewards'.$row['plugin']] += (int)$row['nb_rewards'];
				$result['nb_customers'.$row['plugin']] += (int)$row['nb_customers'];
				// global
				$result['total_rewards'] += (float)$row['credits'];
				$result['nb_rewards'] += (int)$row['nb_rewards'];
			}
		}

		$query = '
			SELECT id_reward_state, c.id_customer, c.firstname, c.lastname, COUNT(*) AS nb_rewards, SUM(credits) AS credits
			FROM `'._DB_PREFIX_.'rewards`
			JOIN `'._DB_PREFIX_.'customer` AS c USING (id_customer)
			GROUP BY id_reward_state, id_customer';
		$rows = Db::getInstance()->executeS($query);
		foreach ($rows AS $row) {
			if (!isset($result['customers'][$row['id_customer']]['total_rewards'])) {
				$result['nb_customers']++;
				$result['customers'][$row['id_customer']]['total_rewards'] = 0;
				$result['customers'][$row['id_customer']]['nb_rewards'] = 0;
			}

			$result['customers'][$row['id_customer']]['firstname'] = $row['firstname'];
			$result['customers'][$row['id_customer']]['lastname'] = $row['lastname'];
			$result['customers'][$row['id_customer']]['total_rewards'.$row['id_reward_state']] = (float)$row['credits'];
			if ($row['id_reward_state'] != RewardsStateModel::getCancelId()) {
				$result['customers'][$row['id_customer']]['total_rewards'] += (float)$row['credits'];
				$result['customers'][$row['id_customer']]['nb_rewards'] += (int)$row['nb_rewards'];
			}
		}

		return $result;
	}
       
}