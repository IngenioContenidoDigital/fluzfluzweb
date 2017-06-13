<?php

include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/allinone_rewards.php');
include_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsModel.php');

error_reporting(E_ALL);

    $username_search = strtolower($_POST['username']);
    $id_customer = $_POST['id_customer'];
    
    $tree = RewardsSponsorshipModel::_getTree($id_customer);
    
    foreach ($tree as &$network){
        $sql = 'SELECT username, email FROM '._DB_PREFIX_.'customer 
                WHERE id_customer='.$network['id'];
        $row_sql = Db::getInstance()->getRow($sql);

        $network['username'] = $row_sql['username'];
        $network['email'] = $row_sql['email'];

    }    
    
    if (!empty($username_search)){
        $usersFind = array();
        foreach ($tree as &$usertree){
            $username = strtolower($usertree['username']);
            $email = strtolower($usertree['email']);
            
            $coincidenceusername = strpos($username,$username_search);
            $coincidenceemail = strpos($email,$username_search);
            if ( $coincidenceusername !== false || $coincidenceemail !== false ) {
                $usersFind[] = $usertree;
            }
        }
        echo json_encode($usersFind);
    }
    
    
    
