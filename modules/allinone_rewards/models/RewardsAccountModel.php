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

class RewardsAccountModel extends ObjectModel
{
	public $id_customer;
	public $date_last_remind;
	public $date_add;
	public $date_upd;

	public static $definition = array(
		'table' => 'rewards_account',
		'primary' => 'id_customer',
		'fields' => array(
			'id_customer' =>		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'date_last_remind' =>	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_add' =>			array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' =>			array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		)
	);

	// list all people to remind having at least 1 reward which is older than REWARDS_REMINDER_FREQUENCY so we don't remind new accounts immediately
	static public function sendReminder($id_customer=NULL) {
		if (Configuration::get('REWARDS_REMINDER') || !is_null($id_customer)) {
			$context = Context::getContext();
			$lang = (int)Configuration::get('PS_LANG_DEFAULT');

			$where  = '
				FROM `'._DB_PREFIX_.'rewards_account` AS ra
				JOIN `'._DB_PREFIX_.'customer` AS c ON(ra.id_customer = c.id_customer AND c.active = 1)
				JOIN `'._DB_PREFIX_.'rewards` AS r ON(ra.id_customer = r.id_customer AND r.id_reward_state=2)
				WHERE '.
				(empty($id_customer) ? '
				(ra.date_last_remind IS NULL OR ra.date_last_remind=0 OR DATE_ADD(ra.date_last_remind, INTERVAL '.Configuration::get('REWARDS_REMINDER_FREQUENCY').' DAY) < NOW())
				AND EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'rewards` AS r2 WHERE r2.id_customer=r.id_customer AND DATE_ADD(r2.date_add, INTERVAL '.Configuration::get('REWARDS_REMINDER_FREQUENCY').' DAY) < NOW())
				GROUP BY id_customer
				HAVING SUM(r.credits) >= '.(float)Configuration::get('REWARDS_REMINDER_MINIMUM') : ' ra.id_customer='.(int)$id_customer);

			$query = 'SELECT ra.id_customer, email, firstname, lastname, SUM(credits) AS total'.(version_compare(_PS_VERSION_, '1.5.4.0', '>=') ? ', id_lang':'').$where.(Configuration::get('REWARDS_USE_CRON') ? '' : ' LIMIT 20');
			$rows = Db::getInstance()->ExecuteS($query);
			if (is_array($rows)) {
				$module = new allinone_rewards();
				foreach ($rows AS $row) {
					if (version_compare(_PS_VERSION_, '1.5.4.0', '>='))
						$lang = (int)$row['id_lang'];
					$data = array(
							'{firstname}' => $row['firstname'],
							'{lastname}' => $row['lastname'],
							'{rewards}' => $module->getRewardReadyForDisplay((float)$row['total'], (int)Configuration::get('PS_CURRENCY_DEFAULT'), $lang),
							'{link_rewards}' => $context->link->getModuleLink('allinone_rewards', 'rewards', array(), true));
					$module->sendMail($lang, 'rewards-reminder', $module->getL('reminder', $lang), $data, $row['email'], $row['firstname'].' '.$row['lastname']);
					Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'rewards_account` ra2 SET date_last_remind=NOW() WHERE id_customer='.(int)$row['id_customer']);
				}
			}
		}
	}
}
