<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');

if ( isset($_POST) && !empty($_POST) && isset($_POST["action"]) && !empty($_POST["action"]) ) {
    $id_customer_send = $_POST["idsend"];
    $id_customer_receive = $_POST["idreceive"];
    $message = $_POST["message"];

    switch ( $_POST["action"] ) {        
        case "sendmessage":
            $messagesponsor = new messagesponsor();
            echo $messagesponsor->sendMessage($id_customer_send, $id_customer_receive, $message);
            break;
        default:
            echo 0;
    }
    
} else {
    echo 0;
}
     
class messagesponsor {
    public function sendMessage( $id_customer_send, $id_customer_receive, $message ) {
        $query = "INSERT INTO "._DB_PREFIX_."message_sponsor(id_customer_send, id_customer_receive, message, date_send)
                    VALUES (".$id_customer_send.", ".$id_customer_receive.", '".$message."', NOW())";
        $result = Db::getInstance()->execute($query);
        return $result;
    }
}

