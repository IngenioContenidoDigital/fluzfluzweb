<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once('./modules/allinone_rewards/models/RewardsSponsorshipModel.php');

/* CLIENTES KICK OUT */
$query = "SELECT rs.*
            FROM "._DB_PREFIX_."customer c
            INNER JOIN "._DB_PREFIX_."rewards_sponsorship rs ON ( c.id_customer = rs.id_customer)
            WHERE c.kick_out = 1";
$customers_kick_out = Db::getInstance()->executeS($query);
foreach ($customers_kick_out as $customer_kick_out) {
    // Nivel Cliente Kick Out
    $numSponsorships = RewardsSponsorshipModel::getSponsorshipAscendants($customer_kick_out['id_customer']);
    $level = count( array_slice($numSponsorships, 1, 15) );
    
    /* INSERTAR CLIENTE EN TABLA DE KICK OUT'S */
    $query = "INSERT INTO "._DB_PREFIX_."rewards_sponsorship_kick_out(id_sponsor, id_customer, email, lastname, firstname, date_add, date_kick_out, level)
                VALUES (".$customer_kick_out['id_sponsor'].", ".$customer_kick_out['id_customer'].", '".$customer_kick_out['email']."', '".$customer_kick_out['lastname']."', '".$customer_kick_out['firstname']."', '".$customer_kick_out['date_add']."', NOW(), ".$level.")";
    $result = Db::getInstance()->execute($query);

    /* TRAER ARBOL COMPLETO CLIENTE */
    $sponsorships = RewardsSponsorshipModel::_getTreeComplete($customer_kick_out['id_customer']);
    foreach ($sponsorships as &$sponsorship) {
        /* TRAER INFORMACION NECESARIA PARA LA COMPARACION DE MEJOR SPONSORSHIP */
        $query = "SELECT IFNULL(SUM(r.credits),0) points, c.date_add date_add_customer, UNIX_TIMESTAMP(c.date_add) date_add_unix_customer, rs.date_add date_add_sponsorship, UNIX_TIMESTAMP(rs.date_add) date_add_unix_sponsorship
                    FROM "._DB_PREFIX_."rewards_sponsorship rs
                    LEFT JOIN "._DB_PREFIX_."customer c ON ( rs.id_customer = c.id_customer )
                    LEFT JOIN "._DB_PREFIX_."rewards r ON ( rs.id_customer = r.id_customer )
                    WHERE rs.id_customer = ".$sponsorship['id'];
        $informationValidation = Db::getInstance()->executeS($query);
        $sponsorship = array_merge($sponsorship, $informationValidation[0]);
    }
    echo '<pre>';
    print_r($sponsorships);
    die();
    
    /* ELIMINAR CLIENTE RED */
    // Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_."rewards_sponsorship WHERE id_customer = ".$customer_kick_out['id_customer']);
}