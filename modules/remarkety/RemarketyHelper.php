<?php

/**
 * 2015-2022 Interamind Ltd
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
 * Do not edit or add to this file if you wish to upgrade Remarkety module to newer
 * versions in the future. If you wish to customize Remarekty module for your
 * needs please contact Remarkety support
 *
 * @author    Interamind Ltd <support@remarkety.com>
 * @copyright 2015-2022 Interamind Ltd
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class RemarketyHelper
{

    public static function handleVoucherFromUrl($cart_controller)
    {
        if (CartRule::isFeatureActive()) {
            if (Tools::isSubmit('submitAddDiscount')) {
                $context = Context::getContext();
                if (!($code = trim(Tools::getValue('discount_name')))) {
                    $cart_controller->errors[] = Tools::displayError('You must enter a voucher code.');
                } elseif (!Validate::isCleanHtml($code)) {
                    $cart_controller->errors[] = Tools::displayError('The voucher code is invalid.');
                } else {
                    if (($cart_rule = new CartRule(CartRule::getIdByCode($code))) &&
                        Validate::isLoadedObject($cart_rule)
                    ) {
                        $context->cart->addcart_rule($cart_rule->id);

                        if (!$cart_rule->checkValidity($context, false, true)) {
                            $context->cart->addcart_rule($cart_rule->id);
                        }
                    } else {
                        $cart_controller->errors[] = Tools::displayError('This voucher does not exists.');
                    }
                }
                $context->smarty->assign(array(
                    'errors' => $cart_controller->errors,
                    'discount_name' => Tools::safeOutput($code)
                ));
            }

        }
    }

    /**
     * This function helps to enrich the Order's webservice API.
     * It adds the coupon code (cart rule) to be available to the webservice api as well
     * Extra code should be added to the order class in order for this to work
     * <root>/classes/order/Order.php
     * Add the following code to the class constractor right after calling to parent::__construct($id, $id_lang);
     *
     * if(file_exists(_PS_MODULE_DIR_ . 'remarkety/RemarketyHelper.php')){
     * include_once(_PS_MODULE_DIR_ . 'remarkety/RemarketyHelper.php');
     * $this->webserviceParameters['fields']['coupon'] = array();
     * RemarketyHelper::addCouponToWebService($this);
     * }

     */
    public static function addCouponToWebService(&$order)
    {
        $cartRuls = $order->getCartRules();
        if (is_array($cartRuls) && !empty($cartRuls)) {
            $couponObj = new CartRule($cartRuls[0]['id_cart_rule']);
            $order->coupon = $couponObj->code;
        }
    }

    public static function addExtraPricesToWebService(&$product)
    {
        if (isset($product->id)) {
            $sale_price_with_tax = Product::getPriceStatic(
                $product->id,
                true,
                null,
                2);

            $product->sale_price_with_tax = $sale_price_with_tax;

            $sale_price_without_tax = Product::getPriceStatic(
                $product->id,
                false,
                null,
                2);

            $product->sale_price_without_tax = $sale_price_without_tax;
        }
    }

    /**
     * @param CartRule $cart_rule
     * @param $xmlFromRemarkety
     *
     * Extra code needed to make this function work
     *
     * In  this file
     * /classes/webservice/WebserviceRequest.php
     *
     * After this line (1555)
     * $result = $object->{$objectMethod}();
     *
     * Add this extra code
     *
     * if(file_exists(_PS_MODULE_DIR_ . 'remarkety/RemarketyHelper.php')){
     * include_once(_PS_MODULE_DIR_ . 'remarkety/RemarketyHelper.php');
     * RemarketyHelper::copyCoupon($object, $xml);
     * }
     *
     */
    public static function copyCoupon(CartRule $cart_rule, $xmlFromRemarkety)
    {

        //PrestaShopLogger::addLog(print_r($xmlFromRemarkety, true), 3, 'RemarketyHelper');

        $carrier_restriction_ids = (string)$xmlFromRemarkety->cart_rule->carrier_restriction_ids;
        if (!empty($carrier_restriction_ids)) {
            $cart_rule->carrier_restriction = 1;
            $cart_rule->update();

            $carrier_restriction_ids = explode(',', $carrier_restriction_ids);
            foreach ($carrier_restriction_ids as $carrier_id) {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_carrier` (`id_cart_rule`, `id_carrier`) VALUES (' . $cart_rule->id . ', ' . (int)$carrier_id . ')');
            }
        }

        $shop_restriction_ids = (string)$xmlFromRemarkety->cart_rule->shop_restriction_ids;
        if (!empty($shop_restriction_ids)) {
            $cart_rule->shop_restriction = 1;
            $cart_rule->update();

            $shop_restriction_ids = explode(',', $shop_restriction_ids);
            foreach ($shop_restriction_ids as $shop_id) {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_shop` (`id_cart_rule`, `id_shop`) VALUES (' . $cart_rule->id . ', ' . (int)$shop_id . ')');
            }
        }

//        Db::getInstance()->execute('
//		INSERT INTO `'._DB_PREFIX_.'cart_rule_group` (`id_cart_rule`, `id_group`)
//		(SELECT '.(int)$id_cart_rule_destination.', id_group FROM `'._DB_PREFIX_.'cart_rule_group` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
//        Db::getInstance()->execute('
//		INSERT INTO `'._DB_PREFIX_.'cart_rule_country` (`id_cart_rule`, `id_country`)
//		(SELECT '.(int)$id_cart_rule_destination.', id_country FROM `'._DB_PREFIX_.'cart_rule_country` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
//        Db::getInstance()->execute('
//		INSERT INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`)
//		(SELECT '.(int)$id_cart_rule_destination.', IF(id_cart_rule_1 != '.(int)$id_cart_rule_source.', id_cart_rule_1, id_cart_rule_2) FROM `'._DB_PREFIX_.'cart_rule_combination`
//		WHERE `id_cart_rule_1` = '.(int)$id_cart_rule_source.' OR `id_cart_rule_2` = '.(int)$id_cart_rule_source.')');

    }

    public static function subscribeContact($storeId, $email, $firstName = '', $lastName = '', $doubleOptIn = true, $tags = '')
    {
        if (!empty($storeId) && !empty($email)) {
            $contactData = [
                "email" => $email,
                "firstName" => $firstName,
                "lastName" => $lastName,
                "doubleOptin" => $doubleOptIn,
                "tags" => $tags
            ];

            $payload = json_encode($contactData);
            $endpoint = "https://app.remarkety.com/api/v1/stores/$storeId/contacts";

            $headers = array(
                "Cache-Control: no-cache",
                "Content-Type: application/json",
                "Accept: application/json"
            );

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_URL, $endpoint);

            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            return $response;
        }

    }
}
