<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once(_PS_CORE_DIR_.'/classes/order/Order.php');

$id_cart = 20060;
$cart = new Cart($id_cart);
$reference = Order::generateReference();
$products = $cart->getProducts();

foreach ($products as $p){
    $total_paid_real += $p['total_wt'];
    $total_products += $p['total'];
}

$order = new Order();
$order->id_address_delivery = $cart->id_address_delivery;
$order->id_address_invoice = $cart->id_address_invoice;
$order->id_shop_group = $cart->id_shop_group;
$order->id_shop = $cart->id_shop;
$order->id_cart = $cart->id;
$order->id_currency = $cart->id_currency;
$order->id_lang = $cart->id_lang;
$order->id_customer = $cart->id_customer;
$order->id_carrier = $cart->id_carrier;
$order->secure_key = $cart->secure_key;
$order->payment = 'bitpay';
$order->date_add = $cart->date_add;
$order->date_upd = $cart->date_upd;
$order->module = 'bitpay';
$order->total_paid = $total_products;
$order->total_paid_real = $total_paid_real;
$order->total_products = $total_products;
$order->total_products_wt = $total_paid_real;
$order->total_paid_tax_incl = $total_products;
$order->total_paid_tax_excl = $total_paid_real;
$order->current_state = 15;
$order->conversion_rate = 1;
$order->reference = $reference;
$order->add();

$order_detail = new OrderDetail();
$order_detail->createList($order, $cart, $order->getCurrentOrderState(), $cart->getProducts(), 0, true, 0);

