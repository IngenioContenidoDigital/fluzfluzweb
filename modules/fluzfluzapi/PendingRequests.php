<?php

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('../../config/defines.inc.php');
require_once(_PS_MODULE_DIR_.'fluzfluzapi/models/CSoap.php');


executePending();


function executePending(){
    $query="SELECT * FROM "._DB_PREFIX_."webservice_external_log WHERE (response_code <> 0 AND response_code <> 9)";
    $requests = Db::getInstance()->executeS($query);
    foreach($requests as $request){
        $pclient = new CSoap($request['id_webservice_external']);
        $response = $pclient->doRequest($request['action'],$request['request']);
        
        $query_name = 'SELECT c.username as username, c.email as email FROM '._DB_PREFIX_.'orders o
                       LEFT JOIN '._DB_PREFIX_.'customer c ON (o.id_customer = c.id_customer)
                       WHERE o.id_order ='.$request['id_order'];
        $user = Db::getInstance()->executeS($query_name);
        
        if(!is_numeric($response)){
            $xml = simplexml_load_string($response);
            $xml->registerXPathNamespace('res', 'http://api.movilway.net/schema/extended');
            $response=array();
            foreach($xml->xpath('//res:*') as $item){
                if(((string)$item->getName())!=='responsemessage'){
                    $response[strtolower((string)$item->getName())]=(string)$item;
                }
            }
            if((int)$response['responsecode']==0){
                
                $chainsql="SELECT "._DB_PREFIX_."customer.id_customer, "._DB_PREFIX_."customer.secure_key 
                    FROM "._DB_PREFIX_."webservice_external_log INNER JOIN "._DB_PREFIX_."orders ON "._DB_PREFIX_."webservice_external_log.id_order = "._DB_PREFIX_."orders.id_order INNER JOIN "._DB_PREFIX_."customer ON "._DB_PREFIX_."orders.id_customer = "._DB_PREFIX_."customer.id_customer 
                        WHERE "._DB_PREFIX_."webservice_external_log.id_order =".$request['id_order'];
                $chainrow= Db::getInstance()->getRow($chainsql);
                
                set_time_limit(5000);
                $chain = Encrypt::encrypt($chainrow['secure_key'] , $request['mobile_phone']);
                
                $code = "INSERT INTO "._DB_PREFIX_."product_code (id_product, code, id_order, used, date_add, encry) VALUES ('".$request['id_product']."', '".$chain."', '".$request['id_order']."', '2', '".date('Y-m-d H:i:s')."', 1)";
                Db::getInstance()->execute($code);
                
                $template = 'order_conf_telco_sucess';
                $prefix_template = 'order_conf_telco_sucess';

                $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
                $row_subject = Db::getInstance()->getRow($query_subject);
                $message_subject = $row_subject['subject_mail'];

                $vars = array(
                        '{username}' => $user[0]['username'],
                        '{Recharged}' => $request['mobile_phone']
                    );

                Mail::Send(1, $template, $message_subject, $vars, $user[0]['email'], $user[0]['username'], Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'),NULL, NULL, dirname(__FILE__).'/mails/', false);

            }
            $update ="UPDATE "._DB_PREFIX_."webservice_external_log SET response_code='".(int)$response['responsecode']."', response_message='".$response['responsemessage']."', date_upd='".date('Y-m-d H:i:s')."' WHERE id_webservice_external_log=".$request['id_webservice_external_log'];
        }                    
        Db::getInstance()->execute($update);
    }
}
