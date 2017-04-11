<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once('./modules/allinone_rewards/allinone_rewards.php');

$query = "SELECT c.id_customer, c.username, c.email, COUNT(rs.id_sponsor) invitation_count
            FROM "._DB_PREFIX_."customer c
            LEFT JOIN "._DB_PREFIX_."rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
            WHERE c.active = 1
            AND c.kick_out = 0
            GROUP BY c.id_customer
            HAVING invitation_count < 2";

$customers = Db::getInstance()->executeS($query);

//echo '<pre>'; print_r($customers); die();

foreach ( $customers as $key => $customer ) {
    
    $template = 'rememberinvitenewusers';
    $subject = 'Invita un nuevo Fluzzer';
    $vars = array(
        '{username}' => $customer['username'],
        '{invitation_count}' => (2 - $customer['invitation_count']),
        '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
        '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id)
    );

    Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."notification_history(id_customer, type_message, message, date_send)
                                VALUES (".$customer['id_customer'].",'Invita un nuevo Fluzzer', 'Te informamos que aun tienes ".$customer['invitation_count']." invitacion (es) pendiente(s).', NOW())");

    $reward = new allinone_rewards();
    $reward->sendMail(Context::getContext()->language->id, $template, $subject, $vars, $customer['email'], $customer['username']);
            
    /*Mail::Send(
        Context::getContext()->language->id,
        'rememberinvitenewusers',
        'Invita un nuevo Fluzzer',
        $vars,
        $customer['email'],
        $customer['username']
    );*/
}

