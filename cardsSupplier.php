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
        $query="SELECT PC.`code` AS card_code, 
	PL.`name` AS product_name, PL.link_rewrite, PL.id_lang,  PL.description,
	PC.id_product, 
	PP.id_manufacturer, 
	PP.id_supplier, 
        PP.price_shop AS price,
	PPI.id_image, 
	PPI.cover
        FROM ps_product_code PC INNER JOIN ps_order_detail POD ON PC.id_order = POD.id_order AND PC.id_product = POD.product_id
	 INNER JOIN ps_orders PO ON POD.id_order = PO.id_order
	 INNER JOIN ps_product PP ON PC.id_product = PP.id_product
	 LEFT JOIN ps_image AS PPI ON PP.id_product = PPI.id_product
	 INNER JOIN ps_product_lang PL ON PP.id_product = PL.id_product
        WHERE ((PO.current_state = 2 OR PO.current_state = 5) AND (PO.id_customer =".(int)$id_customer.") AND (PP.id_manufacturer =".(int)$id_manufacturer.") AND (PPI.cover=1) AND (PL.id_lang=1"./*$this->context->language->id.*/"))
        GROUP BY PC.`code`, PL.`name`, PL.link_rewrite
        ORDER BY product_name ASC";
          
        $cards=Db::getInstance()->executeS($query);
        return $cards;
      }
}

