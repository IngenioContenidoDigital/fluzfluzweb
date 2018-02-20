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

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/allinone_rewards.php');

if (Tools::getValue('secure_key') && Configuration::get('REWARDS_USE_CRON')) {
	$secureKey = Configuration::get('REWARDS_CRON_SECURE_KEY');
	if (!empty($secureKey) AND $secureKey === Tools::getValue('secure_key')) {
		//RewardsModel::checkRewardsStates();
		RewardsAccountModel::sendReminder();
	}
}