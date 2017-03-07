<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');

// ENCRYPT

$query = "SELECT c.id_card, c.id_customer, c.num_creditCard, cc.secure_key
            FROM "._DB_PREFIX_."cards c
            INNER JOIN "._DB_PREFIX_."customer cc ON ( c.id_customer = cc.id_customer )";

$cards = Db::getInstance()->executeS($query);

foreach ( $cards as $card ) {
    $cardencrypt = Encrypt::encrypt($card['secure_key'] , $card['num_creditCard']);
    $lastdigits = substr($card['num_creditCard'], -4);
    $carddecrypt = Encrypt::decrypt($card['secure_key'] , $cardencrypt);
    
    Db::getInstance()->execute("UPDATE "._DB_PREFIX_."cards
                                SET num_creditCard = '".$cardencrypt."', last_digits = '".$lastdigits."'
                                WHERE id_card = ".$card['id_card']);
}


// ENCRYPT
/*
$query = "SELECT c.id_card, c.id_customer, c.num_creditCard, cc.secure_key
            FROM "._DB_PREFIX_."cards c
            INNER JOIN "._DB_PREFIX_."customer cc ON ( c.id_customer = cc.id_customer )";

$cards = Db::getInstance()->executeS($query);

foreach ( $cards as $card ) {
    $carddecrypt = Encrypt::decrypt($card['secure_key'] , $card['num_creditCard']);

    echo $card['num_creditCard']."<br>";
    echo $carddecrypt."<hr>";
}
 */