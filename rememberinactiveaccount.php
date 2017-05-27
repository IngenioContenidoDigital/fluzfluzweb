<?php
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once('./modules/allinone_rewards/models/RewardsSponsorshipModel.php');
include_once('./modules/allinone_rewards/models/RewardsModel.php');
include_once('./modules/allinone_rewards/allinone_rewards.php');

$execute_kickout = false;

$query = "SELECT
                c.id_customer,
                c.username,
                c.email,
                c.date_kick_out,
                DATE_FORMAT(c.date_kick_out,'%Y-%m-%d') date_kick_out_show,
                c.warning_kick_out,
                IF( MAX(o.date_add) IS NULL,
                    DATEDIFF(NOW(),c.date_add),
                    DATEDIFF(NOW(),MAX(o.date_add))
                ) days_inactive,
                ( SELECT SUM(r.credits)
                    FROM "._DB_PREFIX_."rewards r
                    WHERE r.id_customer = c.id_customer
                    AND r.id_reward_state = 2
                ) points
            FROM "._DB_PREFIX_."customer c
            LEFT JOIN "._DB_PREFIX_."orders o ON ( c.id_customer = o.id_customer )
            WHERE c.active = 1
            AND c.kick_out = 0
            GROUP BY c.id_customer";
$customers = Db::getInstance()->executeS($query);

