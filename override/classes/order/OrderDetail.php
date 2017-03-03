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
include_once(_PS_MODULE_DIR_.'allinone_rewards/models/RewardsTemplateModel.php');
class OrderDetail extends OrderDetailCore
{
   
    public $porcentaje;
    
    public $points;
    /** @var int */
    
    public static $definition = array(
        'table' => 'order_detail',
        'primary' => 'id_order_detail',
        'fields' => array(
            'id_order' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order_invoice' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_warehouse' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_shop' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'product_id' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'product_attribute_id' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'product_name' =>                array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'porcentaje' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'points' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'discount_quantity_applied' =>    array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'product_quantity' =>            array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'product_quantity_in_stock' =>    array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'product_quantity_return' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_quantity_refunded' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_quantity_reinjected' =>array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_price' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'reduction_percent' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'reduction_amount' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'reduction_amount_tax_incl' =>  array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'reduction_amount_tax_excl' =>  array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'group_reduction' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'product_quantity_discount' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'product_ean13' =>                array('type' => self::TYPE_STRING, 'validate' => 'isEan13'),
            'product_upc' =>                array('type' => self::TYPE_STRING, 'validate' => 'isUpc'),
            'product_reference' =>            array('type' => self::TYPE_STRING, 'validate' => 'isReference'),
            'product_supplier_reference' => array('type' => self::TYPE_STRING, 'validate' => 'isReference'),
            'product_weight' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'tax_name' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'tax_rate' =>                    array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'tax_computation_method' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_tax_rules_group' =>        array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'ecotax' =>                    array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'ecotax_tax_rate' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'discount_quantity_applied' =>    array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'download_hash' =>                array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'download_nb' =>                array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'download_deadline' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'unit_price_tax_incl' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'unit_price_tax_excl' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_price_tax_incl' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_price_tax_excl' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_shipping_price_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_shipping_price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'purchase_supplier_price' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'original_product_price' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'original_wholesale_price' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice')
        ),
    );
    
    protected function create(Order $order, Cart $cart, $product, $id_order_state, $id_order_invoice, $use_taxes = true, $id_warehouse = 0)
    {
        
        if ($use_taxes) {
            $this->tax_calculator = new TaxCalculator();
        }
        $this->id = null;
        
        $queryprueba = "SELECT p.id_product AS id, rp.value as value FROM "._DB_PREFIX_."product p
                            LEFT JOIN "._DB_PREFIX_."product_attribute pa ON (pa.reference = p.reference)
                            LEFT JOIN "._DB_PREFIX_."product_lang pl ON (p.id_product = pl.id_product)
                            LEFT JOIN "._DB_PREFIX_."rewards_product rp ON (rp.id_product = p.id_product)
                            WHERE p.reference = '".$product['reference']."' AND pl.`id_lang` = ".(int)$this->context->language->id;
        $x = Db::getInstance()->executeS($queryprueba);
        
        $this->product_id = $product['id_product'];
        $this->product_attribute_id = $product['id_product_attribute'] ? (int)$product['id_product_attribute'] : 0;
        $this->product_name = $product['name'].
            ((isset($product['attributes']) && $product['attributes'] != null) ?
                ' - '.$product['attributes'] : '');
        
        $porcentaje_detail = $x[0]['value']/100;
        $this->porcentaje = $porcentaje_detail;
        
        
        $sponsorships = RewardsSponsorshipModel::getSponsorshipAscendants((int)$order->id_customer);
        $sponsorships2=array_slice($sponsorships, 1, 15);
        $points_detail=  (round((RewardsModel::getRewardReadyForDisplay((int)$product['price'], $this->context->currency->id)/(count($sponsorships2)+1))*$porcentaje_detail)*(int)$product['cart_quantity']); 
        $this->points = $points_detail;
        
        $this->product_quantity = (int)$product['cart_quantity'];
        $this->product_ean13 = empty($product['ean13']) ? null : pSQL($product['ean13']);
        $this->product_upc = empty($product['upc']) ? null : pSQL($product['upc']);
        $this->product_reference = empty($product['reference']) ? null : pSQL($product['reference']);
        $this->product_supplier_reference = empty($product['supplier_reference']) ? null : pSQL($product['supplier_reference']);
        $this->product_weight = $product['id_product_attribute'] ? (float)$product['weight_attribute'] : (float)$product['weight'];
        $this->id_warehouse = $id_warehouse;
        $product_quantity = (int)Product::getQuantity($this->product_id, $this->product_attribute_id);
        $this->product_quantity_in_stock = ($product_quantity - (int)$product['cart_quantity'] < 0) ?
            $product_quantity : (int)$product['cart_quantity'];
        $this->setVirtualProductInformation($product);
        $this->checkProductStock($product, $id_order_state);
        if ($use_taxes) {
            $this->setProductTax($order, $product);
        }
        $this->setShippingCost($order, $product);
        $this->setDetailProductPrice($order, $cart, $product);
        // Set order invoice id
        $this->id_order_invoice = (int)$id_order_invoice;
        // Set shop id
        $this->id_shop = (int)$product['id_shop'];
        // Add new entry to the table
        $this->save();
        if ($use_taxes) {
            $this->saveTaxCalculator($order);
        }
        unset($this->tax_calculator);
    }
}

?>