<?php

include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$query_users = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'rewards_sponsorship WHERE id_customer = 0');

if(!empty($query_users)){
    foreach ($query_users as $user){
        
        $query_customer_email = Db::getInstance()->executeS('SELECT id_customer, email FROM '._DB_PREFIX_.'customer WHERE email = "'.$user['email'].'" ');
        if(!empty($query_customer_email)){
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'rewards_sponsorship SET id_customer = '.$query_customer_email[0]['id_customer'].' WHERE email = "'.$user['email'].'" AND id_customer = 0');
        }
        else{
            Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'rewards_sponsorship WHERE email = "'.$user['email'].'" AND id_customer = 0');
        }
    }
}