<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');

$customers = Db::getInstance()->executeS("SELECT
                                            IF( o.date_add IS NULL , DATEDIFF(NOW(),MAX(c.date_add)) , DATEDIFF(NOW(),MAX(o.date_add)) ) days_inactive,
                                            c.id_customer,
                                            c.username,
                                            c.email
                                        FROM "._DB_PREFIX_."customer c
                                        LEFT JOIN "._DB_PREFIX_."orders o ON ( c.id_customer = o.id_customer )
                                        GROUP BY c.id_customer
                                        HAVING days_inactive IN ( 30 , 45 , 52 , 59 , 60 )");

echo '<pre>'; print_r($customers); die();

foreach ( $customers as $key => $customer ) {

    $message_alert = "Si tu cuenta Fluz Fluz permanece inactiva por un total de 60 dias,";
    switch ( $customer['days_inactive'] ) {
        case 30:
            $message_alert .= " se cancelara.";
            break;
        case 45:
            $message_alert .= " (15 dias mas) se cancelara.";
            break;
        case 52:
            $message_alert .= " (8 dias mas) se cancelara.";
            break;
        case 59:
            $message_alert .= " (1 dias mas) se cancelara.";
            break;
        case 60:
            $message_alert .= " se cancelara. Este es el ultimo dia para que renueves la actividad, antes de que tu cuenta se cancele.";
            break;
    }

    $vars = array(
        '{username}' => $customer['username'],
        '{days_inactive}' => $customer['days_inactive'],
        '{message}' => $message_alert,
        '{contributor_count}' => "",
        '{points_count}' => "",
        '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
        '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id)
    );

    Mail::Send(
        Context::getContext()->language->id,
        'TEMPLATE',
        'ASUNTO',
        $vars,
        $customer['email'],
        $customer['username']
    );
}

