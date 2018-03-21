<?php

include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once(_PS_CLASS_DIR_.'Customer.php');

$id_customer= Tools::getValue('id_customer');

$sql = "SELECT phone
                FROM "._DB_PREFIX_."customer
                WHERE id_customer = ".$id_customer.";";
        $phone = Db::getInstance()->getValue($sql);
        
        $numberConfirm = rand(100000, 999999);
        $updateNumberConfirm = 'UPDATE '._DB_PREFIX_.'customer
                                SET web_confirm = '.$numberConfirm.'
                                WHERE id_customer = '.$id_customer.';';
        $result = Db::getInstance()->execute($updateNumberConfirm);
        
        $message_text= "Fluz Fluz te da la bienvenida!!! Tu codigo de verificacion es: ";
        $url = Configuration::get('APP_SMS_URL').$phone."&messagedata=".urlencode($message_text.$numberConfirm)."&longMessage=true";
        
        $args = array ('username'=>'Api_A91ON', 'password'=>'WBZ45NA8Z3');
        $opts = array(
          'http' => array(
            'method'=>'POST', 
            'header'=>'Content-Type: application/xml', 
            'content'=>http_build_query($args)
          )
        );

        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        
        $xml=simplexml_load_string($result);
        $items   = $xml->data;
        $response = json_decode(json_encode($items), TRUE)['acceptreport'];

        
        $result = ( $response['statuscode'] == 0 ) ? true : false;
        return $result;