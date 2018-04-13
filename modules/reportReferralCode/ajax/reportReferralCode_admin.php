<?php
include_once('../../../config/config.inc.php');
include_once('../../../config/defines.inc.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsSponsorshipModel.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsModel.php');

switch (Tools::getValue('action')) {
    case 'searchCode':
        $username_search = strtolower($_POST['username']);

        $tree = Db::getInstance()->executeS('SELECT rsc.code, rsc.id_sponsor, c.email 
                                    FROM '._DB_PREFIX_.'rewards_sponsorship_code rsc
                                    INNER JOIN '._DB_PREFIX_.'customer c ON (rsc.id_sponsor = c.id_customer)
                                    WHERE c.active = 1 AND c.kick_out != 1');

        if (!empty($username_search)){
            $usersFind = array();
            foreach ($tree as &$usertree){
                $username = strtolower($usertree['code']);
                $email = strtolower($usertree['email']);
                $dni = $usertree['id_sponsor'];

                $coincidenceusername = strpos($username,$username_search);
                $coincidenceemail = strpos($email,$username_search);
                $coincidendni = strpos($dni,$username_search);

                if ( $coincidenceusername !== false || $coincidenceemail !== false || $coincidendni !== false) {
                    $usersFind[] = $usertree;
                }
            }
            die (json_encode($usersFind));
        }
        break;
    case 'clickSearch':
        $code_referral = Tools::getValue('referral_code');
        $id_customer = Tools::getValue('id_customer');
        $tree = RewardsSponsorshipModel::_getTree($id_customer);
        
        $email_sponsors = Db::getInstance()->executeS('SELECT c.email, c.date_add, c.id_customer
                            FROM '._DB_PREFIX_.'rewards_sponsorship_code rsc
                            INNER JOIN '._DB_PREFIX_.'customer c ON (rsc.id_sponsor = c.id_customer)
                            WHERE rsc.code_sponsor = "'.$code_referral.'"');
        
        foreach ($email_sponsors as &$network){
            foreach ($tree as $x){
                if($x['id'] == $network['id_customer']){
                    $network['level_sponsorship'] = $x['level'];
                }
            }
        }
        
        usort($email_sponsors, function($a, $b) {
            return $a['level_sponsorship'] - $b['level_sponsorship'];
        });
        
        die (json_encode($email_sponsors));
        break; 
    default:
        break;
}