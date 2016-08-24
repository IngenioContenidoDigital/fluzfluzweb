<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class DiscountControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'discount';
    public $authRedirection = 'discount';
    public $ssl = true;

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
        $members = array();
        $searchnetwork = strtolower(Tools::getValue('searchnetwork'));

        foreach ($tree as $sponsor) {
            if ( $this->context->customer->id != $sponsor['id'] ) {
                $customer = new Customer($sponsor['id']);
                $name = strtolower($customer->firstname." ".$customer->lastname);

                if ( $searchnetwork != "" ) {
                    $coincidence = strpos($name, $searchnetwork);
                    if ( $coincidence !== false ) {
                        $members[$sponsor['id']]['name'] = $name;
                        $members[$sponsor['id']]['dateadd'] = date_format( date_create($customer->date_add) ,"d/m/y");
                        $members[$sponsor['id']]['level'] = $sponsor['level'];
                    }
                } else {
                    $members[$sponsor['id']]['name'] = $name;
                    $members[$sponsor['id']]['dateadd'] = date_format( date_create($customer->date_add) ,"d/m/y");
                    $members[$sponsor['id']]['level'] = $sponsor['level'];
                }
            }
        }
        asort($members);
        $this->context->smarty->assign('members', $members);
        $this->context->smarty->assign('searchnetwork', $searchnetwork);
        
        $this->addJS(_THEME_JS_DIR_.'discount.js');
        $this->addCSS(_THEME_CSS_DIR_.'discount.css');
        $this->setTemplate(_PS_THEME_DIR_.'discount.tpl');
    }
}

/*
 * $pos = strpos($mystring, $findme);
                if ( $pos !== false ) {
                    echo "La cadena '$findme' fue encontrada en la cadena '$mystring'";
                    echo " y existe en la posición $pos";
                } else {
                    echo "La cadena '$findme' no fue encontrada en la cadena '$mystring'";
                }
 */