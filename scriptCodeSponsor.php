<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once ('.override/controllers/admin/AdminCartsController.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/allinone_rewards.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsSponsorshipModel.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsModel.php');

/*$query_list = 'SELECT sc.id_sponsor, LENGTH(sc.`code`) 
                FROM '._DB_PREFIX_.'rewards_sponsorship_code sc  
                WHERE LENGTH(sc.`code`) <= 2';*/

$query_customer = Db::getInstance()->executeS('SELECT id_customer, username FROM '._DB_PREFIX_.'customer WHERE referral_code IS NULL');

//$list_mail = DB::getInstance()->executeS($query_list);

foreach ($query_customer as $customer){
        
        $insert_customer = Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_rewards_sponsorship_code, id_sponsor, code_sponsor, code)
                            VALUES ("", '.$customer['id_customer'].',NULL,CONCAT("'.$customer['username'].'",  FLOOR(RAND()*20)))');
                            
                /*$update_sponsor = Db::getInstance()->execute('UPDATE  '._DB_PREFIX_.'rewards_sponsorship_code sc 
                LEFT JOIN '._DB_PREFIX_.'customer c
                ON   c.id_customer = sc.id_sponsor
                SET    sc.`code` =  CONCAT(c.username,  FLOOR(RAND()*20))
                WHERE sc.id_sponsor = '.$customer['id_sponsor']);*/
}

Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'customer c
                    LEFT JOIN
                    ps_rewards_sponsorship_code rsc
                    ON      rsc.id_sponsor = c.id_customer
                    SET     c.referral_code = rsc.code');

?>