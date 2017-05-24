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
    $vars = array(
        '{username}' => $customer['username'],
        '{invitation_count}' => (2 - $customer['invitation_count']),
        '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
        '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id)
    );

    Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."notification_history(id_customer, type_message, message, date_send)
                                VALUES (".$customer['id_customer'].",'Invita un nuevo Fluzzer', 'Te informamos que aun tienes ".$customer['invitation_count']." invitacion (es) pendiente(s).', NOW())");
    
    $file = _PS_ROOT_DIR_ . '/Flyers-O-s.pdf';
    $file_attachement[0]['content'] = file_get_contents($file);
    $file_attachement[0]['name'] = 'Informacion fluzfluz.pdf';
    $file_attachement[0]['mime'] = 'application/pdf';
    
    $file = _PS_ROOT_DIR_ . '/guiarapidaFluz-O.pdf';
    $file_attachement[1]['content'] = file_get_contents($file);
    $file_attachement[1]['name'] = 'Guia Rapida Fluz Fluz.pdf';
    $file_attachement[1]['mime'] = 'application/pdf';
    
    $template = 'rememberinvitenewusers';
    $prefix_template = '16-rememberinvitenewusers';

    $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
    $row_subject = Db::getInstance()->getRow($query_subject);
    $message_subject = $row_subject['subject_mail'];
    
    $allinone_rewards = new allinone_rewards();
    $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($subject), $vars, $customer['email'],$customer['username'],$file_attachement);
            
    /*Mail::Send(
        Context::getContext()->language->id,
        'rememberinvitenewusers',
        'Invita un nuevo Fluzzer',
        $vars,
        $customer['email'],
        $customer['username']
    );*/
}