foreach ( $customers as $key => &$customer ) {
    set_time_limit(60);
    
    Db::getInstance()->execute("UPDATE "._DB_PREFIX_."customer SET days_inactive = ".$customer['days_inactive']." WHERE id_customer = ".$customer['id_customer']);
    
    $query = "SELECT IFNULL(SUM(od.product_quantity),0) purchases
                FROM "._DB_PREFIX_."orders o
                INNER JOIN "._DB_PREFIX_."order_detail od ON ( o.id_order = od.id_order AND od.product_reference NOT LIKE 'MFLUZ%' )
                WHERE o.current_state = 2
                AND ( o.date_add BETWEEN DATE_ADD('".$customer['date_kick_out_show']." 00:00:00', INTERVAL ".($customer['warning_kick_out'] == 0 ? '-30' : '-60')." DAY)  AND '".$customer['date_kick_out_show']." 23:59:59' )
                AND id_customer = ".$customer['id_customer'];
    $purchases = Db::getInstance()->getValue($query);
    
    $query = "SELECT DATE_FORMAT(DATE_ADD(date_kick_out, INTERVAL ".($customer['warning_kick_out'] == 0 ? '30' : '0')." DAY),'%Y-%m-%d') date
                FROM "._DB_PREFIX_."customer
                WHERE id_customer = ".$customer['id_customer'];
    $expiration_date = Db::getInstance()->getValue($query);
    
    if ( $customer['warning_kick_out'] == 1 && $purchases < 4 && strtotime(date('Y-m-d')) == strtotime($expiration_date) ) {
        $customer['days_inactive'] = 90;
    }
    
    if ( $customer['warning_kick_out'] == 1 && $purchases >= 4 && strtotime(date('Y-m-d')) == strtotime($expiration_date) ) {
        Db::getInstance()->execute("UPDATE "._DB_PREFIX_."customer SET date_kick_out = DATE_ADD(date_kick_out, INTERVAL 30 DAY), warning_kick_out = 0 WHERE id_customer = ".$customer['id_customer']);
    }
    
    if ( $customer['warning_kick_out'] == 0 && strtotime(date('Y-m-d')) == strtotime($customer['date_kick_out_show']) ) {
        Db::getInstance()->execute("UPDATE "._DB_PREFIX_."customer SET date_kick_out = DATE_ADD(date_kick_out, INTERVAL 30 DAY), warning_kick_out = ".($purchases < 2 ? '1' : '0')." WHERE id_customer = ".$customer['id_customer']);
    }
    
    $subject = $message_1 = $message_2 = $message_3 = "";
    $template = 'remember_inactive_account';
    switch ( $customer['days_inactive'] ) {
        case 0:
            $customer['days_inactive'] = "NULL";
            break;
        case 30:
            $subject = "Tus 2 compras minimas del mes!";
            $message_1 = "Fluz Fluz desea recordarte que no has realizado tus 2 compras m&iacute;nimas mensuales para permanecer activo.";
            $message_3 = "Para m&aacute;s informaci&oacute;n puedes ingresar a";
            break;
        case 45:
            $subject = "Para gozar de los beneficios Fluz Fuz, recuerda realizar tus 2 compras minimas!";
            $message_1 = "Fluz Fluz desea recordarte que no has realizado tus 2 compras m&iacute;nimas mensuales para permanecer activo.";
            $message_3 = "Para m&aacute;s informaci&oacute;n puedes ingresar a";
            break;
        case 52:
            $subject = "Olvidaste hacer tus 2 compras minimas en Fluz Fluz.";
            $message_1 = "Fluz Fluz desea recordarte que debido a que no realizaste tus 2 compras m&iacute;nimas mensuales el mes pasado, es necesario que te pongas al d&iacute;a; es decir, debes realizar las 2 compras del mes pasado y las 2 compras de este mes para permanecer activo. En caso contrario, el sistema desactivara t&uacute; cuenta de Fluz Fluz y tu espacio lo ocupar&aacute; un nuevo Fluzzer.";
            $message_2 = "Pd: Si ya relizaste tus compras y estas al d&iacute;a, haz caso omiso de esta notificaci&oacute;n.";
            $message_3 = "M&aacute;s informaci&oacute;n en";
            break;
        case 59:
            $subject = "Alerta de Cancelacion de cuenta en Fluz Fluz.";
            $message_1 = "Fluz Fluz desea recordarte que debido a que no realizaste tus 2 compras m&iacute;nimas mensuales el mes pasado, es necesario que te pongas al d&iacute;a; es decir, debes realizar las 2 compras del mes pasado y las 2 compras de este mes para permanecer activo. En caso contrario, el sistema desactivara t&uacute; cuenta de Fluz Fluz y tu espacio lo ocupar&aacute; un nuevo Fluzzer.";
            $message_2 = "Pd: Si ya relizaste tus compras y estas al d&iacute;a, haz caso omiso de esta notificaci&oacute;n.";
            $message_3 = "M&aacute;s informaci&oacute;n en";
            break;
        case 60:
            $subject = "Tu cuenta sera Cancelada.";
            $message_1 = "Fluz Fluz desea recordarte que debido a que no realizaste tus 2 compras m&iacute;nimas mensuales el mes pasado, es necesario que te pongas al d&iacute;a; es decir, debes realizar las 2 compras del mes pasado y las 2 compras de este mes para permanecer activo. En caso contrario, el sistema desactivara t&uacute; cuenta de Fluz Fluz y tu espacio lo ocupar&aacute; un nuevo Fluzzer.";
            $message_2 = "Pd: Si ya relizaste tus compras y estas al d&iacute;a, haz caso omiso de esta notificaci&oacute;n.";
            $message_3 = "M&aacute;s informaci&oacute;n en";
            break;
        case 90:
            $subject = "Tu cuenta fue Cancelada.";
            $template = 'cancellation_account';
            $message_1 = "";
            $execute_kickout = true;
            Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."message_sponsor (id_message_sponsor, id_customer_send, id_customer_receive, message, date_send) VALUES ('',".Configuration::get('CUSTOMER_MESSAGES_FLUZ').", ".$customer['id_customer'].", 'Tu cuenta ha estado inactiva por mas de 60 dias. Debido a esto, por desgracia, su cuenta ha sido cancelada.', NOW())");
            Db::getInstance()->execute("UPDATE "._DB_PREFIX_."customer SET kick_out = 1 WHERE id_customer = ".$customer['id_customer']);
            break;
    } 
    
    if ( $subject != "" && $template != "cancellation_account" ) {
        if ( $customer['days_inactive'] != "NULL" ) {            
            $existNotificationInactive = Db::getInstance()->getValue("SELECT COUNT(*) FROM "._DB_PREFIX_."notification_inactive WHERE id_customer = ".$customer['id_customer']);
            if ( $existNotificationInactive == 0 ) {
                Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."notification_inactive (id_customer, date_alert_".$customer['days_inactive'].") VALUES (".$customer['id_customer'].", NOW())");
            } else {
                Db::getInstance()->execute("UPDATE "._DB_PREFIX_."notification_inactive SET date_alert_".$customer['days_inactive']." = NOW() WHERE id_customer = ".$customer['id_customer']);
            }
        }

        Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."notification_history (id_customer, type_message, message, date_send) VALUES (".$customer['id_customer'].",'Recordatorio cuenta inactiva', '".$message_1."', NOW())");

        $contributor_count = Db::getInstance()->getValue("SELECT COUNT(*) contributor_count FROM "._DB_PREFIX_."customer WHERE active = 1");
        $points_count = Db::getInstance()->getValue("SELECT SUM(credits) points_count FROM "._DB_PREFIX_."rewards WHERE id_reward_state = 2");

        $vars = array(
            '{username}' => $customer['username'],
            '{days_inactive}' => $customer['days_inactive'],
            '{message}' => $message_1,
            '{message2}' => $message_2,
            '{message3}' => $message_3,
            '{expiration_date}' => $expiration_date,
            '{points}' => $customer['points'] == "" ? 0 : round($customer['points']),
            '{contributor_count}' => $contributor_count,
            '{points_count}' => round($points_count),
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
            '{learn_more_url}' => "http://reglas.fluzfluz.co"
        );

        if ( $customer['days_inactive'] != "NULL" ) {
            
            $file = _PS_ROOT_DIR_ . '/Flyers-O-s.pdf';
            $file_attachement[0]['content'] = file_get_contents($file);
            $file_attachement[0]['name'] = 'Informacion fluzfluz.pdf';
            $file_attachement[0]['mime'] = 'application/pdf';

            $file = _PS_ROOT_DIR_ . '/guiarapidaFluz-O.pdf';
            $file_attachement[1]['content'] = file_get_contents($file);
            $file_attachement[1]['name'] = 'Guia Rapida Fluz Fluz.pdf';
            $file_attachement[1]['mime'] = 'application/pdf';
            
            $allinone_rewards = new allinone_rewards();
            $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($subject), $vars, $customer['email'],$customer['username'], $file_attachement);
                
            /*Mail::Send(
                Context::getContext()->language->id,
                $template,
                $subject,
                $vars,
                $customer['email'],
                $customer['username']
            );*/
        }
    }
}

if ( $execute_kickout ) {
    require_once(_PS_ROOT_DIR_.'/kickoutcustomers.php');
}
