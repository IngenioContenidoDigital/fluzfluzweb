<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
/**
 * @property Cart $object
 */
class AdminCartsController extends AdminCartsControllerCore
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'cart';
        $this->className = 'Cart';
        $this->lang = false;
        $this->explicitSelect = true;
        $this->addRowAction('view');
        $this->addRowAction('delete');
        $this->allow_export = true;
        $this->_orderWay = 'DESC';
        $this->_select = 'CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) `customer`, a.id_cart total, ca.name carrier,
		IF (IFNULL(o.id_order, \''.$this->l('Non ordered').'\') = \''.$this->l('Non ordered').'\', IF(TIME_TO_SEC(TIMEDIFF(\''.pSQL(date('Y-m-d H:i:00', time())).'\', a.`date_add`)) > 3600, \''.$this->l('Abandoned cart').'\', \''.$this->l('Non ordered').'\'), o.id_order) AS status, IF(o.id_order, 1, 0) badge_success, IF(o.id_order, 0, 1) badge_danger, IF(co.id_guest, 1, 0) id_guest';
        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = a.id_customer)
		LEFT JOIN '._DB_PREFIX_.'currency cu ON (cu.id_currency = a.id_currency)
		LEFT JOIN '._DB_PREFIX_.'carrier ca ON (ca.id_carrier = a.id_carrier)
		LEFT JOIN '._DB_PREFIX_.'orders o ON (o.id_cart = a.id_cart)
		LEFT JOIN `'._DB_PREFIX_.'connections` co ON (a.id_guest = co.id_guest AND TIME_TO_SEC(TIMEDIFF(\''.pSQL(date('Y-m-d H:i:00', time())).'\', co.`date_add`)) < 1800)';
        if (Tools::getValue('action') && Tools::getValue('action') == 'filterOnlyAbandonedCarts') {
            $this->_having = 'status = \''.$this->l('Abandoned cart').'\'';
        } else {
            $this->_use_found_rows = false;
        }
        $this->fields_list = array(
            'id_cart' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'status' => array(
                'title' => $this->l('Order ID'),
                'align' => 'text-center',
                'badge_danger' => true,
                'havingFilter' => true
            ),
            'customer' => array(
                'title' => $this->l('Customer'),
                'filter_key' => 'c!lastname'
            ),
            'total' => array(
                'title' => $this->l('Total'),
                'callback' => 'getOrderTotalUsingTaxCalculationMethod',
                'orderby' => false,
                'search' => false,
                'align' => 'text-right',
                'badge_success' => true
            ),
            'carrier' => array(
                'title' => $this->l('Carrier'),
                'align' => 'text-left',
                'callback' => 'replaceZeroByShopName',
                'filter_key' => 'ca!name'
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'align' => 'text-left',
                'type' => 'datetime',
                'class' => 'fixed-width-lg',
                'filter_key' => 'a!date_add'
            ),
            'id_guest' => array(
                'title' => $this->l('Online'),
                'align' => 'text-center',
                'type' => 'bool',
                'havingFilter' => true,
                'class' => 'fixed-width-xs',
                'icon' => array(0 => 'icon-', 1 => 'icon-user')
            )
        );
        $this->shopLinkType = 'shop';
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );
        AdminController::__construct();
    }
}