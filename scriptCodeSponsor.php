<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once ('.override/controllers/admin/AdminCartsController.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/allinone_rewards.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsSponsorshipModel.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsModel.php');

$query_list = 'SELECT sc.id_sponsor, LENGTH(sc.`code`) 
                FROM '._DB_PREFIX_.'rewards_sponsorship_code sc  
                WHERE LENGTH(sc.`code`) <= 2';

$list_mail = DB::getInstance()->executeS($query_list);

foreach ($list_mail as $customer){
                
        $update_sponsor = Db::getInstance()->execute('UPDATE  '._DB_PREFIX_.'rewards_sponsorship_code sc 
                LEFT JOIN '._DB_PREFIX_.'customer c
                ON   c.id_customer = sc.id_sponsor
                SET    sc.`code` =  CONCAT(c.username,  FLOOR(RAND()*20))
                WHERE sc.id_sponsor = '.$customer['id_sponsor']);
}
?>