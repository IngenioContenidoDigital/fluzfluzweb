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
                $code="INSERT INTO "._DB_PREFIX_."product_code (id_product, code, id_order, used, date_add) VALUES ('".$request['id_product']."', '".$request['phone_mobile']."', '".$request['id_order']."', '2', '".date('Y-m-d H:i:s')."')";
                Db::getInstance()->execute($code);
            }
            $update ="UPDATE "._DB_PREFIX_."webservice_external_log SET response_code='".(int)$response['responsecode']."', response_message='".$response['responsemessage']."', date_upd='".date('Y-m-d H:i:s')."' WHERE id_webservice_external_log=".$request['id_webservice_external_log'];
        }                    
        Db::getInstance()->execute($update);
    }
}