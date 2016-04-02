<?php
 require_once(dirname(__FILE__) . '/../../config/config.inc.php');

if(isset($_GET['token']) && !empty($_GET['token']) && preg_match('/^[a-f0-9]{32}$/', $_GET['token'])){

	$token = Tools::getValue('token');
	$query=  "	SELECT url 
				FROM "._DB_PREFIX_."url_confirm_payu
				WHERE token = '".$token."';";
        $row = Db::getInstance()->getRow($query);
    
        if(isset($row['url']) && !empty($row['url'])){
            $reference_pol = Tools::getValue('reference_pol');
            $sql=  "SELECT id_cart,id_customer
                    FROM `"._DB_PREFIX_."pagos_payu`
                    where `orderIdPayu` = '".(int) $reference_pol."';";
            $row2 = Db::getInstance()->getRow($sql);

            if(isset($row2['id_cart']) && !empty($row2['id_cart'])){
                  $sql=  "SELECT id_cart,id_customer
                    FROM `"._DB_PREFIX_."pagos_payu`
                    where `orderIdPayu` = '".(int) $reference_pol."';";
                $row3 = Db::getInstance()->getRow($sql);
                if(!isset($row3['id_cart']) && empty($row3['id_cart'])){
                Db::getInstance()->insert('payu_pse', array(
                    'id_cart' => (int)$row2['id_cart'],
                    'id_customer'      => (int)$row2['id_customer'],
                    'reference_pol' => (int)Tools::getValue('reference_pol'),
                    'transactionId' => pSQL(Tools::getValue('transactionId')),
                    'lapTransactionState' => pSQL(Tools::getValue('lapTransactionState')),
                    'lapResponseCode' => pSQL(Tools::getValue('lapResponseCode')),
                    'response_pse' => pSQL(print_r($_REQUEST,TRUE)),
                ));
                }
            }
            $pse = strtr(base64_encode( gzcompress(json_encode($_REQUEST), 9)), '+/=', '-_,');
            Tools::redirect($row['url'].'&PAYU_PSE='.$pse);    
    }
}
Tools::redirect('/');	