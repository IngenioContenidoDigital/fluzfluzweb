<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');

if ( isset($_POST) && !empty($_POST) && isset($_POST["action"]) && !empty($_POST["action"]) ) {
        
    switch ( $_POST["action"] ) {        
        case "updateautoaddnetwork":
            $autoaddnetwork = new autoaddnetwork();
            echo $autoaddnetwork->updateautoaddnetwork($_POST["id"], $_POST["value"]);
        break;

        default:
            echo 0;
    }
    
} else {
    echo 0;
}
     
class autoaddnetwork {
    public static function updateautoaddnetwork($id_customer, $value) {
        if ( $id_customer != "" && $value != "" ) {
            $query = 'UPDATE '._DB_PREFIX_.'customer
                        SET autoaddnetwork = '.$value.'
                        WHERE id_customer = '.$id_customer;
            return Db::getInstance()->execute($query);
        } else {
            return 0;
        }
    }
}

