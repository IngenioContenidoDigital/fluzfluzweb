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

class HistoryController extends HistoryControllerCore
{
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
        
        $this->context->smarty->assign(array(
            's3'=> _S3_PATH_,
            'orders' => $orders,
            'invoiceAllowed' => (int)Configuration::get('PS_INVOICE'),
            'reorderingAllowed' => !(bool)Configuration::get('PS_DISALLOW_HISTORY_REORDERING'),
            'products'=>$this->productHistory(),
            'slowValidation' => Tools::isSubmit('slowvalidation')
        ));

        $this->setTemplate(_PS_THEME_DIR_.'history.tpl');
    }
    
    public function productHistory(){
        
        $query = 'SELECT
                        a.id_order,
                        p.price_shop as price_shop,
                        p.type_currency,
                        p.save_dolar,
                        p.id_manufacturer,
                        a.product_id AS idProduct,
                        d.link_rewrite as link_rewrite,
                        b.id_image AS image,
                        a.total_price_tax_incl as total,
                        a.unit_price_tax_incl AS precio,
                        a.product_quantity AS cantidad,
                        a.product_name AS purchase,
                        m.name AS manufacturer,
                        n.reference AS referencia,
                        n.date_add AS time
                    FROM '._DB_PREFIX_.'orders n
                    LEFT JOIN '._DB_PREFIX_.'order_detail a ON (a.id_order = n.id_order)
                    LEFT JOIN '._DB_PREFIX_.'image b ON (b.id_product = a.product_id)
                    LEFT JOIN '._DB_PREFIX_.'product_lang d ON (d.id_product = a.product_id)
                    LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = a.product_id)
                    LEFT JOIN '._DB_PREFIX_.'manufacturer m ON ( p.id_manufacturer = m.id_manufacturer )    
                    WHERE id_customer = '.$this->context->customer->id.'
                    AND p.reference != "MFLUZ"
                    AND d.id_lang = '.$this->context->language->id.'
                    GROUP BY
                        p.price_shop,
                        a.product_id,
                        d.link_rewrite,
                        b.id_image,
                        a.total_price_tax_incl,
                        a.product_quantity_in_stock,
                        a.product_name,
                        n.reference,
                        n.date_add
                    ORDER BY n.date_add DESC';
        
        $products=Db::getInstance()->executeS($query);
        $result= array();
        foreach($products as $x){
            $precio = RewardsProductModel::getProductReward($x['idProduct'],$x['precio'],1, $this->context->currency->id);
            $x['points']=round(RewardsModel::getRewardReadyForDisplay($precio, $this->context->currency->id)/(RewardsSponsorshipModel::getNumberSponsorship($this->context->customer->id)));
            $x['pointsNl']=round(RewardsModel::getRewardReadyForDisplay($precio, $this->context->currency->id)/16);
            array_push($result,$x);
         }
        
        return $result;
    }
}

?>