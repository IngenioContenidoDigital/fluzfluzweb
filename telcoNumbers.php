<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');

error_reporting(E_ALL);

if ( isset($_POST) && !empty($_POST) && isset($_POST["action"]) && !empty($_POST["action"]) ) {
    $id_customer = $_POST["id"];
    
    switch ( $_POST["action"] ) {        
        case "default":
            $number = $_POST["number"];
            $telcoNumbers = new telcoNumbers();
            echo $telcoNumbers->defaultNumber($id_customer, $number);
            break;
        case "delete":
            $number = $_POST["number"];
            $telcoNumbers = new telcoNumbers();
            echo $telcoNumbers->deleteNumber($id_customer, $number);
            break;
        case "update":
            $number = $_POST["number"];
            $newnumber = $_POST["newnumber"];
            $telcoNumbers = new telcoNumbers();
            echo $telcoNumbers->updateNumber($id_customer, $number, $newnumber);
            break;
        case "add":
            $number = $_POST["number"];
            $telcoNumbers = new telcoNumbers();
            echo $telcoNumbers->addNumber($id_customer, $number);
            break;
        default:
            echo 0;
    }
    
} else {
    echo 0;
}
     
class telcoNumbers {
    public function defaultNumber( $id_customer, $number ) {
        Db::getInstance()->execute("UPDATE "._DB_PREFIX_."address
                                    SET default_number = 0
                                    WHERE id_customer = ".$id_customer);
        
        Db::getInstance()->execute("UPDATE "._DB_PREFIX_."address
                                    SET default_number = 1
                                    WHERE id_customer = ".$id_customer."
                                    AND phone_mobile = ".$number);
        return true;
    }

    public function deleteNumber( $id_customer, $number ) {
        Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_."address
                                    WHERE id_customer = ".$id_customer."
                                    AND phone_mobile = ".$number);
        return true;
    }
    
    public function updateNumber( $id_customer, $number, $newnumber ) {
        Db::getInstance()->execute("UPDATE "._DB_PREFIX_."address
                                    SET phone_mobile = ".$newnumber."
                                    WHERE id_customer = ".$id_customer."
                                    AND phone_mobile = ".$number);
        return true;
    }

    public function addNumber( $id_customer, $number ) {
        $query = "SELECT *
                    FROM "._DB_PREFIX_."address
                    WHERE id_customer = ".$id_customer."
                    LIMIT 1";
        $address = Db::getInstance()->executeS($query);
        $address = $address[0];
        
        $queryInsert = "INSERT INTO "._DB_PREFIX_."address
                        VALUES (NULL,".$address['id_country'].", 0, ".$id_customer.", 0, 0, 0, 'Mi Direccion', '', '".$address['lastname']."', '".$address['firstname']."', '".$address['address1']."', '".$address['address2']."', '', '".$address['city']."', '', ".$address['phone'].", ".$number.", '', ".$address['type_document'].", ".$address['dni'].", ".$address['checkdigit'].", NOW(), NOW(), 1, 0, 0)";
        Db::getInstance()->execute($queryInsert);
        return true;
    }
}

