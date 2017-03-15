<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');

$query = "SELECT pc.id_product_code, pc.code, pc.id_order, c.id_customer, c.secure_key
            FROM ps_product_code pc
            INNER JOIN ps_orders o ON ( pc.id_order = o.id_order )
            INNER JOIN ps_customer c ON ( o.id_customer = c.id_customer )
            WHERE pc.id_order <> 0
            ORDER BY pc.id_product_code";

$codes = Db::getInstance()->executeS($query);
// echo '<pre>';print_r($codes); die();

foreach ( $codes as $code ) {
    $codedecrypt = Encrypt::decrypt(Configuration::get('PS_FLUZ_CODPRO_KEY') , $code['code']);
    $codeencrypt = Encrypt::encrypt($code['secure_key'] , $codedecrypt);
    
    Db::getInstance()->execute("UPDATE "._DB_PREFIX_."product_code
                                SET code = '".$codeencrypt."'
                                WHERE id_product_code = ".$code['id_product_code']);
}
