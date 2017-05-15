<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');

error_reporting(E_ALL);

        $email = $_POST["email"];
        $status_value = $_POST["value_status"];
        $status_name = $_POST["status_name"];
        
        $query = 'UPDATE '._DB_PREFIX_.'mail_send SET status_mail = '.$status_value.', status_name= '."'$status_name'".' WHERE name_mail= '."'$email'";
        console.log($query);
        try{
            $x = Db::getInstance()->execute($query);
        }catch(Exception $e){
            $x=$e->getMessage();
        }
        echo $x;
