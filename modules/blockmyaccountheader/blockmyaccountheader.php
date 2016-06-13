<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class BlockMyAccountHeader extends Module
{
	public function __construct()
	{
		$this->name = 'blockmyaccountheader';
		$this->tab = 'front_office_features';
		$this->version = '1.4.0';
		$this->author = 'Ingenio Contenido Digital';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('My Account Header');
		$this->description = $this->l('Displays a block with links relative to a user\'s account in header.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		if (!$this->addMyAccountBlockHeaderHook() 
			|| !parent::install() 
                        || !$this->registerHook('top'))
			return false;
		return true;
	}

	public function uninstall()
	{
		return (parent::uninstall() && $this->removeMyAccountBlockHeaderHook());
	}

	private function addMyAccountBlockHeaderHook()
	{
		return Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'hook` (`name`, `title`, `description`, `position`) VALUES (\'displayMyAccountBlockHeader\', \'My account block header\', \'Display extra informations inside the "my account" block in header\', 1)');
	}

	private function removeMyAccountBlockHeaderHook()
	{
		return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'hook` WHERE `name` = \'displayMyAccountBlockHeader\'');
	}
        
        public function hookTop($params)
	{
                $this->context->controller->addCSS(($this->_path).'blockmyaccountheader.css', 'all');
		return $this->display(__FILE__,'blockmyaccountheader.tpl');
	}
}


