<?php

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/payulatam.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/config.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/paymentws.php');

class SondaPayu extends PayUControllerWS {

  private $url_reports; 
                       

  public function __construct($url_service = NULL,$test=TRUE) {
  
    if(Configuration::get('PAYU_LATAM_TEST') === 'true'){
        $this->url_reports = 'https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi';
    }else{
        //$this->url_reports =  'https://api.payulatam.com/reports-api/4.0/service.cgi';
        $this->url_reports = 'https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi';
    }
 }                      


public function updatePendyngOrdes(){

	$orders_pendyng = $this->getPendyngOrders();

	foreach ($orders_pendyng as $key ) {

		$order = new Order((int) $key['id_order']);

    $response= $this->getByOrderId((int) $key['orderIdPayu']);
   
    if($response['code'] === 'SUCCESS'){
      // actualizar sonda con la fecha actual, así esta orden se consultar en función de la ultima fecha de nacionalización y el intervalo definido
      $this->update_sonda($order->id_cart);

      $update = false;

        $statePol = $this->getStatePolBymessagePol($response['result']['payload']['transactions'][0]['transactionResponse']['responseCode']);

         						           if ($statePol == 7){
                                    if($order-> getCurrentState() != (int) Configuration::get('PAYU_OS_PENDING') ){
                                    $order->setCurrentState((int) Configuration::get('PAYU_OS_PENDING'));
                                    $update = true;
                                  }
                                }
                                else if ($statePol == 4){
                                    if($order-> getCurrentState() != (int) Configuration::get('PS_OS_PAYMENT') ){
                                    $order->setCurrentState((int) Configuration::get('PS_OS_PAYMENT'));
                                    $update = true;
                                  }
                                }else {
                                    if($order-> getCurrentState() != (int) Configuration::get('PS_OS_ERROR') ){
                                      $order->setCurrentState((int) Configuration::get('PS_OS_ERROR'));
                                      $update = true; 
                                    }
        	                     }
                              /**
                              if (_PS_VERSION_ >= 1.5) {
                                  $payment = $order->getOrderPaymentCollection();
                                    if (isset($payment[0])) {
                                        $payment[0]->transaction_id = pSQL(md5(Configuration::get('PS_SHOP_NAME')).$idCart);
                                        $payment[0]->save();
                                    }
                               }
                              **/
                               if($update){ // si se actualizo la orden se crea un log da la consulta generada a payu
                                  $this->response_sonda_payu($order, $response);
                               }
  }
        $queryValidation = 'SELECT COUNT(code) AS total FROM '._DB_PREFIX_.'product_code WHERE id_order='.$key['id_order'];
        $row = Db::getInstance()->getRow($queryValidation);
        $orderValidation = $row['total'];

        $quantityProducts = 'SELECT COUNT(product_id) AS productos FROM '._DB_PREFIX_.'order_detail WHERE id_order ='.$key['id_order'];

        $row2 = Db::getInstance()->getRow($quantityProducts);
        $productos = $row2['productos'];                  
        
        if($order->getCurrentState() == 2 && (($orderValidation < $productos))){
            Order::updateCodes($order);
        } 

}
        
}

public function getByOrderId($order_id){

			$conf = new ConfPayu($this->url_reports);
			$keysPayu = $conf->keys();
			$data = '{
                                    "test":';
                        if($conf->isTest()){
                            $data.='true';
                        }else{
                            $data.='false';
                        }
                            $data.=',
                                    "language":"es",
                                    "command":"ORDER_DETAIL",
                                    "merchant":{
                                    "apiLogin":"'.$keysPayu['apiLogin'].'",
                                    "apiKey":"'.$keysPayu['apiKey'].'"
                                    },
                                    "details":{
                                    "orderId":'.(int)$order_id.'
   				    }
				    }';
      
			$response= $conf->sendJson($data);
      return $response;

}

public function getPendyngOrders()
{ 
  $reference = md5(Configuration::get('PS_SHOP_NAME'));
  $results = false;
        // '".date("Y-m-d H:i:s")."' => NOW()
	$sql="SELECT orders.id_cart,orders.id_order, payu.orderIdPayu,payu.transactionId
        	FROM       "._DB_PREFIX_."orders orders 
					INNER JOIN "._DB_PREFIX_."pagos_payu payu ON(orders.id_cart = payu.id_cart)
					INNER JOIN "._DB_PREFIX_."sonda_payu sonda ON(orders.id_cart = sonda.id_cart) 
					INNER JOIN "._DB_PREFIX_."count_pay_cart contador ON (orders.id_cart = contador.id_cart)
					WHERE (orders.current_state = ".(int) Configuration::get('PAYU_OS_PENDING')." OR orders.current_state = ".(int) Configuration::get('PS_OS_OUTOFSTOCK').") 
					AND payu.orderIdPayu !=0 
					AND ( sonda.last_update + INTERVAL sonda.`interval` MINUTE) < NOW() 
					AND payu.message like 'PENDING_TRANSACTION_%'
					AND  !ISNULL(payu.orderIdPayu)				
          ORDER BY orders.id_order;"; 
   if ($results = Db::getInstance()->ExecuteS($sql) ) {   
    return $results;
  }else{
    return array();  
  }
}

