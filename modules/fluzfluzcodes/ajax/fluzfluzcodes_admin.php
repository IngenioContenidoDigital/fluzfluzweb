<?php
include_once('../../../config/config.inc.php');
include_once('../../../config/defines.inc.php');

if ( isset($_POST) && !empty($_POST) && isset($_POST["action"]) && !empty($_POST["action"]) ) {
    switch ( $_POST["action"] ) {        
        case "deletecode":
            $fluzfluzcodes = new fluzfluzcodes_admin();
            echo $fluzfluzcodes->deleteCode( $_POST["product"], $_POST["id_product_code"] );
            break;
        case "deletecodeall":
            $fluzfluzcodes = new fluzfluzcodes_admin();
            echo $fluzfluzcodes->deleteCodeAll( $_POST["product"], $_POST["id_product_code"] );
            break;
        default:
            echo 0;
    }
} else { echo 0; }

class fluzfluzcodes_admin {
    public function deleteCode( $product, $id_product_code ) {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS( "DELETE FROM "._DB_PREFIX_."product_code WHERE id_product_code = '".$id_product_code."' AND id_product = '".$product."'" );
        return $result;
    }
    
    public function deleteCodeAll( $product, $id_product_code ) {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS( "DELETE FROM "._DB_PREFIX_."product_code
                                WHERE id_product = '".$product."' AND id_order = 0 AND (state IS NULL or state = 'Disponible' )" );
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'stock_available SET quantity = 0
                                    WHERE id_product = '.$product);
        
        return $result;
    }
}