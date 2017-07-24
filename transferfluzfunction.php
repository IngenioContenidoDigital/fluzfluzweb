<?php

include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once(_PS_MODULE_DIR_.'allinone_rewards/allinone_rewards.php');
include_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsModel.php');

error_reporting(E_ALL);

    $username_search = strtolower($_POST['username']);
    $id_customer = $_POST['id_customer'];
    
    $tree = Db::getInstance()->executeS('SELECT c.id_customer as id FROM '._DB_PREFIX_.'customer  c
            LEFT JOIN '._DB_PREFIX_.'customer_group cg ON (c.id_customer = cg.id_customer)
            WHERE c.active = 1 AND cg.id_group = 4 AND c.id_customer != 1');
    
    foreach ($tree as &$network){
        $sql = 'SELECT username, email, dni FROM '._DB_PREFIX_.'customer 
                WHERE id_customer='.$network['id'];
        $row_sql = Db::getInstance()->getRow($sql);

        $network['username'] = $row_sql['username'];
        $network['email'] = $row_sql['email'];
        $network['dni'] = $row_sql['dni'];
    }
    
    if (!empty($username_search)){
        $usersFind = array();
        foreach ($tree as &$usertree){
            $username = strtolower($usertree['username']);
            $email = strtolower($usertree['email']);
            $dni = $usertree['dni'];
            
            $coincidenceusername = strpos($username,$username_search);
            $coincidenceemail = strpos($email,$username_search);
            $coincidendni = strpos($dni,$username_search);
            
            if ( $coincidenceusername !== false || $coincidenceemail !== false || $coincidendni !== false) {
                $usersFind[] = $usertree;
            }
        }
        echo json_encode($usersFind);
    }
    
    
    
