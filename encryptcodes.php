<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');

// ENCRYPT

$query = "SELECT id_product_code, code
            FROM "._DB_PREFIX_."product_code
		WHERE last_digits = '0' ORDER BY id_product_code";

$codes = Db::getInstance()->executeS($query);
//echo '<pre>';print_r($codes); die();

foreach ( $codes as $code ) {
    $codeencrypt = Encrypt::encrypt(Configuration::get('PS_FLUZ_CODPRO_KEY') , $code['code']);
    $lastdigits = substr($code['code'], -4);
    
    Db::getInstance()->execute("UPDATE "._DB_PREFIX_."product_code
                                SET code = '".$codeencrypt."', last_digits = '".$lastdigits."'
                                WHERE id_product_code = ".$code['id_product_code']);
}


// DECRYPT
/*
$query = "SELECT id_product_code, code
            FROM "._DB_PREFIX_."product_code ORDER BY id_product_code DESC";

$codes = Db::getInstance()->executeS($query);

foreach ( $codes as $code ) {
    $codedecrypt = Encrypt::decrypt(Configuration::get('PS_FLUZ_CODPRO_KEY') , $code['code']);

    echo $code['code']."<br>";
    echo $codedecrypt."<hr>";
}
*/
