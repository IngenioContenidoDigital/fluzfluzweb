<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once('./modules/allinone_rewards/allinone_rewards.php');

$remembers = array();

// customers without any purchase
$query = "SELECT
                c.id_customer,
                c.username,
                CONCAT(c.firstname,' ',c.lastname) name,
                c.email,
                o.date_add,
                (SELECT SUM(r.credits)
                FROM "._DB_PREFIX_."rewards r
                WHERE r.id_customer = c.id_customer
                AND r.id_reward_state = 2) points,
                DATE_FORMAT(ADDDATE(c.date_add, INTERVAL 30 DAY) ,'%d/%m/%Y') end_date,
                DATEDIFF(NOW(),o.date_add) days
        FROM "._DB_PREFIX_."customer c
        LEFT JOIN "._DB_PREFIX_."orders o ON ( c.id_customer = o.id_customer )
        WHERE c.active = 1
        AND c.kick_out = 0
        AND o.date_add IS NULL
        GROUP BY c.id_customer";
$remembers1 = Db::getInstance()->executeS($query);

// customers without shopping for more than a month
$query = "SELECT
                c.id_customer,
                c.username,
                CONCAT(c.firstname,' ',c.lastname) name,
                c.email,
                (SELECT SUM(r.credits)
                FROM "._DB_PREFIX_."rewards r
                WHERE r.id_customer = c.id_customer
                AND r.id_reward_state = 2) points,
                (SELECT date_add
                FROM "._DB_PREFIX_."orders
                WHERE id_customer = c.id_customer
                ORDER BY date_add DESC
                LIMIT 1) AS date_add,
                (SELECT DATE_FORMAT(ADDDATE(oo.date_add, INTERVAL 2 MONTH) ,'%d/%m/%Y')
                FROM "._DB_PREFIX_."orders oo
                WHERE oo.id_customer = c.id_customer
                ORDER BY oo.date_add DESC
                LIMIT 1) end_date,
                (SELECT DATEDIFF(NOW(),oo.date_add) days
                FROM "._DB_PREFIX_."orders oo
                WHERE oo.id_customer = c.id_customer
                ORDER BY oo.date_add DESC
                LIMIT 1) days
        FROM "._DB_PREFIX_."customer c
        LEFT JOIN "._DB_PREFIX_."orders o ON ( c.id_customer = o.id_customer )
        LEFT JOIN "._DB_PREFIX_."order_detail od ON ( o.id_order = od.id_order AND od.product_reference <> 'MFLUZ' )
        WHERE o.date_add IS NOT NULL
        AND DATEDIFF(NOW(), (SELECT date_add
                            FROM "._DB_PREFIX_."orders
                            WHERE id_customer = c.id_customer
                            ORDER BY date_add DESC
                            LIMIT 1)
                    ) > 30
        AND c.active = 1
        AND c.kick_out = 0
        GROUP BY c.id_customer
        HAVING COUNT(od.id_order_detail) <= 1
        ORDER BY o.date_add DESC";
$remembers2 = Db::getInstance()->executeS($query);

$remembers = array_merge($remembers1, $remembers2);
//  echo '<pre>'; print_r($remembers);
//  die();

foreach ( $remembers as $key => $remember ) {
    $vars = array(
        '{username}' => $remember['username'],
        '{points}' => (($remember['points'] != "") ? $remember['points'] : 0),
        '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
        '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
        '{expiration_date}' => $remember['end_date']
    );
    
    $queryMessage = "INSERT INTO "._DB_PREFIX_."message_sponsor (id_message_sponsor, id_customer_send, id_customer_receive, message, date_send)
                    VALUES('',".Configuration::get('CUSTOMER_MESSAGES_FLUZ').", ".$remember['id_customer'].", 'No has realizado tus 2 compras minimas mensuales para permanecer activo.', NOW())";
    Db::getInstance()->execute($queryMessage);

    Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."notification_history(id_customer, type_message, message, date_send)
                                VALUES (".$remember['id_customer'].",'Recordatorio compra minima', 'No has realizado tus 2 compras minimas mensuales para permanecer activo.', NOW())");

    Mail::Send(
        Context::getContext()->language->id,
        'remember_buy',
        'Recordatorio compra minima',
        $vars,
        $remember['email'],
        $remember['name']
    );
}