public function pingPayu(){

        $conf = new ConfPayu();
        $keysPayu = $conf->keys();
  
        $data='{
                "test": ';
        if($conf->isTest()){
          $data.='true';
          }else{
           $data.='false';
          }
        $data.=',
                "language": "es",
                "command": "PING",
                "merchant":
                            {
                              "apiLogin": "'.$keysPayu['apiLogin'].'",
                              "apiKey": "'.$keysPayu['apiKey'].'"
                            }
              }';
        echo '<pre>'.print_r($data,true).'</pre><br>';
        $response= $conf->sendJson($data);

}

public function getByTransactionId($transaction_id){
 
  $conf = new ConfPayu($this->url_reports);
  $keysPayu = $conf->keys();
  $data='{
          "test":';
          if($conf->isTest()){
          $data.='true';
          }else{
           $data.='false';
          }
          $data.=',
          "language":"es",
          "command":"TRANSACTION_RESPONSE_DETAIL",
          "merchant":{
          "apiLogin":"'.$keysPayu['apiLogin'].'",
          "apiKey":"'.$keysPayu['apiKey'].'"
            },
          "details":{
          "transactionId":"'.$transaction_id.'"
            }
        }';

   $response= $conf->sendJson($data);
 return $response;
}

public function getByReference($reference){

  $conf = new ConfPayu($this->url_reports);
  $keysPayu = $conf->keys();
 
  $data='{
          "test":';
          if($conf->isTest()){
          $data.='true';
          }else{
           $data.='false';
          }
          $data.=',
          "language":"es",
          "command":"ORDER_DETAIL_BY_REFERENCE_CODE",
          "merchant":{
                      "apiLogin":"'.$keysPayu['apiLogin'].'",
                      "apiKey":"'.$keysPayu['apiKey'].'"
        },
          "details":{
          "referenceCode":"'.$reference.'"
          }
        }';

  $response= $conf->sendJson($data);
 return $response;
}

public function getPendyngOrdesConfirmation(){ 
     $reference = md5(Configuration::get('PS_SHOP_NAME'));
  $sql="SELECT orders.id_cart,orders.id_order, payu.orderIdPayu,payu.transactionId,confirmacion.message
                FROM "._DB_PREFIX_."orders orders 
		                INNER JOIN "._DB_PREFIX_."pagos_payu payu ON(orders.id_cart = payu.id_cart)
                    INNER JOIN "._DB_PREFIX_."sonda_payu sonda ON(orders.id_cart = sonda.id_cart) 
		                INNER JOIN "._DB_PREFIX_."count_pay_cart contador ON (orders.id_cart = contador.id_cart)
                    INNER JOIN "._DB_PREFIX_."log_payu_response confirmacion ON(contador.id_cart = confirmacion.id_cart)
		                WHERE (orders.current_state = ".(int) Configuration::get('PAYU_OS_PENDING')." OR orders.current_state = ".(int) Configuration::get('PS_OS_OUTOFSTOCK').")   AND payu.orderIdPayu !=0
		                AND payu.orderIdPayu = confirmacion.orderIdPayu  
		                ORDER BY orders.id_order;
      "; 
     if ($results = Db::getInstance()->ExecuteS($sql) ) {   
    return $results;
  }else{
    return array();  
  }    
}


public function updatePendyngOrdesConfirmation(){

  $orders_pendyng = $this->getPendyngOrdesConfirmation();

  foreach ($orders_pendyng as $key ) {


          $order = new Order((int) $key['id_order']);
          $statePol = $this->getStatePolBymessagePol( $key['message']);
            
                        if ($statePol == 7){
                            if($order-> getCurrentState() != (int) Configuration::get('PAYU_OS_PENDING') )
                            $order->setCurrentState((int) Configuration::get('PAYU_OS_PENDING'));
                        }
                        else if ($statePol == 4){
                            if($order-> getCurrentState() != (int) Configuration::get('PS_OS_PAYMENT') )
                            $order->setCurrentState((int) Configuration::get('PS_OS_PAYMENT'));
                        }
                        else {
                            if($order-> getCurrentState() != (int) Configuration::get('PS_OS_ERROR') ){
                            $order->setCurrentState((int) Configuration::get('PS_OS_ERROR'));
                            }  
                          }
                          
        $queryValidation = 'SELECT COUNT(code) AS total FROM '._DB_PREFIX_.'product_code WHERE id_order='.$key['id_order'];
        $row = Db::getInstance()->getRow($queryValidation);
        $orderValidation = $row['total'];

        $quantityProducts = 'SELECT COUNT(product_id) AS productos FROM '._DB_PREFIX_.'order_detail WHERE id_order ='.$key['id_order'];

        $row2 = Db::getInstance()->getRow($quantityProducts);
        $productos = $row2['productos'];                  
        
        if($order->getCurrentState() == 2 && (($orderValidation < $productos))){
            Order::updateCodes($order);
        }                  
                          
        if (_PS_VERSION_ >= 1.5) {
            $payment = $order->getOrderPaymentCollection();
            if (isset($payment[0])) {
               $payment[0]->transaction_id = pSQL("payU_".$key['id_cart']);
               $payment[0]->save();
            }
        }        
        
echo '<br>Order: '.$key['id_order'];
  }
}

