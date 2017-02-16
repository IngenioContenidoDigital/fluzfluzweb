<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');

error_reporting(E_ALL);

if ( isset($_POST) && !empty($_POST) && isset($_POST["action"]) && !empty($_POST["action"]) ) {
    
    switch ( $_POST["action"] ) {        
        case "getManufacturer":
            $manufacturer = $_POST["manufacturer"];
            $filterShop = new filterShop();
            echo $filterShop->getManufacturer($manufacturer);
            break;
        default:
            echo 0;
    }

} else {
    echo 0;
}
     
class filterShop {
    public function getManufacturer( $manufacturer ) {
        $query = "SELECT name
                    FROM "._DB_PREFIX_."manufacturer
                    WHERE id_manufacturer = ".$manufacturer;
        return Db::getInstance()->getValue($query);
    }
}

