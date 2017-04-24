<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');

error_reporting(E_ALL);
//echo 'Hey';

if ( isset($_POST) && !empty($_POST) && isset($_POST["action"]) && !empty($_POST["action"]) ) {
    $id_customer=$_POST["profile"];
    $id_manufacturer=$_POST["id_manu"];
    //echo 'Cliente: '.$id_customer.' Fabricante: '.$id_manufacturer.' Accion: '.$_POST['action'];
    
    switch ( $_POST["action"] ) {        
        case "getCardsbySupplier":
            $cardsSupplier = new cardsSupplier();
            echo json_encode($cardsSupplier->getCardsbySupplier2($id_customer, $id_manufacturer));
            break;
        default:
            echo 0;
    }
    
} else {
    echo 0;
}
     
class cardsSupplier {
    public function getCardsbySupplier2($id_customer,$id_manufacturer){
        $query = "SELECT
                    PC.`code` AS card_code,
                    PC.pin_code as codepin,
                    PL.`name` AS product_name, PL.link_rewrite, PL.id_lang,  PL.description, PL.description_short,
                    PC.id_product,
                    PC.used AS used,
                    PP.id_manufacturer, 
                    PP.type_currency,
                    PP.id_supplier, 
                    PP.price_shop AS price,
                    PP.price AS price_value,
                    DATE_FORMAT( PO.date_add ,'%d/%m/%Y') AS date,
                    PPI.id_image, 
                    PPI.cover,
                    C.secure_key
                FROM "._DB_PREFIX_."product_code PC
                INNER JOIN "._DB_PREFIX_."order_detail POD ON PC.id_order = POD.id_order AND PC.id_product = POD.product_id
                INNER JOIN "._DB_PREFIX_."orders PO ON POD.id_order = PO.id_order
                INNER JOIN "._DB_PREFIX_."product PP ON PC.id_product = PP.id_product
                INNER JOIN "._DB_PREFIX_."product_lang PL ON PP.id_product = PL.id_product
                LEFT JOIN "._DB_PREFIX_."image AS PPI ON PP.id_product = PPI.id_product
                LEFT JOIN "._DB_PREFIX_."customer C ON PO.id_customer = C.id_customer
                WHERE ((PO.current_state = 2 OR PO.current_state = 5) AND (PO.id_customer =".(int)$id_customer.") AND (PP.id_manufacturer =".(int)$id_manufacturer.") AND (PL.id_lang=1"./*$this->context->language->id.*/"))
                GROUP BY PC.`code`, PL.`name`, PL.link_rewrite
                ORDER BY product_name ASC";
          
        $cards = Db::getInstance()->executeS($query);
        
        foreach ($cards as &$card) {
            $card['card_code_cry'] = $card['card_code'];
            $card['card_code'] = Encrypt::decrypt($card['secure_key'] , $card['card_code']);
        }
        
        return $cards;
      }
}

