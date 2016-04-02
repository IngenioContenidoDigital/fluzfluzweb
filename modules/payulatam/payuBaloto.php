<?php

$useSSL = true;
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
require_once(_PS_MODULE_DIR_.'payulatam/payulatam.php');
require_once(_PS_MODULE_DIR_.'payulatam/config.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/paymentws.php');

class PayuBaloto extends PayUControllerWS{    
   
    
    public $ssl = true;
   

	public function setMedia()
	{
		parent::setMedia();
	}

	public function process()
	{
            
     if(empty( $this->context->cart->id)){
         Tools::redirect('/');  
     }       
		parent::process();               
                

 $params = $this->initParams();

        // url para re intentos de pago
        $url_reintento=$_SERVER['HTTP_REFERER'];
          if(!strpos($_SERVER['HTTP_REFERER'], 'step=3')){
             if(!strpos($_SERVER['HTTP_REFERER'], '?')){
              $url_reintento.='?step=3';
            }else{
              $url_reintento.='&step=3';
            }
            }
          // vaciar errores en el intento de pago anterior  
        if(isset($this->context->cookie->{'error_pay'})){
            unset($this->context->cookie->{'error_pay'});
        }


if (isset($_POST['pagar_baloto']))
  {
    $conf=new ConfPayu();
    if($conf->exist_cart_in_pagos($this->context->cart->id)){
        if(isset($this->context->cookie->{'url_confirmation'})){
            Tools::redirectLink(json_decode($this->context->cookie->{'url_confirmation'}));
        }
        Tools::redirectLink('/');
        exit();
    }   
    $id_cart = $this->context->cart->id;
    $id_address = $this->context->cart->id_address_delivery;
    $customer = new Customer((int)$this->context->cart->id_customer);
    $reference_code=$customer->id.'_'.$id_cart.'_0_'.$id_address;
    $address = $conf->get_address($this->context->cart->id_customer, $this->context->cart->id_address_delivery);
    $fecha = date('Y-m-j');
    $nuevafecha = strtotime ( '+3 day' , strtotime ( $fecha ) ) ;
    $fechaBaloto=date ( 'Y-m-d' , $nuevafecha ).'T'.date ( 'h:i:s' , $nuevafecha );          
    $keysPayu= $conf->keys();
    $intentos = $conf->count_pay_cart($id_cart);

// Script Json payuLatam (Baloto)              
$data='{
"language":"es",
"command":"SUBMIT_TRANSACTION",
"merchant":{
"apiLogin":"'.$keysPayu['apiLogin'].'",
"apiKey":"'.$keysPayu['apiKey'].'"
},
"transaction":{
"order":{
"accountId":"'.$keysPayu['accountId'].'",
"referenceCode":"'.$params[2]['referenceCode']. '_'.$intentos.'",
"description":"'.$reference_code.'",
"language":"es",
"notifyUrl":"'.$conf->urlv().'",
"signature":"'.$conf->sing($params[2]['referenceCode']. '_'.$intentos.'~' .$params[4]['amount'].'~'.$params[9]['currency']).'",
"shippingAddress":{
"country":"'.$address['iso_code'].'"
},
"buyer":{
"fullName":"'.$this->context->customer->firstname.' '. $this->context->customer->lastname.'",
"emailAddress":"'.$params[5]['buyerEmail'].'",
"dniNumber":"'.$address['dni'].'",
"shippingAddress":{
"street1":"'.$address['address1'].'",
"city":"'.$address['city'].'",
"state":"'.$address['state'].'",
"country":"'.$address['iso_code'].'",
"phone":"'.$address['phone_mobile'].'"
}
},
"additionalValues":{
"TX_VALUE":{
"value":'.$params[4]['amount'].',
"currency":"'.$params[9]['currency'].'"
}
}
},
"type":"AUTHORIZATION_AND_CAPTURE",
"paymentMethod":"BALOTO",
"expirationDate":"'.$fechaBaloto.'",
"paymentCountry": "' . $address['iso_code'] . '"     
},
"test":false
}
';


$response = $conf->sendJson($data);

// colector Errores Payu
$error_pay = array();

if($response['code'] === 'ERROR')
{
$conf->error_payu(0, $customer->id, $data, $response, 'Baloto', $response['transactionResponse']['state'], $this->context->cart->id, $id_address); 
 $error_pay[]=$response;
}


        elseif ($response['code'] === 'SUCCESS' && $response['transactionResponse']['state'] === 'PENDING' && $response['transactionResponse']['responseMessage'] != 'ERROR_CONVERTING_TRANSACTION_AMOUNTS') {
                $extra_vars =  array('method'=>'Baloto',
                                     'cod_pago'=>$response['transactionResponse']['extraParameters']['REFERENCE'],
                                     'fechaex'=> date('d/m/Y', substr($response['transactionResponse']['extraParameters']['EXPIRATION_DATE'], 0, -3)));

                $this->createPendingOrder($extra_vars, 'Baloto', utf8_encode($conf->getMessagePayu($response['transactionResponse']['responseCode'])), 'PAYU_OS_PENDING');
                $order=$conf->get_order($id_cart);
                $extras=$response['transactionResponse']['extraParameters']['REFERENCE'].';'.date('d/m/Y', substr($response['transactionResponse']['extraParameters']['EXPIRATION_DATE'], 0, -3));
                $conf->pago_payu($order['id_order'], $customer->id, $data, $response, 'Baloto', $extras, $id_cart,$id_address);
                $orden_select = $order['id_order'];
                $payulatam = new PayULatam();
                $url_base64 = strtr(base64_encode($response['transactionResponse']['extraParameters']['URL_PAYMENT_RECEIPT_HTML']), '+/=', '-_,');
                $url_confirmation = __PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.(int)$id_cart.'&id_module='.(int)$payulatam->id.'&id_order='.(int)$orden_select.'&URL_PAYMENT_RECEIPT_HTML='.$url_base64;
                $this->context->cookie->{'url_confirmation'} = json_encode($url_confirmation);
                Tools::redirectLink($url_confirmation);

}
else {
        $conf->error_payu(0, $customer->id, $data, $response, 'Baloto', $response['transactionResponse']['state'], $this->context->cart->id, $id_address); 
        $error_pay[]=array('ERROR'=>utf8_encode($conf->getMessagePayu($response['transactionResponse']['responseCode'])));
     }

    $this->context->cookie->{'error_pay'} = json_encode($error_pay);
    Tools::redirectLink($url_reintento);
    exit();   
     
  }  else {
                $this->context->cookie->{'error_pay'} = json_encode(array('ERROR'=>'Valida tus datos he intenta de nuevo.'));
                  Tools::redirectLink($url_reintento); 
                    exit();   
        }

           

}

	public function displayContent()
	{
		parent::displayContent();
               
		self::$smarty->display(_PS_MODULE_DIR_.'payulatam/tpl/success.tpl');
	}

}


$farmaPayu = new PayuBaloto();


$farmaPayu->run();