public function getStatePolBymessagePol($message_pol){
  switch ($message_pol) {
    case 'APPROVED':
      return 4;
      break;

    case 'ANTIFRAUD_REJECTED':
      return 6;
      break;
      case 'REJECTED':
      return 6;
      break;  

    case 'BANK_UNREACHABLE':
      return 6;
      break;

      case 'ERROR_CONVERTING_TRANSACTION_AMOUNTS':
      return 6;
      break;
      
    case 'EXPIRED_CARD':
      return 6;
      break;

    case 'EXPIRED_TRANSACTION':
      return 5;
      break;
    case 'INTERNAL_PAYMENT_PROVIDER_ERROR':
      return 6;
      break; 
    case 'INVALID_CARD':
      return 6;
      break; 
    case 'NOT_ACCEPTED_TRANSACTION':
      return 6;
      break; 
      case 'PAYMENT_NETWORK_REJECTED':
      return 6;
      break; 
      case 'RESTRICTED_CARD':
      return 6;
      break;                                               
      case 'PENDING_TRANSACTION_CONFIRMATION':
      return 7;
      break;  
      case 'PENDING':
      return 7;
      break;
      case 'PENDING_TRANSACTION_REVIEW':
      return 7;
      break;
   
    default:
      return 6;
      break;
  }
}

public function update_sonda($id_cart){
  $date = date("Y-m-d H:i:s");
  $sql="update "._DB_PREFIX_."sonda_payu
        set last_update = '".$date."'
        WHERE id_cart = ".(int)$id_cart.";";

        if (Db::getInstance()->Execute($sql) ) {   
            return true;
          }
          return false;
}

public function response_sonda_payu($order, $response_ws){

        $date = date("Y-m-d H:i:s");
        $sql="INSERT INTO `"._DB_PREFIX_."response_sonda_payu` (`id_order`, `id_cart`, `response_ws`, `date_add`, `responseCode`, `id_transaction`, `id_payload`)
              VALUES (".(int)$order->id.", ".(int)$order->id_cart.", '".print_r($response_ws,true)."', '".$date."', '".$response_ws['result']['payload']['transactions'][0]['transactionResponse']['responseCode']."', '".$response_ws['result']['payload']['transactions'][0]['id']."', ".$response_ws['result']['payload']['id'].");";
        
           if (Db::getInstance()->Execute($sql) ) {   
            return true;
          }
          return false;
}

/*    public function updatePendyngCustomers(){
        // Inactivar Clientes - Orden Error de pago
        $customers = Db::getInstance()->ExecuteS("SELECT c.id_customer
                                                FROM "._DB_PREFIX_."customer c
                                                INNER JOIN "._DB_PREFIX_."orders o ON ( c.id_customer = o.id_customer )
                                                INNER JOIN "._DB_PREFIX_."order_detail od ON ( o.id_order = od.id_order AND od.product_reference = 'MFLUZ' )
                                                WHERE o.id_customer NOT IN (
                                                    SELECT c.id_customer
                                                    FROM "._DB_PREFIX_."customer c
                                                    INNER JOIN "._DB_PREFIX_."orders o ON ( c.id_customer = o.id_customer AND o.current_state = 2 )
                                                    INNER JOIN "._DB_PREFIX_."order_detail od ON ( o.id_order = od.id_order AND od.product_reference = 'MFLUZ' )
                                                    GROUP BY c.id_customer
                                                )
                                                GROUP BY o.id_customer");
        foreach ( $customers as $customer ) {
            Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."customer SET active = 0, id_default_group = 2 WHERE id_customer = ".$customer['id_customer']);
            Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."customer_group WHERE id_customer = ".$customer['id_customer']);
            Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."customer_group VALUES (".$customer['id_customer'].",2)");
        }

        // Activar Clientes - Orden Pago aceptado
        $customers = Db::getInstance()->ExecuteS("SELECT c.id_customer
                                                FROM "._DB_PREFIX_."customer c
                                                INNER JOIN "._DB_PREFIX_."orders o ON ( c.id_customer = o.id_customer AND o.current_state = 2 )
                                                INNER JOIN "._DB_PREFIX_."order_detail od ON ( o.id_order = od.id_order AND od.product_reference = 'MFLUZ' )
                                                WHERE c.manual_inactivation = 0
                                                GROUP BY c.id_customer");
        foreach ( $customers as $customer ) {
            Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."customer SET active = 1, id_default_group = 4 WHERE id_customer = ".$customer['id_customer']);
            Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."customer_group WHERE id_customer = ".$customer['id_customer']);
            Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."customer_group VALUES (".$customer['id_customer'].",3)");
            Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."customer_group VALUES (".$customer['id_customer'].",4)");
        }
    }*/
}
?>
