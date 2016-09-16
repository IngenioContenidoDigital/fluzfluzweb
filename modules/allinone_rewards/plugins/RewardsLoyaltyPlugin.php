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

require_once(_PS_MODULE_DIR_.'/allinone_rewards/plugins/RewardsGenericPlugin.php');

class RewardsLoyaltyPlugin extends RewardsGenericPlugin
{
	public $name = 'loyalty';

	public function install()
	{
		// hooks
		if (!$this->registerHook('displayRightColumnProduct') || !$this->registerHook('displayShoppingCartFooter')
		|| !$this->registerHook('actionValidateOrder') || !$this->registerHook('actionOrderStatusUpdate')
		|| !$this->registerHook('actionObjectOrderDetailAddAfter') || !$this->registerHook('actionObjectOrderDetailUpdateAfter') || !$this->registerHook('actionObjectOrderDetailDeleteAfter')
		|| !$this->registerHook('displayAdminOrder') || !$this->registerHook('displayAdminProductsExtra') || !$this->registerHook('ActionAdminControllerSetMedia')
		|| !$this->registerHook('displayPDFInvoice')
                || !$this->registerHook('actionValidateOrder2')
		|| !$this->registerHook('actionObjectProductDeleteAfter'))
			return false;

		$groups_config = '';
		$groups = Group::getGroups((int)(Configuration::get('PS_LANG_DEFAULT')));
		foreach ($groups AS $group)
			$groups_config .= (int)$group['id_group'].',';
		$groups_config = rtrim($groups_config, ',');

		if (!Configuration::updateValue('RLOYALTY_TYPE', 0)
		|| !Configuration::updateValue('RLOYALTY_TAX', 1)
		|| !Configuration::updateValue('RLOYALTY_POINT_VALUE', 0.50)
		|| !Configuration::updateValue('RLOYALTY_POINT_RATE', 10)
		|| !Configuration::updateValue('RLOYALTY_PERCENTAGE', 5)
		|| !Configuration::updateValue('RLOYALTY_DEFAULT_PRODUCT_REWARD', 0)
		|| !Configuration::updateValue('RLOYALTY_DEFAULT_PRODUCT_TYPE', 0)
		|| !Configuration::updateValue('RLOYALTY_MULTIPLIER', 1)
		|| !Configuration::updateValue('RLOYALTY_DISCOUNTED_ALLOWED', 1)
		|| !Configuration::updateValue('RLOYALTY_ACTIVE', 0)
		|| !Configuration::updateValue('RLOYALTY_INVOICE', 0)
		|| !Configuration::updateValue('RLOYALTY_MAIL_VALIDATION', 1)
		|| !Configuration::updateValue('RLOYALTY_MAIL_CANCELPROD', 1)
		|| !Configuration::updateValue('RLOYALTY_GROUPS', $groups_config)
		|| !Configuration::updateValue('RLOYALTY_ALL_CATEGORIES', 1)
		|| !Configuration::updateValue('RLOYALTY_CATEGORIES', ''))
			return false;

		// database
		Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_product` (
			`id_reward_product` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_product` INT UNSIGNED NOT NULL,
			`type` INT UNSIGNED NOT NULL DEFAULT 0,
			`value` DECIMAL(20, 2) UNSIGNED NOT NULL DEFAULT \'0\',
			`date_from` DATETIME,
			`date_to` DATETIME,
			PRIMARY KEY (`id_reward_product`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		// create an invisible tab so we can call an admin controller to manage the product rewards in the product page
		$tab = new Tab();
		$tab->active = 1;
		$tab->class_name = "AdminProductReward";
		$tab->name = array();
		foreach (Language::getLanguages(true) as $lang)
			$tab->name[$lang['id_lang']] = 'AllinoneRewards Product Reward';
		$tab->id_parent = -1;
		$tab->module = $this->instance->name;

		if (!$tab->add())
			return false;

		return true;
	}

	public function uninstall()
	{
		$id_tab = (int)Tab::getIdFromClassName('AdminProductReward');
		if ($id_tab) {
			$tab = new Tab($id_tab);
			$tab->delete();
		}

		//Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'rewards_product`;');
		Db::getInstance()->Execute('
			DELETE FROM `'._DB_PREFIX_.'configuration_lang`
			WHERE `id_configuration` IN (SELECT `id_configuration` from `'._DB_PREFIX_.'configuration` WHERE `name` like \'RLOYALTY_%\')');

		Db::getInstance()->Execute('
			DELETE FROM `'._DB_PREFIX_.'configuration`
			WHERE `name` like \'RLOYALTY_%\'');

		return true;
	}

	public function isActive()
	{
		$id_template=0;
		if (isset($this->context->customer))
			$id_template = (int)MyConf::getIdTemplate('loyalty', $this->context->customer->id);
		return MyConf::get('RLOYALTY_ACTIVE', null, $id_template);
	}

	public function getTitle()
	{
		return $this->l('Loyalty program');
	}

	public function getDetails($reward, $admin)
	{
		if ($admin) {
			$tokenOrder = Tools::getAdminToken('AdminOrders'.(int)Tab::getIdFromClassName('AdminOrders').(int)$this->context->employee->id);
			return sprintf($this->l('Loyalty - order #%s'), '<a href="?tab=AdminOrders&id_order='.$reward['id_order'].'&vieworder&token='.$tokenOrder.'" style="display: inline; width: auto">'.sprintf('%06d', $reward['id_order']).'</a>');
		} else
			return sprintf($this->l('Loyalty - order #%s'), sprintf('%06d', $reward['id_order']));
	}

	protected function postProcess($params=null)
	{
		// on initialise le template à chaque chargement
		$this->initTemplate();

		if (Tools::isSubmit('submitLoyalty')) {
			$this->_postValidation();
			if (!sizeof($this->_errors)) {
				if (empty($this->id_template)) {
					Configuration::updateValue('RLOYALTY_GROUPS', implode(",", Tools::getValue('rloyalty_groups')));
				}
				MyConf::updateValue('RLOYALTY_ACTIVE', (int)Tools::getValue('rloyalty_active'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_INVOICE', (int)Tools::getValue('rloyalty_invoice'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_TYPE', (int)Tools::getValue('rloyalty_type'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_TAX', (int)Tools::getValue('rloyalty_tax'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_POINT_VALUE', (float)Tools::getValue('rloyalty_point_value'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_POINT_RATE', (float)Tools::getValue('rloyalty_point_rate'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_PERCENTAGE', (float)Tools::getValue('rloyalty_percentage'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_DEFAULT_PRODUCT_REWARD', (float)Tools::getValue('rloyalty_default_product_reward'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_DEFAULT_PRODUCT_TYPE', (int)Tools::getValue('rloyalty_default_product_type'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_MULTIPLIER', (float)Tools::getValue('rloyalty_multiplier'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_DISCOUNTED_ALLOWED', (int)Tools::getValue('rloyalty_discounted_allowed'), null, $this->id_template);
				if (!Tools::getValue('rloyalty_type') || (int)Tools::getValue('rloyalty_type') == 1) {
					MyConf::updateValue('RLOYALTY_ALL_CATEGORIES', (int)Tools::getValue('rloyalty_all_categories'), null, $this->id_template);
					MyConf::updateValue('RLOYALTY_CATEGORIES', Tools::getValue('categoryBox') ? implode(',', Tools::getValue('categoryBox')) : '', null, $this->id_template);
				}
				$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Settings updated.'));
			} else
				$this->instance->errors = $this->instance->displayError(implode('<br />', $this->_errors));
		} else if (Tools::isSubmit('submitLoyaltyNotifications')) {
			Configuration::updateValue('RLOYALTY_MAIL_VALIDATION', (int)Tools::getValue('mail_validation'));
			Configuration::updateValue('RLOYALTY_MAIL_CANCELPROD', (int)Tools::getValue('mail_cancel_product'));
			$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Settings updated.'));
		}
	}

	private function _postValidation()
	{
		$this->_errors = array();
		if (empty($this->id_template)) {
			if (!is_array(Tools::getValue('rloyalty_groups')))
				$this->_errors[] = $this->l('Please select at least 1 customer group allowed to get loyalty rewards');
		}
		if (!is_numeric(Tools::getValue('rloyalty_point_rate')) || Tools::getValue('rloyalty_point_rate') <= 0)
			$this->_errors[] = $this->l('The ratio is required/invalid.');
		if (!is_numeric(Tools::getValue('rloyalty_point_value')) || Tools::getValue('rloyalty_point_value') <= 0)
			$this->_errors[] = $this->l('The value is required/invalid.');
		if (!is_numeric(Tools::getValue('rloyalty_percentage')) || Tools::getValue('rloyalty_percentage') <= 0)
			$this->_errors[] = $this->l('The percentage is required/invalid.');
		if (!is_numeric(Tools::getValue('rloyalty_default_product_reward')) || Tools::getValue('rloyalty_default_product_reward') < 0)
			$this->_errors[] = $this->l('The default reward is invalid.');
		if (!is_numeric(Tools::getValue('rloyalty_multiplier')) || Tools::getValue('rloyalty_multiplier') <= 0)
			$this->_errors[] = $this->l('The coefficient multiplier is required/invalid.');
		if ((!Tools::getValue('rloyalty_type') || (int)Tools::getValue('rloyalty_type')==1) && !Tools::getValue('rloyalty_all_categories') && !is_array(Tools::getValue('categoryBox')) || !sizeof(Tools::getValue('categoryBox')))
			$this->_errors[] = $this->l('You must choose at least one category of products');
	}

	public function displayForm()
	{
		if (Tools::getValue('stats'))
			return $this->_getStatistics();

		$this->postProcess();

		$currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
		$groups = Group::getGroups((int)$this->context->language->id);
		$allowed_groups = Tools::getValue('rloyalty_groups', explode(',', Configuration::get('RLOYALTY_GROUPS')));
		$categories = Tools::getValue('categoryBox', explode(',', MyConf::get('RLOYALTY_CATEGORIES', null, $this->id_template)));

		$html = $this->getTemplateForm($this->id_template, $this->name, $this->l('Loyalty')).'
		<div class="tabs" style="display: none">
			<ul>
				<li><a href="#tabs-'.$this->name.'-1">'.$this->l('Settings').'</a></li>
				<li class="not_templated"><a href="#tabs-'.$this->name.'-2">'.$this->l('Notifications').'</a></li>
				<li class="not_templated"><a href="'.$this->instance->getCurrentPage($this->name, true).'&stats=1">'.$this->l('Statistics').'</a></li>
			</ul>
			<div id="tabs-'.$this->name.'-1">
				<form action="'.$this->instance->getCurrentPage($this->name).'" method="post">
					<fieldset>
						<legend>'.$this->l('General settings').'</legend>
						<label>'.$this->l('Activate loyalty program').'</label>
						<div class="margin-form">
							<label class="t" for="loyalty_active_on"><img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Yes').'" /></label>
							<input type="radio" id="loyalty_active_on" name="rloyalty_active" value="1" '.(Tools::getValue('rloyalty_active', MyConf::get('RLOYALTY_ACTIVE', null, $this->id_template)) == 1 ? 'checked="checked"' : '').' /> <label class="t" for="loyalty_active_on">' . $this->l('Yes') . '</label>
							<label class="t" for="loyalty_active_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('No').'" /></label>
							<input type="radio" id="loyalty_active_off" name="rloyalty_active" value="0" '.(Tools::getValue('rloyalty_active', MyConf::get('RLOYALTY_ACTIVE', null, $this->id_template)) == 0 ? 'checked="checked"' : '').' /> <label class="t" for="loyalty_active_off">' . $this->l('No') . '</label>
						</div>
						<div class="clear not_templated">
							<label>'.$this->l('Customers groups allowed to get loyalty rewards').'</label>
							<div class="margin-form">
								<select name="rloyalty_groups[]" multiple="multiple" class="multiselect">';
		foreach($groups as $group) {
			$html .= '				<option '.(is_array($allowed_groups) && in_array($group['id_group'], $allowed_groups) ? 'selected':'').' value="'.$group['id_group'].'"> '.$group['name'].'</option>';
		}
		$html .= '
								</select>
							</div>
						</div>
						<div class="clear"></div>
						<label>'.$this->l('Display the reward in the PDF invoice').'</label>
						<div class="margin-form">
							<label class="t" for="loyalty_invoice_on"><img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Yes').'" /></label>
							<input type="radio" id="loyalty_invoice_on" name="rloyalty_invoice" value="1" '.(Tools::getValue('rloyalty_invoice', MyConf::get('RLOYALTY_INVOICE', null, $this->id_template)) == 1 ? 'checked="checked"' : '').' /> <label class="t" for="loyalty_invoice_on">' . $this->l('Yes') . '</label>
							<label class="t" for="loyalty_invoice_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('No').'" /></label>
							<input type="radio" id="loyalty_invoice_off" name="rloyalty_invoice" value="0" '.(Tools::getValue('rloyalty_invoice', MyConf::get('RLOYALTY_INVOICE', null, $this->id_template)) == 0 ? 'checked="checked"' : '').' /> <label class="t" for="loyalty_invoice_off">' . $this->l('No') . '</label>
						</div>
						<div class="clear"></div>
						<label>'.$this->l('How is calculated the reward ?').'</label>
						<div class="margin-form">
							<input type="radio" id="loyalty_type_range" name="rloyalty_type" value="0" '.(Tools::getValue('rloyalty_type', MyConf::get('RLOYALTY_TYPE', null, $this->id_template)) == 0 ? 'checked="checked"' : '').' /> <label class="t" for="loyalty_type_range">' . $this->l('Based on the total of the cart') . '</label>
							&nbsp;<input type="radio" id="loyalty_type_percentage" name="rloyalty_type" value="1" '.(Tools::getValue('rloyalty_type', MyConf::get('RLOYALTY_TYPE', null, $this->id_template)) == 1 ? 'checked="checked"' : '').' /> <label class="t" for="loyalty_type_percentage">' . $this->l('% of the total of the cart') . '</label>
							&nbsp;<input type="radio" id="loyalty_type_product" name="rloyalty_type" value="2" '.(Tools::getValue('rloyalty_type', MyConf::get('RLOYALTY_TYPE', null, $this->id_template)) == 2 ? 'checked="checked"' : '').' /> <label class="t" for="loyalty_type_product">' . $this->l('Product per product') . '</label>
						</div>
						<div class="clear optional reward_type_optional_0">
							<label></label>
							<div class="margin-form">'.$this->l('All vouchers will be deduced before calculating the total').'</div>
							<div class="clear"></div>
							<label>'.$this->l('For every').'</label>
							<div class="margin-form">
								<input type="text" size="3" id="rloyalty_point_rate" name="rloyalty_point_rate" value="'.Tools::getValue('rloyalty_point_rate', (float)MyConf::get('RLOYALTY_POINT_RATE', null, $this->id_template)).'" /> <label class="t">'.$currency->sign.' '.$this->l('spent on the shop').'</label>
							</div>
							<div class="clear"></div>
							<label>'.$this->l('Customer gets').'</label>
							<div class="margin-form">
								<input class="notvirtual" type="text" size="3" name="rloyalty_point_value" id="rloyalty_point_value" value="'.Tools::getValue('rloyalty_point_value', (float)MyConf::get('RLOYALTY_POINT_VALUE', null, $this->id_template)).'" onBlur="showVirtualValue(this, '.$currency->id.', true)" /> <label class="t">'.$currency->sign.' <span class="virtualvalue"></span></label>
							</div>
						</div>
						<div class="clear optional reward_type_optional_1">
							<label></label>
							<div class="margin-form">'.$this->l('All vouchers will be deduced before calculating the total').'</div>
							<div class="clear"></div>
							<label>'.$this->l('Percentage').'</label>
							<div class="margin-form">
								<input type="text" size="3" name="rloyalty_percentage" value="'.Tools::getValue('rloyalty_percentage', (float)MyConf::get('RLOYALTY_PERCENTAGE', null, $this->id_template)).'" /> %
							</div>
						</div>
						<div class="clear optional reward_type_optional_2">
							<label>'.$this->l('Default reward for product with no custom value').'</label>
							<div class="margin-form">
								<input class="notvirtual" type="text" size="3" name="rloyalty_default_product_reward" value="'.Tools::getValue('rloyalty_default_product_reward', (float)MyConf::get('RLOYALTY_DEFAULT_PRODUCT_REWARD', null, $this->id_template)).'" onBlur="showVirtualValue(this, '.$currency->id.', true)" />
								<select name="rloyalty_default_product_type" onChange="showVirtualValue(this, '.$currency->id.', true)">
									<option '.(Tools::getValue('rloyalty_default_product_type', (float)MyConf::get('RLOYALTY_DEFAULT_PRODUCT_TYPE', null, $this->id_template)) == 0 ? 'selected' : '').' value="0">% '.$this->l('of its own price').'</option>
									<option '.(Tools::getValue('rloyalty_default_product_type', (float)MyConf::get('RLOYALTY_DEFAULT_PRODUCT_TYPE', null, $this->id_template)) == 1 ? 'selected' : '').' value="1">'.$currency->sign.'</option>
								</select>
								&nbsp;<span class="virtualvalue"></span>
							</div>
							<div class="clear"></div>
							<label>'.$this->l('Coefficient multiplier (all rewards will be multiplied by this coefficient)').'</label>
							<div class="margin-form">
								<input type="text" size="3" name="rloyalty_multiplier" value="'.Tools::getValue('rloyalty_multiplier', (float)MyConf::get('RLOYALTY_MULTIPLIER', null, $this->id_template)).'" />
							</div>
						</div>
						<div class="clear"></div>
						<label>'.$this->l('Price to use to calculate the reward (when the customer pays the VAT)').'</label>
						<div class="margin-form">
							<input type="radio" id="rloyalty_tax_off" name="rloyalty_tax" value="0" '.(Tools::getValue('rloyalty_tax', MyConf::get('RLOYALTY_TAX', null, $this->id_template)) == 0 ? 'checked="checked"' : '').' /> <label class="t" for="rloyalty_tax_off">' . $this->l('VAT Excl.') . '</label>
							<input type="radio" id="rloyalty_tax_on" name="rloyalty_tax" value="1" '.(Tools::getValue('rloyalty_tax', MyConf::get('RLOYALTY_TAX', null, $this->id_template)) == 1 ? 'checked="checked"' : '').' /> <label class="t" for="rloyalty_tax_on">' . $this->l('VAT Incl.') . '</label>
						</div>
						<div class="clear"></div>
						<label>'.$this->l('Give rewards on discounted products').' </label>
						<div class="margin-form">
							<label class="t" for="rloyalty_discounted_allowed_on"><img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Yes').'" /></label>
							<input type="radio" id="rloyalty_discounted_allowed_on" name="rloyalty_discounted_allowed" value="1" '.(MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $this->id_template) ? 'checked="checked" ' : '').'/> <label class="t" for="rloyalty_discounted_allowed_on">' . $this->l('Yes') . '</label>
							<label class="t" for="rloyalty_discounted_allowed_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('No').'" /></label>
							<input type="radio" id="rloyalty_discounted_allowed_off" name="rloyalty_discounted_allowed" value="0" '.(!MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $this->id_template) ? 'checked="checked" ' : '').'/> <label class="t" for="rloyalty_discounted_allowed_off">' . $this->l('No') . '</label>
						</div>
						<div class="clear reward_type_optional_0 reward_type_optional_1">
							<label>'.$this->l('Categories of products allowing to get loyalty rewards').'</label>
							<div class="margin-form">
								<input class="all_categories" type="radio" id="all_categories_on" name="rloyalty_all_categories" value="0" '.(!Tools::getValue('rloyalty_all_categories', MyConf::get('RLOYALTY_ALL_CATEGORIES', null, $this->id_template)) ? 'checked="checked"' : '').' /> <label class="t" for="all_categories_on">' . $this->l('Choose categories') . '</label>&nbsp;
								<input class="all_categories" type="radio" id="all_categories_off" name="rloyalty_all_categories" value="1" '.(Tools::getValue('rloyalty_all_categories', MyConf::get('RLOYALTY_ALL_CATEGORIES', null, $this->id_template)) ? 'checked="checked"' : '').' /> <label class="t" for="all_categories_off">' . $this->l('All categories') . '</label>
								<div class="optional categories_optional" style="padding-top: 15px">
									'.$this->getCategoriesTree($categories).'
								</div>
							</div>
						</div>
					</fieldset>
					<div class="clear center"><input type="submit" name="submitLoyalty" id="submitLoyalty" value="'.$this->l('Save settings').'" class="button" /></div>
				</form>
			</div>
			<div id="tabs-'.$this->name.'-2" class="not_templated">
				<form action="'.$this->instance->getCurrentPage($this->name).'" method="post">
				<input type="hidden" name="tabs-'.$this->name.'" value="tabs-'.$this->name.'-2" />
				<fieldset>
					<legend>'.$this->l('Notifications').'</legend>
					<label>'.$this->l('Send a mail to the customer on reward validation/cancellation').'</label>
					<div class="margin-form">
						<label class="t" for="mail_validation_on"><img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Yes').'" /></label>
						<input type="radio" id="mail_validation_on" name="mail_validation" value="1" '.(Tools::getValue('mail_validation', Configuration::get('RLOYALTY_MAIL_VALIDATION')) == 1 ? 'checked="checked"' : '').' /> <label class="t" for="mail_validation_on">' . $this->l('Yes') . '</label>
						<label class="t" for="mail_validation_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('No').'" /></label>
						<input type="radio" id="mail_validation_off" name="mail_validation" value="0" '.(Tools::getValue('mail_validation', Configuration::get('RLOYALTY_MAIL_VALIDATION')) == 0 ? 'checked="checked"' : '').' /> <label class="t" for="mail_validation_off">' . $this->l('No') . '</label>
					</div>
					<div class="clear"></div>
					<label>'.$this->l('Send a mail to the customer on reward modification').'</label>
					<div class="margin-form">
						<label class="t" for="mail_cancel_product_on"><img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Yes').'" /></label>
						<input type="radio" id="mail_cancel_product_on" name="mail_cancel_product" value="1" '.(Tools::getValue('mail_cancel_product', Configuration::get('RLOYALTY_MAIL_CANCELPROD')) == 1 ? 'checked="checked"' : '').' /> <label class="t" for="mail_cancel_product_on">' . $this->l('Yes') . '</label>
						<label class="t" for="mail_cancel_product_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('No').'" /></label>
						<input type="radio" id="mail_cancel_product_off" name="mail_cancel_product" value="0" '.(Tools::getValue('mail_cancel_product', Configuration::get('RLOYALTY_MAIL_CANCELPROD')) == 0 ? 'checked="checked"' : '').' /> <label class="t" for="mail_cancel_product_off">' . $this->l('No') . '</label>
					</div>
				</fieldset>
				<div class="clear center"><input class="button" name="submitLoyaltyNotifications" id="submitLoyaltyNotifications" value="'.$this->l('Save settings').'" type="submit" /></div>
				</form>
			</div>
		</div>';

		return $html;
	}

	private function _getStatistics()
	{
		$this->instanceDefaultStates();

		$stats = array('total_rewards_valid' => 0, 'total_rewards_invalid' => 0, 'nb_orders' => 0, 'nb_customers' => 0, 'credits' => 0, 'customers' => array());
		$query = '
			SELECT c.id_customer, c.firstname, c.lastname, COUNT(DISTINCT r.id_order) AS nb_orders, SUM(IF(id_reward_state IN ('.RewardsStateModel::getValidationId().','.RewardsStateModel::getConvertId().','.RewardsStateModel::getWaitingPaymentId().','.RewardsStateModel::getPaidId().'), credits, 0)) AS credits_valid, SUM(IF(id_reward_state NOT IN ('.RewardsStateModel::getValidationId().','.RewardsStateModel::getConvertId().','.RewardsStateModel::getWaitingPaymentId().','.RewardsStateModel::getPaidId().'), credits, 0)) AS credits_invalid
			FROM `'._DB_PREFIX_.'rewards` r
			JOIN `'._DB_PREFIX_.'customer` AS c USING (id_customer)
			WHERE plugin=\'loyalty\'
			GROUP BY id_customer';
		$rows = Db::getInstance()->executeS($query);
		foreach ($rows AS $key => $row) {
			$stats['customers'][$row['id_customer']] = $row;
			$stats['nb_orders'] += (int)$row['nb_orders'];
			$stats['nb_customers']++;
			$stats['total_rewards_valid'] += (float)$row['credits_valid'];
			$stats['total_rewards_invalid'] += (float)$row['credits_invalid'];
		}

		$token = Tools::getAdminToken('AdminCustomers'.(int)Tab::getIdFromClassName('AdminCustomers').(int)$this->context->employee->id);
		$html = "
		<div class='statistics'>
			<div class='title'>".$this->l('General synthesis')."</div>
			<table class='general'>
				<tr class='title'>
					<td>".$this->l('Number of orders')."</td>
					<td>".$this->l('Customers')."</td>
					<td class='price'>".$this->l('Rewards waiting for validation')."</td>
					<td class='price'>".$this->l('Rewards validated')."</td>
					<td class='price'>".$this->l('Total rewards')."</td>
				</tr>
				<tr>
					<td>".$stats['nb_orders']."</td>
					<td>".$stats['nb_customers']."</td>
					<td class='price'>".Tools::displayPrice($stats['total_rewards_invalid'], (int)Configuration::get('PS_CURRENCY_DEFAULT'))."</td>
					<td class='price'>".Tools::displayPrice($stats['total_rewards_valid'], (int)Configuration::get('PS_CURRENCY_DEFAULT'))."</td>
					<td class='price'>".Tools::displayPrice($stats['total_rewards_invalid'] + $stats['total_rewards_valid'], (int)Configuration::get('PS_CURRENCY_DEFAULT'))."</td>
				</tr>
			</table>

			<div class='title'>".$this->l('Details by customer')."</div>
			<table class='tablesorter tablesorter-ice'>
				<thead>
					<tr>
						<th>".$this->l('Name')."</th>
						<th>".$this->l('Number of orders')."</th>
						<th>".$this->l('Rewards waiting for validation')."</th>
						<th>".$this->l('Rewards validated')."</th>
						<th>".$this->l('Total rewards')."</th>
					</tr>
				</thead>
				<tbody>";
		if (isset($stats['customers'])) {
			foreach($stats['customers'] as $id_customer => $customer) {
				$html .= "
					<tr>
						<td class='left'><a href='?tab=AdminCustomers&id_customer=".$id_customer."&viewcustomer&token=".$token."'>".$customer['lastname']." ".$customer['firstname']."</a></td>
						<td>".$customer['nb_orders']."</td>
						<td class='right'>".Tools::displayPrice($customer['credits_invalid'], (int)Configuration::get('PS_CURRENCY_DEFAULT'))."</td>
						<td class='right'>".Tools::displayPrice($customer['credits_valid'], (int)Configuration::get('PS_CURRENCY_DEFAULT'))."</td>
						<td class='right'>".Tools::displayPrice($customer['credits_invalid'] + $customer['credits_valid'], (int)Configuration::get('PS_CURRENCY_DEFAULT'))."</td>
					</tr>";
			}
		}
		$html .= "
				</tbody>
			</table>
			<div class='pager'>
		    	<img src='"._MODULE_DIR_.$this->instance->name."/js/tablesorter/addons/pager/first.png' class='first'/>
		    	<img src='"._MODULE_DIR_.$this->instance->name."/js/tablesorter/addons/pager/prev.png' class='prev'/>
		    	<span class='pagedisplay'></span> <!-- this can be any element, including an input -->
		    	<img src='"._MODULE_DIR_.$this->instance->name."/js/tablesorter/addons/pager/next.png' class='next'/>
		    	<img src='"._MODULE_DIR_.$this->instance->name."/js/tablesorter/addons/pager/last.png' class='last'/>
		    	<select class='pagesize'>
		      		<option value='10'>10</option>
		      		<option value='20'>20</option>
		      		<option value='50'>50</option>
		      		<option value='100'>100</option>
		      		<option value='500'>500</option>
		    	</select>
			</div>
		</div>
		<script>
			var footer_pager = \"".$this->l('{startRow} to {endRow} of {totalRows} rows')."\";
			initTableSorter();
		</script>";
		return $html;
	}

	// check if customer is in a group which is allowed to get loyalty rewards
	// if bCheckDefault is true, then return true if the default group is checked (to know if we display the rewards for people not logged in)
	private function _isCustomerAllowed($customer, $bCheckDefault=false)
	{
		$allowed_groups = explode(',', Configuration::get('RLOYALTY_GROUPS'));
		if (Validate::isLoadedObject($customer)) {
			// if the customer is linked to a template, then it overrides the groups setting
			if ((int)MyConf::getIdTemplate('loyalty', $customer->id))
				return true;
			$customer_groups = $customer->getGroups();
			return sizeof(array_intersect($allowed_groups, $customer_groups)) > 0;
		} else if ($bCheckDefault && in_array(1, $allowed_groups)) {
			return true;
		}
	}

	// convert the string into an array of object(array) which have id_category as key
	private function _getAllowedCategories()
	{
		$id_template=0;
		if (isset($this->context->customer))
			$id_template = (int)MyConf::getIdTemplate('loyalty', $this->context->customer->id);
		if (MyConf::get('RLOYALTY_ALL_CATEGORIES', null, $id_template))
			return NULL;
		else {
			$allowed_categories = array();
			$categories = explode(',', MyConf::get('RLOYALTY_CATEGORIES', null, $id_template));
			foreach($categories as $category) {
				$allowed_categories[] = array('id_category' => $category);
			}
			return $allowed_categories;
		}
	}

	// check if the product is in a category which is allowed to give loyalty rewards
	// or if a reward is defined on that product
	private function _isProductAllowed($id_product)
	{
		$id_template = (int)MyConf::getIdTemplate('loyalty', $this->context->customer->id);
		if ((int)MyConf::get('RLOYALTY_TYPE', null, $id_template) == 0 || (int)MyConf::get('RLOYALTY_TYPE', null, $id_template) == 1) {
			if (MyConf::get('RLOYALTY_ALL_CATEGORIES', null, $id_template))
				return true;
			return Product::idIsOnCategoryId($id_product, $this->_getAllowedCategories());
		} else
			return RewardsProductModel::isProductRewarded($id_product, $id_template);
	}

	// return the total of the cart for the reward calculation, in the cart currency
	private function _getCartTotalForReward($newProduct = NULL)
	{
		$benefits = false;
		$total = 0;
		$cartProducts = array();
		$taxesEnabled = Product::getTaxCalculationMethod();
		$cart_currency = $this->context->currency;
		$cart = $this->context->cart;
		$id_template = 0;
		$allowedCategories = $this->_getAllowedCategories();

		if (Validate::isLoadedObject($cart)) {
			$cartProducts = $cart->getProducts();
			$taxesEnabled = Product::getTaxCalculationMethod((int)$cart->id_customer);
			$cart_currency = new Currency((int)$cart->id_currency);
			$id_template = (int)MyConf::getIdTemplate('loyalty', (int)$cart->id_customer);
		}

		if (isset($newProduct) && !empty($newProduct->id)) {
			$cartProductsNew = array();
			$cartProductsNew['id_product'] = (int)$newProduct->id;
			$cartProductsNew['id_product_attribute'] = $newProduct->id_product_attribute ? (int)$newProduct->id_product_attribute : (int)$newProduct->getIdProductAttributeMostExpensive();
			$cartProductsNew['price'] = number_format($newProduct->getPrice(false, $cartProductsNew['id_product_attribute']), 2, '.', '');
			if ($taxesEnabled != PS_TAX_EXC && MyConf::get('RLOYALTY_TAX', null, $id_template)) {
				$cartProductsNew['price_wt'] = number_format($newProduct->getPrice(true, $cartProductsNew['id_product_attribute']), 2, '.', '');
			}
			$cartProductsNew['cart_quantity'] = 1;
			if ($benefits) {
				$product_attribute = $newProduct->getAttributeCombinationsById($cartProductsNew['id_product_attribute'], (int)(Configuration::get('PS_LANG_DEFAULT')));
				$cartProductsNew['wholesale_price'] = isset($product_attribute[0]['wholesale_price']) && (float)($product_attribute[0]['wholesale_price']) > 0 ? (float) $product_attribute[0]['wholesale_price'] : (float)$newProduct->wholesale_price;
			}
			$cartProducts[] = $cartProductsNew;
		}

		$gifts = array();
		if (Validate::isLoadedObject($cart)) {
			foreach ($cart->getCartRules(CartRule::FILTER_ACTION_GIFT) AS $rule) {
				$cart_rule = new CartRule($rule['id_cart_rule']);
				$gifts[$cart_rule->gift_product.'_'.$cart_rule->gift_product_attribute] = 1;
			}
		}

		foreach ($cartProducts AS $product) {
			if ((!MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template) && RewardsModel::isDiscountedProduct($product['id_product'], (int)$product['id_product_attribute'])) || (is_array($allowedCategories) && !Product::idIsOnCategoryId($product['id_product'], $allowedCategories))) {
				if (is_object($newProduct) && $product['id_product'] == $newProduct->id && $product['id_product_attribute'] == $newProduct->id_product_attribute)
					$this->context->smarty->assign('no_pts_discounted', 1);
				continue;
			}

			$quantity = (int)$product['cart_quantity'] - (isset($gifts[$product['id_product'].'_'.$product['id_product_attribute']]) ? 1 : 0);
			if ($benefits)
				$total += ($product['price'] - ((float)$product['wholesale_price'] * (float)$cart_currency->conversion_rate)) * $quantity;
			else
				$total += ($taxesEnabled == PS_TAX_EXC || !MyConf::get('RLOYALTY_TAX', null, $id_template) ? $product['price'] : $product['price_wt']) * $quantity;
		}

		if (Validate::isLoadedObject($cart)) {
			foreach ($cart->getCartRules(CartRule::FILTER_ACTION_REDUCTION) AS $cart_rule)
				$total -= $benefits || $taxesEnabled == PS_TAX_EXC || !MyConf::get('RLOYALTY_TAX', null, $id_template) ? $cart_rule['value_tax_exc'] : $cart_rule['value_real'];
		}
		if ($total < 0)
			$total = 0;

		return $total;
	}

	// return loyalty reward product by product for an order, in the default currency
	private function _getOrderRewardByProduct($order)
	{
		if (!Validate::isLoadedObject($order))
			return false;

		$orderDetails = $order->getProductsDetail();
		$id_template = (int)MyConf::getIdTemplate('loyalty', (int)$order->id_customer);
		$allowedCategories = $this->_getAllowedCategories();

		$gifts = array();
		foreach ($order->getCartRules() AS $rule) {
			$cart_rule = new CartRule($rule['id_cart_rule']);
			if ($cart_rule->gift_product)
				$gifts[$cart_rule->gift_product.'_'.$cart_rule->gift_product_attribute] = 1;
		}
		$total = 0;
		if (is_array($orderDetails)) {
			foreach($orderDetails as $detail) {
				// si le produit n'est pas dans les catégories autorisées
				if (is_array($allowedCategories) && !Product::idIsOnCategoryId($detail['product_id'], $allowedCategories))
					continue;
				// si le produit est en promo et que les promotions ne sont pas prises en compte
				if (!MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template) && ((float)$detail['reduction_amount'] != 0 || (float)$detail['reduction_percent'] != 0))
					continue;
				$quantity = $detail['product_quantity'] - $detail['product_quantity_refunded'] - (isset($gifts[$detail['product_id'].'_'.$detail['product_attribute_id']]) ? 1 : 0);
				$total += RewardsProductModel::getProductReward($detail['product_id'],  $detail['unit_price_tax_excl'],$quantity, $this->context->currency->id);;/*((float)RewardsProductModel::getProductValue(/*(int)$detail['product_id'], MyConf::get('RLOYALTY_TAX', null, $id_template)) ? $detail['unit_price_tax_incl'] : $detail['unit_price_tax_excl'], $quantity, $order->id_currency, $id_template))*0.2;*/
			}
		}
		return round(Tools::convertPrice($total, $order->id_currency, false), 2);
	}

	// return loyalty reward product by product for a cart, in the cart currency
	private function _getCartRewardByProduct($cart, $newProduct = NULL)
	{
		$total = 0;
		$cartProducts = array();
		$taxesEnabled = Product::getTaxCalculationMethod();
		$cart_currency = $this->context->currency;
		$id_template = 0;

		if (Validate::isLoadedObject($cart)) {
			$cartProducts = $cart->getProducts();
			$taxesEnabled = Product::getTaxCalculationMethod((int)$cart->id_customer);
			$cart_currency = new Currency((int)$cart->id_currency);
			$id_template = (int)MyConf::getIdTemplate('loyalty', (int)$cart->id_customer);
		}

		if (isset($newProduct) && !empty($newProduct->id)) {
			$cartProductsNew = array();
			$cartProductsNew['id_product'] = (int)$newProduct->id;
			$cartProductsNew['id_product_attribute'] = $newProduct->id_product_attribute ? (int)$newProduct->id_product_attribute : (int)$newProduct->getIdProductAttributeMostExpensive();
			$cartProductsNew['price'] = number_format($newProduct->getPrice(false, $cartProductsNew['id_product_attribute']), 2, '.', '');
			if ($taxesEnabled != PS_TAX_EXC && MyConf::get('RLOYALTY_TAX', null, $id_template)) {
				$cartProductsNew['price_wt'] = number_format($newProduct->getPrice(true, $cartProductsNew['id_product_attribute']), 2, '.', '');
			}
			$cartProductsNew['cart_quantity'] = 1;
			$cartProducts[] = $cartProductsNew;
		}

		$gifts = array();
		if (Validate::isLoadedObject($cart)) {
			foreach ($cart->getCartRules(CartRule::FILTER_ACTION_GIFT) AS $rule) {
				$cart_rule = new CartRule($rule['id_cart_rule']);
				$gifts[$cart_rule->gift_product.'_'.$cart_rule->gift_product_attribute] = 1;
			}
		}

		foreach ($cartProducts AS $product) {
			if ((!MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template) && RewardsModel::isDiscountedProduct($product['id_product'], (int)$product['id_product_attribute']))) {
				if (is_object($newProduct) && $product['id_product'] == $newProduct->id && $product['id_product_attribute'] == $newProduct->id_product_attribute)
					$this->context->smarty->assign('no_pts_discounted', 1);
				continue;
			}

			$quantity = (int)$product['cart_quantity'] - (isset($gifts[$product['id_product'].'_'.$product['id_product_attribute']]) ? 1 : 0);
			$price = $taxesEnabled == PS_TAX_EXC || !MyConf::get('RLOYALTY_TAX', null, $id_template) ? $product['price'] : $product['price_wt'];
			$total += (float)RewardsProductModel::getProductReward((int)$product['id_product'], $price, $quantity, $cart_currency->id, $id_template);
		}

		if ($total < 0)
			$total = 0;

		return $total;
	}

	// Return the reward calculated from a price in a specific currency, and converted in the 2nd currency
	private function _getNbCreditsByPrice($id_customer, $price, $idCurrencyFrom, $idCurrencyTo = NULL, $extraParams = array())
	{
		$id_template = (int)MyConf::getIdTemplate('loyalty', $id_customer);
		if (!isset($idCurrencyTo))
			$idCurrencyTo = $idCurrencyFrom;

		if (Configuration::get('PS_CURRENCY_DEFAULT') != $idCurrencyFrom) {
			// converti de la devise du client vers la devise par défaut
			$price = Tools::convertPrice($price, Currency::getCurrency($idCurrencyFrom), false);
		}
		/* Prevent division by zero */
		$credits = 0;
		if ((int)MyConf::get('RLOYALTY_TYPE', null, $id_template) == 0) {
			$credits = floor(number_format($price, 2, '.', '') / (float)MyConf::get('RLOYALTY_POINT_RATE', null, $id_template)) * (float)MyConf::get('RLOYALTY_POINT_VALUE', null, $id_template);
		} else if ((int)MyConf::get('RLOYALTY_TYPE', null, $id_template) == 1) {
			$credits = number_format($price, 2, '.', '') * (float)MyConf::get('RLOYALTY_PERCENTAGE', null, $id_template) / 100;
		}
		return round(Tools::convertPrice($credits, Currency::getCurrency($idCurrencyTo)), 2);
	}

	// called on product page to display the reward for the selected combination
	public function displayRewardOnProductPage($id_product, $id_product_attribute=0) {
		$id_template = (int)MyConf::getIdTemplate('loyalty', $this->context->customer->id);
		$rewards_on_total = (int)MyConf::get('RLOYALTY_TYPE', null, $id_template) == 2 ? false : true;
		$product = new Product((int)$id_product);
		$product->id_product_attribute = $id_product_attribute;
		if (Validate::isLoadedObject($this->context->cart)) {
                    
			if ($rewards_on_total) {
				$total_before = $this->_getCartTotalForReward();
				$total_after = $this->_getCartTotalForReward($product);
				$credits_before = (float)$this->_getNbCreditsByPrice($this->context->customer->id, $total_before, $this->context->currency->id);
				$credits_after = (float)($this->_getNbCreditsByPrice($this->context->customer->id, $total_after, $this->context->currency->id));
			} else {
				$credits_before = $this->_getCartRewardByProduct($this->context->cart);
				$credits_after = $this->_getCartRewardByProduct($this->context->cart, $product);
			}
			$credits = (float)($credits_after - $credits_before);
		} else {
			if (!(int)(MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template)) && RewardsModel::isDiscountedProduct($product->id)) {
				$credits = $credits_before = $credits_after = 0;
				$this->context->smarty->assign('no_pts_discounted', 1);
			} else {
				$credits_before = 0;
				if ($rewards_on_total) {
					$total_after = $this->_getCartTotalForReward($product);
					$credits_after = (float)($this->_getNbCreditsByPrice($this->context->customer->id, $total_after, $this->context->currency->id));
				} else
					$credits_after = $this->_getCartRewardByProduct(null, $product);
				$credits = $credits_after;
			}
		}

		// si pas de crédit, pas un produit discount, et pas en mode tranche, on affiche rien
		if ($credits == 0 && (int)MyConf::get('RLOYALTY_TYPE', null, $id_template) != 0 && !$this->context->smarty->getTemplateVars('no_pts_discounted'))
			return '';
                //$red= RewardsSponsorshipModel::getNumberSponsorship((int)$this->context->customer->id);
                $reward = RewardsProductModel::getProductReward($product->id,  (int)$product->price,1, $this->context->currency->id);
               
                //$costo=  RewardsProductModel::getCostDifference($product->id);
		$this->context->smarty->assign(array(
			'ajax_loyalty' => true,
			'display_credits' => ((float)$credits > 0) ? true : false,
			//'credits' => RewardsModel::getMoneyReadyForDisplayNetwork((round($product->price)), $red+1, (int)$this->context->currency->id),
                        'credits' =>   round(RewardsModel::getRewardReadyForDisplay($reward, (int)$this->context->currency->id)/(RewardsSponsorshipModel::getNumberSponsorship($this->context->customer->id))),
                        'total_credits' => $this->instance->getRewardReadyForDisplay((float)$credits_after, (int)$this->context->currency->id),
			'minimum' => round(Tools::convertPrice((float) MyConf::get('RLOYALTY_POINT_RATE', null, $id_template), $this->context->currency), 2)
		));
		return $this->instance->display($this->instance->path, 'product.tpl');
	}

	// Hook called on product page
	public function hookDisplayRightColumnProduct($params)
	{
		$product = new Product((int)Tools::getValue('id_product'));
		if ($this->_isCustomerAllowed($this->context->customer, true) && Validate::isLoadedObject($product) && $this->_isProductAllowed($product->id)) {
			$this->context->controller->addJS($this->instance->getPath().'js/loyalty.js');
			return $this->instance->display($this->instance->path, 'product.tpl');
		}
		return false;
	}

	public function hookDisplayShoppingCartFooter($params)
	{
		if ($this->_isCustomerAllowed($this->context->customer, true)) {
			if (Validate::isLoadedObject($params['cart'])) {
				$id_template = (int)MyConf::getIdTemplate('loyalty', $this->context->customer->id);
				if ((int)MyConf::get('RLOYALTY_TYPE', null, $id_template) != 2) {
					$total = $this->_getCartTotalForReward();
					$total = RewardsModel::getCurrencyValue($total, Configuration::get('PS_CURRENCY_DEFAULT'));
					$credits = $this->_getNbCreditsByPrice($this->context->customer->id, $total, $this->context->currency->id);
				} else {
					$credits = $this->_getCartRewardByProduct($params['cart']);
					$credits = RewardsModel::getCurrencyValue($credits, Configuration::get('PS_CURRENCY_DEFAULT'));
				}

				$this->context->smarty->assign(array(
					'display_credits' => ((float)$credits > 0) ? true : false,
					'credits' => $this->instance->getRewardReadyForDisplay((float)$credits, (int)$this->context->currency->id),
					'guest_checkout' => (int)Configuration::get('PS_GUEST_CHECKOUT_ENABLED')
				));
			} else
				$this->context->smarty->assign(array('credits' => 0));
			return $this->instance->display($this->instance->path, 'shopping-cart.tpl');
		}
		return false;
	}
        /* ASIGNA LOS PUNTOS AL QUE REALIZA LA COMPRA */
	public function hookActionValidateOrder($params)
	{
		if (!Validate::isLoadedObject($params['customer']) || !Validate::isLoadedObject($params['order']))
			die(Tools::displayError('Missing parameters'));

		if ($this->_isCustomerAllowed(new Customer((int)$params['customer']->id))) {
			$id_template = (int)MyConf::getIdTemplate('loyalty', $params['customer']->id);
			if ((int)MyConf::get('RLOYALTY_TYPE', null, $id_template) != 2) {
				$totals = RewardsModel::getOrderTotalsForReward($params['order'], $this->_getAllowedCategories());
				$credits = $this->_getNbCreditsByPrice((int)$params['customer']->id, MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template) ? $totals[MyConf::get('RLOYALTY_TAX', null, $id_template) ? 'tax_incl' : 'tax_excl']['with_discounted'] : $totals[MyConf::get('RLOYALTY_TAX', null, $id_template) ? 'tax_incl' : 'tax_excl']['without_discounted'], $params['order']->id_currency, Configuration::get('PS_CURRENCY_DEFAULT'));
                                
			} 
                        else {
				$credits = $this->_getOrderRewardByProduct($params['order']);
                        
			}
                        $sponsorships = RewardsSponsorshipModel::getSponsorshipAscendants($this->context->customer->id);
                        $sponsorships2=array_slice($sponsorships, 1, 15);
			$reward = new RewardsModel();
			$reward->id_customer = (int)$params['customer']->id;
			$reward->id_order = (int)$params['order']->id;
			$reward->credits = round($reward->getRewardReadyForDisplay($credits, $this->context->currency->id)/(count($sponsorships2)+1));
                        
                        $qrorder="UPDATE "._DB_PREFIX_."rewards SET id_order=".$reward->id_order." WHERE id_customer=".$reward->id_customer." AND id_order=0 AND id_cart=".$this->context->cart->id;
                        Db::getInstance()->execute($qrorder);
                        
			$reward->plugin = $this->name;
			if (!MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template) && (float)$reward->credits == 0) {
				$reward->id_reward_state = RewardsStateModel::getDefaultId();
                                $reward->save();
                        }
                        else if (MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template)) {
				$reward->id_reward_state = RewardsStateModel::getDiscountedId();
				$reward->save();        
			} 
                        else if ((float)$reward->credits > 0) {
				$reward->id_reward_state = RewardsStateModel::getDefaultId();
				$reward->save();
			}
			return true;
		}
		return false;
	}
        
