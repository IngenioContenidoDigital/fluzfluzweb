<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once('./modules/allinone_rewards/models/RewardsSponsorshipModel.php');

$query = "SELECT
                IF( o.date_add IS NULL,
                    DATEDIFF(NOW(),MAX(c.date_add)),
                    DATEDIFF(NOW(),MAX(o.date_add))
                ) days_inactive,
                c.id_customer,
                c.username,
                c.email,
                ( SELECT COUNT(od.id_order_detail)
                    FROM "._DB_PREFIX_."orders o2
                    LEFT JOIN "._DB_PREFIX_."order_detail od ON ( o2.id_order = od.id_order )
                    WHERE o2.id_customer = c.id_customer
                    AND o2.date_add BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL -2 MONTH) 
		) products,
                ( SELECT SUM(r.credits)
                    FROM ps_rewards r
                    WHERE r.id_customer = c.id_customer
                    AND r.id_reward_state = 2
                ) points
            FROM "._DB_PREFIX_."customer c
            INNER JOIN "._DB_PREFIX_."rewards_sponsorship rs ON ( c.id_customer = rs.id_customer )
            LEFT JOIN "._DB_PREFIX_."orders o ON ( c.id_customer = o.id_customer )
            WHERE o.payment != 'Pedido gratuito'    
            GROUP BY c.id_customer
            -- HAVING days_inactive IN (0, 30 , 45 , 52 , 59 , 60 ) OR days_inactive >= 61
            ORDER BY days_inactive DESC";
$customers = Db::getInstance()->executeS($query);

// echo '<pre>'; print_r($customers); die();

$execute_kickout = false;
foreach ( $customers as $key => &$customer ) {

    Db::getInstance()->execute("UPDATE "._DB_PREFIX_."customer SET days_inactive = ".$customer['days_inactive']." WHERE id_customer = ".$customer['id_customer']);

    if ( $customer['days_inactive'] >= 61 && $customer['products'] < 4 ) {
        $customer['days_inactive'] = 90;
    }

    $subject = "";
    $template = 'remember_inactive_account';
    $message_alert = "Si tu cuenta Fluz Fluz permanece inactiva por un total de 60 dias,";
    switch ( $customer['days_inactive'] ) {
        case 0:
            $customer['days_inactive'] = "NULL";
            break;
        case 30:
            $subject = "Tus 2 compras minimas del mes!";
            $message_alert .= " se cancelara.";
            break;
        case 45:
            $subject = "Para gozar de los beneficios Fluz Fuz, recuerda realizar tus 2 compras minimas!";
            $message_alert .= " (15 dias mas) se cancelara.";
            break;
        case 52:
            $subject = "Olvidaste hacer tus 2 compras minimas en Fluz Fluz.";
            $message_alert .= " (8 dias mas) se cancelara.";
            break;
        case 59:
            $subject = "Alerta de Cancelacion de cuenta en Fluz Fluz.";
            $message_alert .= " (1 dias mas) se cancelara.";
            break;
        case 60:
            $subject = "Tu cuenta sera Cancelada.";
            $message_alert .= " se cancelara. Este es el ultimo dia para que renueves la actividad, antes de que tu cuenta se cancele.";
            break;
        case 90:
            $subject = "Tu cuenta fue Cancelada.";
            $template = 'cancellation_account';
            $message_alert = "";
            $execute_kickout = true;
            Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."message_sponsor (id_message_sponsor, id_customer_send, id_customer_receive, message, date_send) VALUES ('',".Configuration::get('CUSTOMER_MESSAGES_FLUZ').", ".$customer['id_customer'].", 'Tu cuenta ha estado inactiva por mas de 60 dias. Debido a esto, por desgracia, su cuenta ha sido cancelada.', NOW())");
            Db::getInstance()->execute("UPDATE "._DB_PREFIX_."customer SET kick_out = 1 WHERE id_customer = ".$customer['id_customer']);
            break;
    }
    
    if ( $subject != "" && $template != "cancellation_account"  ) {
        if ( $customer['days_inactive'] != "NULL" ) {
            $existNotificationInactive = Db::getInstance()->getValue("SELECT COUNT(*) FROM "._DB_PREFIX_."notification_inactive WHERE id_customer = ".$customer['id_customer']);
            if ( $existNotificationInactive == 0 ) {
                Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."notification_inactive (id_customer, date_alert_".$customer['days_inactive'].") VALUES (".$customer['id_customer'].", NOW())");
            } else {
                Db::getInstance()->execute("UPDATE "._DB_PREFIX_."notification_inactive SET date_alert_".$customer['days_inactive']." = NOW() WHERE id_customer = ".$customer['id_customer']);
            }
        }

        $contributor_count = Db::getInstance()->getValue("SELECT COUNT(*) contributor_count
                                                            FROM "._DB_PREFIX_."customer
                                                            WHERE active = 1");
        
        $points_count = Db::getInstance()->getValue("SELECT SUM(credits) points_count
                                                        FROM "._DB_PREFIX_."rewards
                                                        WHERE id_reward_state = 2");

        $vars = array(
            '{username}' => $customer['username'],
            '{days_inactive}' => $customer['days_inactive'],
            '{message}' => $message_alert,
            '{points}' => $customer['points'] == "" ? 0 : round($customer['points']),
            '{contributor_count}' => $contributor_count,
            '{points_count}' => round($points_count),
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
            '{learn_more_url}' => "http://reglas.fluzfluz.co"
        );

        if ( $customer['days_inactive'] != "NULL" ) { 
            Mail::Send(
                Context::getContext()->language->id,
                $template,
                $subject,
                $vars,
                $customer['email'],
                $customer['username']
            );
        }

        Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."notification_history (id_customer, type_message, message, date_send)
                                    VALUES (".$customer['id_customer'].",'Recordatorio cuenta inactiva', '".$message_alert."', NOW())");
    }
}

if ( $execute_kickout ) {
    require_once(_PS_ROOT_DIR_.'/kickoutcustomers.php');
}
