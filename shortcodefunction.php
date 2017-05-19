<?php

include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');

error_reporting(E_ALL);

    $value = $_POST["value"];
    
    $query = 'SELECT var_template FROM '._DB_PREFIX_.'mail_send WHERE name_mail = '."'$value'";
    $array_list = DB::getInstance()->executeS($query);
    
    $list_var = explode(",", $array_list[0]['var_template']);
    
    echo json_encode($list_var);
 