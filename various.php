<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once('./modules/allinone_rewards/models/RewardsSponsorshipModel.php');
require_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsModel.php');

$users = Db::getInstance()->ExecuteS("SELECT * FROM ps_rewards_sponsorship_OLD ORDER BY id_customer");

foreach ( $users as $user ) {
    $sponsor = Db::getInstance()->ExecuteS("SELECT
                                            c.id_customer,
                                            c.username,
                                            c.email,
                                            c.firstname, c.lastname, c.date_add,
                                            (2 - COUNT(rs.id_sponsorship)) pendingsinvitation
                                        FROM ps_customer c
                                        LEFT JOIN ps_rewards_sponsorship rs2 ON ( c.id_customer = rs2.id_customer )
                                        LEFT JOIN ps_rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                                        LEFT JOIN ps_customer_group cg ON ( c.id_customer = cg.id_customer AND cg.id_group = 4 )
                                        WHERE c.active = 1
                                        AND c.kick_out = 0
                                        AND rs2.id_sponsorship IS NOT NULL
                                        GROUP BY c.id_customer
                                        HAVING pendingsinvitation > 0
                                        ORDER BY c.id_customer ASC
                                        LIMIT 1");
    
    
    $users = Db::getInstance()->execute("INSERT INTO ps_rewards_sponsorship(id_sponsor, channel, email, lastname, firstname, id_customer, id_cart_rule, date_end, date_add, date_upd)
                                        VALUES(".$sponsor[0]['id_customer'].", 1, '".$user['email']."', '".$user['lastname']."', '".$user['firstname']."', '".$user['id_customer']."', 0, '0000-00-00 00:00:00', '".$user['date_add']."', '".$user['date_upd']."')");
    
}

