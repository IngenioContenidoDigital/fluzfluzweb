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

class HistoryControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'history';
    public $authRedirection = 'history';
    public $ssl = true;

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(array(
            _THEME_CSS_DIR_.'history.css',
            _THEME_CSS_DIR_.'addresses.css'
        ));
        $this->addJS(array(
            _THEME_JS_DIR_.'history.js',
            _THEME_JS_DIR_.'tools.js' // retro compat themes 1.5
        ));
        $this->addJqueryPlugin(array('scrollTo', 'footable', 'footable-sort'));
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if ($orders = Order::getCustomerOrders($this->context->customer->id)) {
            foreach ($orders as &$order) {
                $myOrder = new Order((int)$order['id_order']);
                if (Validate::isLoadedObject($myOrder)) {
                    $order['virtual'] = $myOrder->isVirtual(false);
                }
            }
        }
        
        $query = 'SELECT a.id_product as id_product, a.price as price FROM '._DB_PREFIX_.'product as a
                  LEFT JOIN '._DB_PREFIX_.'customer b ON b.id_customer = '.$this->context->customer->id;
                $row = Db::getInstance()->getRow($query);
                $productId = $row['id_product'];
                $price = $row['price'];
                
                $this->context->smarty->assign('productId', $productId);
                $this->context->smarty->assign('price', $price);
                
        $priceP = (int)$price - RewardsProductModel::getCostDifference($productId);
        $productP=RewardsModel::getRewardReadyForDisplay($priceP, $this->context->currency->id)/(RewardsSponsorshipModel::getNumberSponsorship($this->context->customer->id)+1);
        
        $this->context->smarty->assign(array(
            'orders' => $orders,
            'productP' => $productP,
            'invoiceAllowed' => (int)Configuration::get('PS_INVOICE'),
            'reorderingAllowed' => !(bool)Configuration::get('PS_DISALLOW_HISTORY_REORDERING'),
            'products'=>$this->productHistory(),
            'slowValidation' => Tools::isSubmit('slowvalidation')
        ));

        $this->setTemplate(_PS_THEME_DIR_.'history.tpl');
    }
    
    public function productHistory(){
        
        $query = 'SELECT p.price_shop as price_shop, a.product_id AS idProduct, d.link_rewrite as link_rewrite, b.id_image AS image, a.total_price_tax_incl as total, a.unit_price_tax_incl AS precio, a.product_quantity_in_stock AS cantidad, a.product_name AS purchase, n.reference AS referencia, n.date_add AS time FROM '._DB_PREFIX_.'orders n
                        LEFT JOIN '._DB_PREFIX_.'order_detail a ON (a.id_order = n.id_order)
			LEFT JOIN '._DB_PREFIX_.'image b ON (b.id_product=a.product_id)  
                        LEFT JOIN '._DB_PREFIX_.'product_lang d ON (d.id_product=a.product_id)
                        LEFT JOIN ps_product p ON (p.id_product = a.product_id) WHERE id_customer = '.$this->context->customer->id.' AND p.reference != "MFLUZ" GROUP BY p.price_shop, a.product_id,d.link_rewrite,b.id_image,
                        a.total_price_tax_incl,a.product_quantity_in_stock,a.product_name,n.reference, n.date_add ORDER BY n.date_add DESC';
        
        $products=Db::getInstance()->executeS($query);
        return $products;
    }
}
