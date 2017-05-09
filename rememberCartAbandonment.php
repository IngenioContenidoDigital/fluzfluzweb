<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once ('.override/controllers/admin/AdminCartsController.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/allinone_rewards.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsSponsorshipModel.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsModel.php');

$query_Abandonment = 'SELECT
                        c.id_customer,
                        c.email,
                        c.username,
                        cu.id_currency,
                        a.id_cart,
                        IF (IFNULL(o.id_order, \''.'No ordenado'.'\') = \''.'No ordenado'.'\', IF(TIME_TO_SEC(TIMEDIFF(\''.pSQL(date('Y-m-d H:i:00', time())).'\', a.`date_add`)) > 3600, \''.'Carrito abandonado'.'\', \''.'No ordenado'.'\'), o.id_order) AS status
                    FROM '._DB_PREFIX_.'cart as a
                    LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = a.id_customer)
                    LEFT JOIN '._DB_PREFIX_.'orders o ON (o.id_cart = a.id_cart)
                    LEFT JOIN '._DB_PREFIX_.'currency cu ON (cu.id_currency = a.id_currency)                        
                    WHERE a.remember_cart_abandonment = 0
                    AND c.id_customer IS NOT NULL
                    HAVING status = "Carrito Abandonado"';
        
$cart_abandonment = DB::getInstance()->executeS($query_Abandonment);

foreach ($cart_abandonment as $remember){
            $name = '';
            $quantity_p = '';
            $price_unit = '';
            $price_total = '';
            $price_point = '';
            
            $query_product = 'SELECT cp.id_product,cp.quantity, pl.name, p.price 
                              FROM '._DB_PREFIX_.'cart_product as cp
                              LEFT JOIN '._DB_PREFIX_.'product_lang as pl ON (cp.id_product = pl.id_product) 
                              LEFT JOIN '._DB_PREFIX_.'product as p ON (cp.id_product = p.id_product) 
                              WHERE cp.id_cart ='.$remember['id_cart'].' AND pl.id_lang = 1';    
    
            $list_product = Db::getInstance()->executeS($query_product);
            
            $sponsorships = RewardsSponsorshipModel::getSponsorshipAscendants((int)$remember['id_customer']);
            $sponsorships2=array_slice($sponsorships, 1, 15);
            
            foreach ($list_product as &$product_cart){
                $name .=  "<label>".$product_cart['name']."</label><br>";
                $quantity_p .=  "<label>".$product_cart['quantity']."</label><br>";
                $price_unit .=  "<label>".$product_cart['price']."</label><br>";
                $sum = $product_cart['price'];
                $price_total +=  $sum;

                $query_value = 'SELECT (rp.`value`/100) as value FROM '._DB_PREFIX_.'rewards_product rp WHERE id_product = '.$product_cart['id_product'];
                $row_v = Db::getInstance()->getRow($query_value);
                $value_porc = $row_v['value'];

                $reward = round(RewardsModel::getRewardReadyForDisplay($price_total, $remember['id_currency'])/(count($sponsorships2)+1));
                $r_point = floor($reward*$value_porc);
                $price_point .=  "<label>".$r_point."</label><br>";
                 
            }
            
            $mailVars = array(
                '{order_link}' => Context::getContext()->link->getPageLink('order', false, (int)$cart_normal->id_lang, 'step=3&recover_cart='.(int)$cart_normal->id.'&token_cart='.md5(_COOKIE_KEY_.'recover_cart_'.(int)$cart_normal->id)),
                '{username}' => $remember['username'],
                '{quantity}' => $quantity_p,
                '{name_product}' => $name,
                '{points}' => $price_point,
                '{price_unit}' => $price_unit,
                '{price_total}' => $price_total,
                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
            );
            
            $template = 'remember_cart';
            $prefix_template = '16-remember_cart';

            $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'subject_mail WHERE name_template_mail ="'.$prefix_template.'"';
            $row_subject = Db::getInstance()->getRow($query_subject);
            $message_subject = $row_subject['subject_mail'];
            
            $allinone_rewards = new allinone_rewards();
            $allinone_rewards->sendMail(1, $template, $allinone_rewards->getL($message_subject), $mailVars, $remember['email'], $remember['username']);

            $update_mail_send = 'UPDATE '._DB_PREFIX_.'cart SET remember_cart_abandonment = 1 WHERE id_cart = '.$remember['id_cart'].' AND remember_cart_abandonment = 0';
            Db::getInstance()->execute($update_mail_send);
}

?>