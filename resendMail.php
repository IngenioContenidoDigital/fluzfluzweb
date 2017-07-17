<?php

include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/allinone_rewards.php');

error_reporting(E_ALL);

    $id_mail = $_POST["id_mail"];
    
    $query_mail = 'SELECT * FROM '._DB_PREFIX_.'mail m
                   LEFT JOIN '._DB_PREFIX_.'customer c ON (m.recipient = c.email)
                   WHERE id_mail = '.$id_mail;
    $list_mail = Db::getInstance()->executeS($query_mail);
    
    $vars = json_decode($list_mail[0]['vars'], true);
    $template = substr($list_mail[0]['template'], 3);
    $vars['resend'] = 1;
   
    $allinone_rewards = new allinone_rewards();
    $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($list_mail[0]['subject']), $vars, $list_mail[0]['recipient'],$list_mail[0]['username']);    