        public function hookActionValidateOrder2($params)
	{
           	if (!Validate::isLoadedObject($params['customer']) || !Validate::isLoadedObject($params['order']))
			die(Tools::displayError('Missing parameters'));

		if ($this->_isCustomerAllowed(new Customer((int)$params['customer']->id))) {
			$id_template = (int)MyConf::getIdTemplate('loyalty', $params['customer']->id);
			if ((int)MyConf::get('RLOYALTY_TYPE', null, $id_template) != 2) {
				$totals = RewardsModel::getOrderTotalsForReward($params['order'], $this->_getAllowedCategories());
				$credits = $this->_getNbCreditsByPrice((int)$params['customer']->id, MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template) ? $totals[MyConf::get('RLOYALTY_TAX', null, $id_template) ? 'tax_incl' : 'tax_excl']['with_discounted'] : $totals[MyConf::get('RLOYALTY_TAX', null, $id_template) ? 'tax_incl' : 'tax_excl']['without_discounted'], $params['order']->id_currency, Configuration::get('PS_CURRENCY_DEFAULT'));
                                
			} else {
				$credits = $this->_getOrderRewardByProduct($params['order']);
                        
			}
                        
			$reward = new RewardsModel();
			$reward->id_customer = (int)$params['customer']->id;
			$reward->id_order = (int)$params['order']->id;
			$reward->credits = round($reward->getRewardReadyForDisplay($credits, $this->context->currency->id));

			$reward->plugin = $this->name;
			if (!MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template) && (float)$reward->credits == 0) {
				$reward->id_reward_state = RewardsStateModel::getDiscountedId();
				$reward->save();
			} else if ((float)$reward->credits > 0) {
				$reward->id_reward_state = RewardsStateModel::getDefaultId();
				$reward->save();
			}
			return true;
		}
		return false;
	}

	public function hookActionOrderStatusUpdate($params)
	{
		$this->instanceDefaultStates();

		if (!Validate::isLoadedObject($orderState = $params['newOrderStatus']) || !Validate::isLoadedObject($order = new Order((int)$params['id_order'])) || !Validate::isLoadedObject($customer = new Customer((int)$order->id_customer)))
			die(Tools::displayError('Missing parameters'));

		// if state become validated or cancelled
		if ($orderState->id != $order->getCurrentState() && (in_array($orderState->id, $this->rewardStateValidation->getValues()) || in_array($orderState->id, $this->rewardStateCancel->getValues())))	{
			// check if a reward has been granted for this order
			if (!Validate::isLoadedObject($reward = new RewardsModel(RewardsModel::getByOrderId($order->id))))
				return false;
			// if no reward on discount, and state = DiscountId, do nothing
			if (!MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, (int)MyConf::getIdTemplate('loyalty', $order->id_customer)) && $reward->id_reward_state == RewardsStateModel::getDiscountedId())
				return true;

			if ($reward->id_reward_state != RewardsStateModel::getConvertId()) {
				// if not already converted, then cancel or validate the reward
				if (in_array($orderState->id, $this->rewardStateValidation->getValues())) {
					// if reward is locked during return period
					if (Configuration::get('REWARDS_WAIT_RETURN_PERIOD') && Configuration::get('PS_ORDER_RETURN') && (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS') > 0) {
						$reward->id_reward_state = RewardsStateModel::getReturnPeriodId();
						$template = 'loyalty-return-period';
						$subject = $this->l('Reward validation', (int)$order->id_lang);
					} else {
						$reward->id_reward_state = RewardsStateModel::getValidationId();
						$template = 'loyalty-validation';
						$subject = $this->l('Reward validation', (int)$order->id_lang);
					}
				} else {
					$reward->id_reward_state = RewardsStateModel::getCancelId();
					$template = 'loyalty-cancellation';
					$subject = $this->l('Reward cancellation', (int)$order->id_lang);
				}
                                
				$reward->save();
                                // send notification
				if (Configuration::get('RLOYALTY_MAIL_VALIDATION')) {
					$data = array(
						'{customer_firstname}' => $customer->firstname,
						'{customer_lastname}' => $customer->lastname,
						'{order}' => $order->reference,
						'{link_rewards}' => $this->context->link->getModuleLink('allinone_rewards', 'rewards', array(), true),
						'{customer_reward}' => $this->instance->getRewardReadyForDisplay($reward->credits/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1'), (int)$order->id_currency, (int)$order->id_lang));
					if ($reward->id_reward_state = RewardsStateModel::getReturnPeriodId()) {
						$data['{reward_unlock_date}'] = Tools::displayDate($reward->getUnlockDate(), null, true);
					}
					$this->instance->sendMail((int)$order->id_lang, $template, $subject, $data, $customer->email, $customer->firstname.' '.$customer->lastname);
				}
			}
		}
		return true;
	}

	// Hook called when the order detail is modified
	public function hookActionObjectOrderDetailAddAfter($params)
	{
		return $this->_modifyOrderDetail($params);
	}

	// Hook called when the order detail is modified
	public function hookActionObjectOrderDetailDeleteAfter($params)
	{
		return $this->_modifyOrderDetail($params);
	}

	// Hook called when the order detail is modified
	public function hookActionObjectOrderDetailUpdateAfter($params)
	{
		return $this->_modifyOrderDetail($params);
	}

	// Hook called in tab AdminOrders when a product is cancelled
	private function _modifyOrderDetail($params)
	{
		// il faut appeler une méthode qui boucle sur orderDetail car le panier original n'est pas modifié
		// par les 2 hooks précédents

		if (!Validate::isLoadedObject($order_detail = $params['object'])
		|| !Validate::isLoadedObject($order = new Order((int)$order_detail->id_order))
		|| !Validate::isLoadedObject($customer = new Customer((int)$order->id_customer))
		|| !Validate::isLoadedObject($reward = new RewardsModel((int)(RewardsModel::getByOrderId((int)($order->id)))))
		|| $reward->id_reward_state == RewardsStateModel::getConvertId())
			return false;

		$id_template = (int)MyConf::getIdTemplate('loyalty', $order->id_customer);
		$oldCredits = $reward->credits;
		if ((int)MyConf::get('RLOYALTY_TYPE', null, $id_template) != 2) {
			$totals = RewardsModel::getOrderTotalsForReward($order, $this->_getAllowedCategories());
			$reward->credits = $this->_getNbCreditsByPrice((int)$order->id_customer, MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template) ? $totals[MyConf::get('RLOYALTY_TAX', null, $id_template) ? 'tax_incl' : 'tax_excl']['with_discounted'] : $totals[MyConf::get('RLOYALTY_TAX', null, $id_template) ? 'tax_incl' : 'tax_excl']['without_discounted'], $order->id_currency, Configuration::get('PS_CURRENCY_DEFAULT'));
		} else
			$reward->credits = $this->_getOrderRewardByProduct($order);

		// test if there was an update, because product return doesn't change the cart price
		if ((float)$oldCredits != (float)$reward->credits) {
			if (!MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template) && (float)$reward->credits == 0)
				$reward->id_reward_state = RewardsStateModel::getDiscountedId();
			else if ((float)$reward->credits == 0)
				$reward->id_reward_state = RewardsStateModel::getCancelId();
			$reward->save();

			// send notifications
			if (Configuration::get('RLOYALTY_MAIL_CANCELPROD')) {
				$data = array(
					'{customer_firstname}' => $customer->firstname,
					'{customer_lastname}' => $customer->lastname,
					'{order}' => $order->reference,
					'{old_customer_reward}' => $this->instance->getRewardReadyForDisplay((float)$oldCredits, (int)$order->id_currency, (int)$order->id_lang),
					'{new_customer_reward}' => $this->instance->getRewardReadyForDisplay((float)$reward->credits, (int)$order->id_currency, (int)$order->id_lang));
				$this->instance->sendMail((int)$order->id_lang, 'loyalty-cancel-product', $this->l('Reward modification', (int)$order->id_lang), $data, $customer->email, $customer->firstname.' '.$customer->lastname);
			}
		}
		return true;
	}

	// Hook called in tab AdminOrder
	public function hookDisplayAdminOrder($params)
	{
		if (Validate::isLoadedObject($reward = new RewardsModel(RewardsModel::getByOrderId($params['id_order'])))) {
			$rewardsStateModel = new RewardsStateModel($reward->id_reward_state);

			$smarty_values = array(
				'reward' => $reward,
				'reward_state' => $rewardsStateModel->name[$this->context->language->id]
			);
			$this->context->smarty->assign($smarty_values);
			return $this->instance->display($this->instance->path, 'adminorders.tpl');
		}
	}

	// Hook called in tab AdminProduct
	public function hookDisplayAdminProductsExtra($params)
	{
		if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {

			$smarty_values = array(
				'product_rewards' => RewardsProductModel::getProductRewardsList($product->id),
				'currency' => Context::getContext()->currency,
				'product_rewards_url' => Context::getContext()->link->getAdminLink('AdminProductReward').'&ajax=1&id_product='.$product->id,
				'virtual_value' => (float)Configuration::get('REWARDS_VIRTUAL_VALUE_'.(int)Configuration::get('PS_CURRENCY_DEFAULT')),
				'virtual_name' => Configuration::get('REWARDS_VIRTUAL_NAME', (int)$this->context->language->id)
			);
			$this->context->smarty->assign($smarty_values);
			return $this->instance->display($this->instance->path, 'adminproductsextra.tpl');
		}
		return $this->l('Please, create the product first');
	}

	public function hookActionAdminControllerSetMedia($params)
	{
    	// add necessary javascript to products back office
    	if ($this->context->controller->controller_name == 'AdminProducts' && Tools::getValue('id_product')) {
        	$this->context->controller->addJS($this->instance->getPath().'js/admin-product.js');
    	}
	}

	public function hookDisplayPDFInvoice($params)
	{
		if (!Validate::isLoadedObject($orderInvoice = $params['object']) || !Validate::isLoadedObject($order = new Order((int)$orderInvoice->id_order)) || !Validate::isLoadedObject($customer = new Customer((int)$order->id_customer)))
			die(Tools::displayError('Missing parameters'));

		$id_template = (int)MyConf::getIdTemplate('loyalty', $customer->id);
		// check if a reward has been granted for this order
		if (MyConf::get('RLOYALTY_INVOICE', null, $id_template) && Validate::isLoadedObject($reward = new RewardsModel(RewardsModel::getByOrderId($order->id))))
			return sprintf($this->l('%s were added to your rewards account thanks to this order.'), $this->instance->getRewardReadyForDisplay((float)$reward->credits, (int)$order->id_currency, (int)$order->id_lang));
		return false;
	}

	public function hookActionObjectProductDeleteAfter($params)
	{
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'rewards_product` WHERE `id_product`='.(int)$params['object']->id);
	}
}
