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
include('../../../modules/allinone_rewards/models/RewardsModel.php');
include('../../../modules/allinone_rewards/models/RewardsProductModel.php');
include('../../../modules/allinone_rewards/models/RewardsStateModel.php');
include('../../../modules/allinone_rewards/models/RewardsSponsorshipModel.php');

class OrderController extends OrderControllerCore
{
  public function initContent()
    {
        FrontController::initContent();
        $totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
        $totalAvailable = round(isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0);
        $totalAvailableCurrency=RewardsModel::getmoneyReadyForDisplay($totalAvailableCurrency,(int)$this->context->currency->id);
        $this->context->smarty->assign('totalAvailable', $totalAvailable);
        $this->context->smarty->assign('totalAvailableCurrency', $totalAvailableCurrency);
        
        $sponsorships = RewardsSponsorshipModel::getSponsorshipAscendants($this->context->customer->id);
        $sponsorships2=array_slice($sponsorships, 1, 15);
        
        if(!$this->context->cart){
            $carro = $this->context->cart;
            $cart_products = $carro->getProducts();
            foreach ($cart_products as $cart_product) {
                //if ($cart_product['id_product_attribute'] != 0) {        
                  $sql="SELECT "._DB_PREFIX_."product.id_product, 
            "._DB_PREFIX_."product_attribute.id_product_attribute, "._DB_PREFIX_."product_attribute.id_product AS id_p_attribute FROM "._DB_PREFIX_."product_attribute INNER JOIN "._DB_PREFIX_."product ON "._DB_PREFIX_."product_attribute.reference = "._DB_PREFIX_."product.reference WHERE id_product_attribute=".$cart_product['id_product_attribute'];
                  $product = Db::getInstance()->getRow($sql);
                  $carro->deleteProduct($product['id_p_attribute'], $product['id_product_attribute']);
                  $carro->updateQty(0,$product['id_p_attribute'], $product['id_product_attribute']);
                  $carro->updateQty(1,$product['id_product']);
                //}
            }
        }else{
            //$carro = new Cart($this->context->cart->id);
            //$carro = $this->context->cart;
            
            $cart_products = $this->context->cart->getProducts();
            foreach ($cart_products as $cart_product) {
                if ($cart_product['id_product_attribute'] > 0) {        
                  $sql="SELECT "._DB_PREFIX_."product.id_product, 
            "._DB_PREFIX_."product_attribute.id_product_attribute, "._DB_PREFIX_."product_attribute.id_product AS id_p_attribute FROM "._DB_PREFIX_."product_attribute INNER JOIN "._DB_PREFIX_."product ON "._DB_PREFIX_."product_attribute.reference = "._DB_PREFIX_."product.reference WHERE id_product_attribute=".$cart_product['id_product_attribute'];
                  $product = Db::getInstance()->getRow($sql);
                  $this->context->cart->deleteProduct($product['id_p_attribute'], $product['id_product_attribute']);
                  //$this->context->cart->updateQty(0,$product['id_p_attribute'], $product['id_product_attribute']);
                  $this->context->cart->updateQty(1,$product['id_product']);
                }
            }
        }
        
        foreach ($this->context->cart->getProducts() as $product) {
            
            $queryprueba = "SELECT p.id_product as id, p.type_currency , i.id_image as image_parent, p.save_dolar, p.price_shop, p.reference FROM "._DB_PREFIX_."product p
                            LEFT JOIN "._DB_PREFIX_."product_attribute pa ON (pa.reference = p.reference)
                            LEFT JOIN "._DB_PREFIX_."image as i ON (i.id_product = pa.id_product)
                            LEFT JOIN "._DB_PREFIX_."product_lang pl ON (p.id_product = pl.id_product)
                            WHERE p.reference = '".$product['reference']."' AND pl.`id_lang` = ".(int)$this->context->language->id;
            $x = Db::getInstance()->executeS($queryprueba);
            $price = RewardsProductModel::getProductReward($x[0]['id'],$product['price'],1, $this->context->currency->id);
            $fluz = substr($product['reference'], 0,5);
            if($fluz != 'MFLUZ'){
                $productP= floor(RewardsModel::getRewardReadyForDisplay($price, $this->context->currency->id)/(2));
            }
            else{
                $productP= floor(RewardsModel::getRewardReadyForDisplay($price, $this->context->currency->id)/(1));
            }
            $productsPoints[$x[0]['reference']] = $productP;
            $shop[$x[0]['reference']] = $x[0]['price_shop'];
            $productsID[$x[0]['reference']] = $x[0]['id'];
            $type_currency[$x[0]['reference']] = $x[0]['type_currency'];
            $save_dolar[$x[0]['reference']] = $x[0]['save_dolar'];
            $img_parent[$x[0]['reference']] = $x[0]['image_parent'];
        }
        
        $this->context->smarty->assign(array(
            's3'=> _S3_PATH_,
            'productsPoints' => $productsPoints,
            'productsID' => $productsID,
            'img_parent' => $img_parent,
            'shop' => $shop,
            'type_currency' => $type_currency,
            'save_dolar' => $save_dolar        ));
        
        if (Tools::isSubmit('ajax') && Tools::getValue('method') == 'updateExtraCarrier') {
            // Change virtualy the currents delivery options
            $delivery_option = $this->context->cart->getDeliveryOption();
            $delivery_option[(int)Tools::getValue('id_address')] = Tools::getValue('id_delivery_option');
            $this->context->cart->setDeliveryOption($delivery_option);
            $this->context->cart->save();
            $return = array(
                'content' => Hook::exec(
                    'displayCarrierList',
                    array(
                        'address' => new Address((int)Tools::getValue('id_address'))
                    )
                )
            );
            $this->ajaxDie(Tools::jsonEncode($return));
        }
        
        if ($this->nbProducts) {
            $this->context->smarty->assign('virtual_cart', $this->context->cart->isVirtualCart());
        }
        if (!Tools::getValue('multi-shipping')) {
            $this->context->cart->setNoMultishipping();
        }

        $card = Customer::getCard($this->context->customer->id);
        $this->context->smarty->assign('cardCustomer', $card);

        // Check for alternative payment api
        $is_advanced_payment_api = (bool)Configuration::get('PS_ADVANCED_PAYMENT_API');
        // 4 steps to the order
        switch ((int)$this->step) {
            case OrderController::STEP_SUMMARY_EMPTY_CART:
                $this->context->smarty->assign('empty', 1);
                $this->setTemplate(_PS_THEME_DIR_.'shopping-cart.tpl');
            break;
             case OrderController::STEP_ADDRESSES:
                if (!$this->context->cart->isVirtualCart()){
                    $this->_assignAddress();
                    $this->processAddressFormat();
                    if (Tools::getValue('multi-shipping') == 1) {
                        $this->_assignSummaryInformations();
                        $this->context->smarty->assign('product_list', $this->context->cart->getProducts());
                        $this->setTemplate(_PS_THEME_DIR_.'order-address-multishipping.tpl');
                    } else {
                    $this->setTemplate(_PS_THEME_DIR_.'order-address.tpl');
                    }
                }
                else{
                    Tools::redirect('index.php?controller=order&step=3');
                }
                
            break;
            case OrderController::STEP_DELIVERY:
                if (Tools::isSubmit('processAddress')) {
                    $this->processAddress();
                }
                $this->autoStep();
                $this->_assignCarrier();
                $this->setTemplate(_PS_THEME_DIR_.'order-carrier.tpl');
            break;
            case OrderController::STEP_PAYMENT:
                // Check that the conditions (so active) were accepted by the customer
                $cgv = Tools::getValue('cgv') || $this->context->cookie->check_cgv;
                if ($is_advanced_payment_api === false && Configuration::get('PS_CONDITIONS')
                    && (!Validate::isBool($cgv) || $cgv == false)) {
                    Tools::redirect('index.php?controller=order&step=2');
                }
                if ($is_advanced_payment_api === false) {
                    Context::getContext()->cookie->check_cgv = true;
                }
                // Check the delivery option is set
                if ($this->context->cart->isVirtualCart() === false) {
                    if (!Tools::getValue('delivery_option') && !Tools::getValue('id_carrier') && !$this->context->cart->delivery_option && !$this->context->cart->id_carrier) {
                        Tools::redirect('index.php?controller=order&step=2');
                    } elseif (!Tools::getValue('id_carrier') && !$this->context->cart->id_carrier) {
                        $deliveries_options = Tools::getValue('delivery_option');
                        if (!$deliveries_options) {
                            $deliveries_options = $this->context->cart->delivery_option;
                        }
                        foreach ($deliveries_options as $delivery_option) {
                            if (empty($delivery_option)) {
                                Tools::redirect('index.php?controller=order&step=2');
                            }
                        }
                    }
                }
                
               $this->autoStep();
               
                // Bypass payment step if total is 0
                if (($id_order = $this->_checkFreeOrder()) && $id_order) {
                    if ($this->context->customer->is_guest) {
                        $order = new Order((int)$id_order);
                        $email = $this->context->customer->email;
                        $this->context->customer->mylogout(); // If guest we clear the cookie for security reason
                        Tools::redirect('index.php?controller=guest-tracking&id_order='.urlencode($order->reference).'&email='.urlencode($email));
                    } else {
                        
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'rewards SET id_reward_state=2, id_order='.$id_order.' WHERE id_customer = '.$this->context->customer->id.' AND id_cart='.$this->context->cart->id);
                        $qstate="UPDATE "._DB_PREFIX_."rewards SET id_reward_state= 2, id_cart = ".$this->context->cart->id." WHERE id_customer=".$this->context->customer->id." AND id_order=".$id_order; 
                        Db::getInstance()->execute($qstate);
                        Tools::redirect($this->context->link->getPageLink('my-account', true));
                    }
                }
                
                $this->_assignPayment();
                if ($is_advanced_payment_api === true) {
                    $this->_assignAddress();
                }
                // assign some informations to display cart
                $this->_assignSummaryInformations();
                $this->setTemplate(_PS_THEME_DIR_.'order-payment.tpl');
            break;
            default:
                $this->_assignSummaryInformations();
                $this->setTemplate(_PS_THEME_DIR_.'shopping-cart.tpl');
            break;
        }
    }
    
    public function autoStep()
    {
       
        /*if ($this->step >= 2 && (!$this->context->cart->id_address_delivery || !$this->context->cart->id_address_invoice)) {
            Tools::redirect('index.php?controller=order&step=1');
        }*/
        if ($this->step > 2 && !$this->context->cart->isVirtualCart()) {
            $redirect = false;
            if (count($this->context->cart->getDeliveryOptionList()) == 0) {
                $redirect = true;
            }
            $delivery_option = $this->context->cart->getDeliveryOption();
            if (is_array($delivery_option)) {
                $carrier = explode(',', $delivery_option[(int)$this->context->cart->id_address_delivery]);
            }
            if (!$redirect && !$this->context->cart->isMultiAddressDelivery()) {
                foreach ($this->context->cart->getProducts() as $product) {
                    $carrier_list = Carrier::getAvailableCarrierList(new Product($product['id_product']), null, $this->context->cart->id_address_delivery);
                    foreach ($carrier as $id_carrier) {
                        if (!in_array($id_carrier, $carrier_list)) {
                            $redirect = true;
                        } else {
                            $redirect = false;
                            break;
                        }
                    }
                    if ($redirect) {
                        break;
                    }
                }
            }
            if ($redirect) {
                Tools::redirect('index.php?controller=order&step=2');
            }
        }
        $delivery = new Address((int)$this->context->cart->id_address_delivery);
        $invoice = new Address((int)$this->context->cart->id_address_invoice);
        if ($delivery->deleted || $invoice->deleted) {
            if ($delivery->deleted) {
                unset($this->context->cart->id_address_delivery);
            }
            if ($invoice->deleted) {
                unset($this->context->cart->id_address_invoice);
            }
            Tools::redirect('index.php?controller=order&step=1');
        }
    }
    
    
}

?>