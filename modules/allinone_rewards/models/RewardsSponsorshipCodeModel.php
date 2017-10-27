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

class RewardsSponsorshipCodeModel extends ObjectModel
{
	public $id_sponsor;
	public $code;

	public static $definition = array(
		'table' => 'rewards_sponsorship_code',
		'primary' => 'id_sponsor',
		'fields' => array(
			'id_sponsor' =>			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'code' =>				array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'size' => 20)
		),
	);

	public static function getIdSponsorByCode($code)
	{
		return Db::getInstance()->getValue("SELECT rsc.id_sponsor FROM "._DB_PREFIX_."rewards_sponsorship_code rsc 
                                                    INNER JOIN "._DB_PREFIX_."customer c ON (rsc.id_sponsor = c.id_customer)
                                                    WHERE c.active=1 AND c.kick_out=0 AND rsc.code='".pSQL($code)."'");
	}
        
        public static function getCodeSponsorById($id_customer)
	{
		return Db::getInstance()->getValue("SELECT rsc.code FROM "._DB_PREFIX_."rewards_sponsorship_code rsc
                                                    INNER JOIN "._DB_PREFIX_."customer c ON (rsc.id_sponsor = c.id_customer)
                                                    WHERE c.active=1 AND c.kick_out=0 AND rsc.id_sponsor=".pSQL($id_customer));
	}
}