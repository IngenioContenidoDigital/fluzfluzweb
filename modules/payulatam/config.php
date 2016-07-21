<?php
require_once('XmlToArray.class.php');
require_once(dirname(__FILE__) . '/../../config/config.inc.php');
 class ConfPayu {

 private $testing=  array('apiKey'=>'4Vj8eK4rloUd272L48hsrarnUA',
                      'apiLogin'=>'pRRXKOl8ikMmt9u',
                      'merchantId'=>'508029',
                      'accountId'=>'512321',
                      'pse-CO'=>'512321');
 
private $test= false;

private $url_service = NULL;

public function __construct($url_service = NULL,$test=TRUE) {
  if(Configuration::get('PAYU_LATAM_TEST') === 'true'){
      $this->test = TRUE;
    }else{
          $this->test = FALSE;
    }
  $this->url_service = $url_service;

 }
                 
  
 public function keys()
  {
      if ($this->test)
      {
      return $this->testing;    
      }
 else {
        $production=  array('apiKey'=>Configuration::get('PAYU_LATAM_API_KEY'),
                          'apiLogin'=>Configuration::get('PAYU_LATAM_API_LOGIN'),
                          'merchantId'=>Configuration::get('PAYU_LATAM_MERCHANT_ID'),
                          'accountId'=>(int)Configuration::get('PAYU_LATAM_ACCOUNT_ID'),
                          'pse-CO'=>(int)Configuration::get('PAYU_LATAM_ACCOUNT_ID'));
          return $production;    
      }
  }


  
public function sendJson($data)
 {
  $responseData ='';

  try {
$ch =NULL;

if($this->url_service != NULL ){
  $ch = curl_init($this->url_service);
}
else if($this->test)
    {  
     $ch = curl_init('https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi');
    }
else {
     $ch = curl_init('https://api.payulatam.com/payments-api/4.0/service.cgi');  
     //$ch = curl_init('https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi');
     }

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // deshabilitar la validacion SSl (false)
curl_setopt_array($ch, array(
CURLOPT_POST => TRUE,
CURLOPT_RETURNTRANSFER => TRUE,
CURLOPT_HTTPHEADER => array(
                            "Content-Type: application/json; charset=utf-8",
                            "Accept: application/json"),
CURLOPT_POSTFIELDS =>$data)); //json_encode($postData) 

$response = curl_exec($ch); // enviando datos al servidor de payuLatam

$info = curl_getinfo($ch);
// echo '<br><b>Info Solicitud: </br><pre>'.print_r($info,true).'</pre><br>';
curl_close($ch);

if($response === FALSE) // si hay errores
  {
   //die(curl_error($ch));
  return false;
 }

return $responseData = json_decode($response, TRUE); // decodificando el formato Json

 } catch (Exception $ex) {
     return false;
 }
 
}


public function sendXml($data)
 {
  $responseData ='';
  try {
    $ch = NULL;

if($this->test)
    {
     $ch = curl_init('https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi');
    }

else {
     $ch = curl_init('https://api.payulatam.com/payments-api/4.0/service.cgi');  
     //$ch = curl_init('https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi');
     }


curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // deshabilitar la validacion SSl (false)
curl_setopt_array($ch, array(
CURLOPT_POST => TRUE,
CURLOPT_RETURNTRANSFER => TRUE,
CURLOPT_HTTPHEADER => array("Accept:application/xml","Content-Type:application/xml"),
CURLOPT_POSTFIELDS =>$data)); //json_encode($postData) 

$response = curl_exec($ch); // enviando datos al servidor de payuLatam

if($response === FALSE) // si hay errores
  {

   //die(curl_error($ch));
  return false;
 }

//Creating Instance of the Class
$xmlObj    = new XmlToArray($response);

//Creating Array
return $arrayData = $xmlObj->createArray();


//return ($response);//json_decode($response, TRUE); // decodificando el formato Json

 } catch (Exception $ex) {

     return false;
 }
 
}

public function randString ($length = 32)
{  
 $string = "";
 $possible = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXY";
 $i = 0;
 while ($i < $length)
     {    
      $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
      $string .= $char;    
      $i++;  
     }  
return $string;
}


public function sing($str)
        
{
  $keys=$this->keys();
  return md5($signature=$keys['apiKey'].'~'.$keys['merchantId'].'~'.$str); 
}

public function urlv() {
 $url = '';   
if (Configuration::get('PS_SSL_ENABLED') || (!empty($_SERVER['HTTPS']) && Tools::strtolower($_SERVER['HTTPS']) != 'off'))
{
	if (method_exists('Tools', 'getShopDomainSsl')){
		$url = 'https://'.Tools::getShopDomainSsl().__PS_BASE_URI__.'modules/payulatam/';
        }
	else{
		$url = 'https://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/payulatam/';
        }
}
else{
	$url = 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/payulatam/';
}

        return  $url . 'validationws.php';
    }

    public function pago_payu($id_order, $id_customer, $json_request, $json_response, $method, $extras, $id_cart, $id_address) {
        $array_rq = json_decode($json_request, TRUE);
        try {

            $mysqldate = date("Y-m-d H:i:s");

            $log = 'Fecha de transacción-WS: ' . $mysqldate . '\r\nRequest: \r\n' . $json_request . '\r\nResponse: \r\n' . json_encode($json_response);
            $this->logtxt($log);

            Db::getInstance()->autoExecute(_DB_PREFIX_.'pagos_payu', array(
                'fecha' => pSQL($mysqldate),
                'id_order' => (int) $id_order,
                'id_customer' => (int) $id_customer,
                'json_request' => pSQL(addslashes($json_request)),
                'json_response' => pSQL(addslashes(json_encode($json_response))),
                'method' => pSQL($method),
                'extras' => pSQL($extras),
                'id_cart' => (int) $id_cart,
                'id_address' => (int) $id_address,
                'transactionId' => PSQL($json_response['transactionResponse']['transactionId']),
                'valor' => (int) $array_rq['transaction']['order']['additionalValues']['TX_VALUE']['value'],
                'orderIdPayu' => (int) $json_response['transactionResponse']['orderId'],
                'message'=>PSQL($json_response['transactionResponse']['responseCode']),                
                    ), 'INSERT');
        } catch (Exception $exc) {
            Logger::AddLog('payulatam [config.php] pago_payu() error: ' . $exc->getTraceAsString(), 2, null, null, null, true);
        }
    }

    public function log_response_ws($array_rs) {
      

        $reference_code = explode("_", $array_rs['description']);

        try {
            $mysqldate = date("Y-m-d H:i:s");

            Db::getInstance()->autoExecute(_DB_PREFIX_.'log_payu_response', array(
                'date' => pSQL($mysqldate),
                'reponse' => pSQL(var_export($array_rs,TRUE)),
                'id_order' => (int) $reference_code[2],
                'id_customer' => (int) $reference_code[0],
                'id_cart' => (int) $reference_code[1],
                'id_address' => (int) $reference_code[3],
                'transactionId' => PSQL($array_rs['transaction_id']),
                'valor' => (int) $array_rs['value'],
                'orderIdPayu' => (int) $array_rs['reference_pol'],
                'message'=>PSQL($array_rs['response_message_pol']),
                    ), 'INSERT');
        } catch (Exception $exc) {
            Logger::AddLog('payulatam [config.php] pago_payu() error: ' . $exc->getTraceAsString(), 2, null, null, null, true);
        }
    }

    public function error_payu($id_order, $id_customer, $json_request, $json_response, $method, $extras, $id_cart, $id_address) {
        $array_rq = json_decode($json_request, TRUE);

        try {
            
            if (!isset($json_response['transactionResponse']['responseCode'])){
              $errortransaction = explode(",", $json_response['error']);
              $json_response['transactionResponse']['responseCode'] = $errortransaction[0];
            }

            $mysqldate = date("Y-m-d H:i:s");

          $result =  Db::getInstance()->autoExecute(_DB_PREFIX_.'error_payu', array(
                'fecha' => pSQL($mysqldate),
                'id_order' => (int) $id_order,
                'id_customer' => (int) $id_customer,
                'json_request' => pSQL(addslashes($json_request)),
                'json_response' => pSQL(addslashes(json_encode($json_response))),
                'method' => pSQL($method),
                'extras' => pSQL($extras),
                'status' => pSQL('0'),
                'id_cart' => (int) $id_cart,
                'id_address' => (int) $id_address,
                'transactionId' => PSQL(isset($json_response['transactionResponse']['transactionId']) ? $json_response['transactionResponse']['transactionId'] : NULL),
                'valor' => (int) $array_rq['transaction']['order']['additionalValues']['TX_VALUE']['value'],
                'orderIdPayu' => (int) isset($json_response['transactionResponse']['orderId']) ? $json_response['transactionResponse']['orderId'] : NULL,
                'message'=>PSQL($json_response['transactionResponse']['responseCode']),                 
                    ), 'INSERT'); 
        } catch (Exception $exc) {
            Logger::AddLog('payulatam [config.php] pago_payu() error: ' . $exc->getTraceAsString(), 2, null, null, null, true);
        }
    }

    public function get_order($id_cart) {
        try {
            $sql = 'select ord.* 
    from '._DB_PREFIX_.'orders ord INNER JOIN '._DB_PREFIX_.'cart car ON(ord.id_cart=car.id_cart) 
    WHERE  ord.id_cart=' . $id_cart . ' Limit 1';

            if ($results = Db::getInstance()->ExecuteS($sql)) {
                foreach ($results as $row) {
                    return $row;
                }
            }
            return null;
        } catch (Exception $exc) {
            Logger::AddLog('payulatam [config.php] get_order() error: ' . $exc->getTraceAsString(), 2, null, null, null, true);
            return null;
        }
    }

    public function logtxt ($text="")
{
            //$contenido="-- lo que quieras escribir en el archivo -- \r\n";
/*$fp=fopen("/home/ubuntu/log_payu/log_payu.txt","a+");
fwrite($fp,$text."\r\n");
fclose($fp) ;*/
            
        }

public function url_confirm_payu($token,$url){
    $sql = "INSERT INTO "._DB_PREFIX_."url_confirm_payu (token,url) VALUES('".$token."','".$url."')";
    return Db::getInstance()->Execute($sql);
}        
        
public function get_state($id_state)
{
    $query="select state.`name` FROM 
            "._DB_PREFIX_."state state
            WHERE state.id_state=".(int)$id_state.' limit 1;';
    
    
                if ($results = Db::getInstance()->ExecuteS($query)) {
                   
             if(count($results)>0){
                
                 return $results[0]['name'];
             }   
            }
       
   return null; 
}

public function get_address($id_customer, $id_address_delivery) {


        $sql = 'select ad.address1,city,phone_mobile,phone,dni, st.`name` as state, co.iso_code   
            from '._DB_PREFIX_.'address ad, '._DB_PREFIX_.'state st, '._DB_PREFIX_.'country co  where ad.id_customer=' . $id_customer . ''
                . ' and ad.id_address=' . $id_address_delivery . ' and ad.id_state= st.id_state and
           co.id_country =ad.id_country';


        if ($results = Db::getInstance()->ExecuteS($sql)) {
            if (count($results) > 0) {
                return $results[0];
            }
        }
        return FALSE;
    }
     
         public function get_dni($id_address) {

        $sql = 'select cus.identification, adr.dni
                            from '._DB_PREFIX_.'address adr INNER JOIN '._DB_PREFIX_.'customer cus ON (adr.id_customer = cus.id_customer) 
                            WHERE adr.id_address=' . (int) $id_address . ';';

               $dni =  'N/A';

        if ($results = Db::getInstance()->ExecuteS($sql)) {
    
            foreach ($results as $row) {
           


                if ($row['identification'] != NULL && $row['identification'] != '0') {
                    $dni = $row['identification'];
                } else if ($row['dni'] != '1111' && $row['dni'] != '') {
                    $dni = $row['dni'];
                } else {
                    $dni = 'N/A';
                }
            }
        }
        return $dni;
    }
    
    public function isTest() {
        return $this->test;
    }

    public function count_pay_cart($id_cart){

      $query= "SELECT id_cart,contador
              FROM "._DB_PREFIX_."count_pay_cart
              WHERE id_cart = ".(int)$id_cart;

      $row = Db::getInstance()->getRow($query);

      if ( isset($row) && count($row) > 1 && is_array($row)){
          $sql= "UPDATE "._DB_PREFIX_."count_pay_cart SET contador = ". ((int)$row['contador'] + 1) ." WHERE id_cart = ".$id_cart;

          if(Db::getInstance()->Execute($sql))
              return ($row['contador']+1);
            }
              else{
                    $ini=1;
                    $sql="INSERT INTO "._DB_PREFIX_."count_pay_cart (id_cart,contador)
                    VALUES(".$id_cart.",".$ini.")";   
                   if(Db::getInstance()->Execute($sql))
                   return $ini;
              }
    }
    
    public function get_intentos($id_cart){
            $query=  "SELECT id_cart,contador
                      FROM "._DB_PREFIX_."count_pay_cart
                      WHERE id_cart = ".(int)$id_cart;

            $row = Db::getInstance()->getRow($query);
            if(isset($row['contador'])){
                return $row['contador'];
            }else{
              return false;
            }

    }

 public function is_ssl() {
    if ( isset($_SERVER['HTTPS']) ) {
        if ( 'on' == strtolower($_SERVER['HTTPS']) )
            return true;
        if ( '1' == $_SERVER['HTTPS'] )
            return true;
    } elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
        return true;
    }
    return false;
}

    public function exist_cart_in_pagos($id_cart){
            $query=  "SELECT id_cart
                      FROM "._DB_PREFIX_."pagos_payu
                      WHERE id_cart = ".(int)$id_cart;

            $row = Db::getInstance()->getRow($query);
            if(isset($row['id_cart']) && !empty($row['id_cart'])){
                return TRUE;
            }else{
              return FALSE;
            }

    }

/**
 * Retorna mensajes de error en Español
 */
public function getMessagePayu($cod_payu){

  $messages = array('ERROR' => 'Ocurrió un error general.', 
                    'APPROVED'=>'La transacción fue aprobada.',
                    'ANTIFRAUD_REJECTED'=>'La transacción fue rechazada por el sistema anti-fraude.',
                    'PAYMENT_NETWORK_REJECTED'=>'La red financiera rechazó la transacción.',
                    'ENTITY_DECLINED'=>'  La transacción fue declinada por el banco o por la red financiera debido a un error.',
                    'INTERNAL_PAYMENT_PROVIDER_ERROR'=>'Ocurrió un error en el sistema intentando procesar el pago.',
                    'INACTIVE_PAYMENT_PROVIDER'=>'El proveedor de pagos no se encontraba activo.',
                    'DIGITAL_CERTIFICATE_NOT_FOUND'=>'La red financiera reportó un error en la autenticación.',
                    'INVALID_EXPIRATION_DAT'=>'El código de seguridad o la fecha de expiración estaba inválido.',
                    'E_OR_SECURITY_CODE'=>'El código de seguridad o la fecha de expiración estaba inválido.',
                    'INSUFFICIENT_FUNDS'=>'La cuenta no tenía fondos suficientes.',
                    'CREDIT_CARD_NOT_AUTHORIZED_FOR_INTERNET_TRANSACTIONS'=>'La tarjeta de crédito no estaba autorizada para transacciones por Internet.',
                    'INVALID_TRANSACTION'=>'La red financiera reportó que la transacción fue inválida.',
                    'INVALID_CARD'=>'La tarjeta es inválida.',
                    'EXPIRED_CARD'=>'La tarjeta ya expiró.',
                    'RESTRICTED_CARD'=>'La tarjeta presenta una restricción.',
                    'CONTACT_THE_ENTITY'=>'Debe contactar al banco.',
                    'REPEAT_TRANSACTION'=>'Se debe repetir la transacción.',
                    'ENTITY_MESSAGING_ERROR'=>'La red financiera reportó un error de comunicaciones con el banco.',
                    'BANK_UNREACHABLE'=>'El banco no se encontraba disponible.',
                    'EXCEEDED_AMOUNT'=>'La transacción excede un monto establecido por el banco.',
                    'NOT_ACCEPTED_TRANSACTION'=>'La transacción no fue aceptada por el banco por algún motivo.',
                    'ERROR_CONVERTING_TRANSACTION_AMOUNTS'=>'Ocurrió un error convirtiendo los montos a la moneda de pago.',
                    'EXPIRED_TRANSACTION'=>'La transacción expiró.',
                    'PENDING_TRANSACTION_REVIEW'=>'La transacción fue detenida y debe ser revisada, esto puede ocurrir por filtros de seguridad.',
                    'PENDING_TRANSACTION_CONFIRMATION'=>'La transacción está pendiente de ser confirmada.',
                    'PENDING_TRANSACTION_TRANSMISSION'=>'La transacción está pendiente para ser trasmitida a la red financiera. Normalmente esto aplica para transacciones con medios de pago en efectivo.',
                    'PAYMENT_NETWORK_BAD_RESPONSE'=>'El mensaje retornado por la red financiera es inconsistente.',
                    'PAYMENT_NETWORK_NO_CONNECTION'=>'No se pudo realizar la conexión con la red financiera.',
                    'PAYMENT_NETWORK_NO_RESPONSE'=>'La red financiera no respondió.',
                    'FIX_NOT_REQUIRED'=>'Clínica de transacciones: Código de manejo interno.',
                    'AUTOMATICALLY_FIXED_AND_SUCCESS_REVERSAL'=>'Clínica de transacciones: Código de manejo interno.<br>Sólo aplica para la API de reportes.',
                    'AUTOMATICALLY_FIXED_AND_UNSUCCESS_REVERSAL'=>'Clínica de transacciones: Código de manejo interno.<br>Sólo aplica para la API de reportes.',
                    'AUTOMATIC_FIXED_NOT_SUPPORTED'=>'Clínica de transacciones: Código de manejo interno.<br>Sólo aplica para la API de reportes.',
                    'NOT_FIXED_FOR_ERROR_STATE'=>'  Clínica de transacciones: Código de manejo interno.<br>Sólo aplica para la API de reportes.',
                    'ERROR_FIXING_AND_REVERSING'=>'Clínica de transacciones: Código de manejo interno.<br>Sólo aplica para la API de reportes.',
                    'ERROR_FIXING_INCOMPLETE_DATA'=>'   Clínica de transacciones: Código de manejo interno.<br>Sólo aplica para la API de reportes.');
  if(isset($messages[$cod_payu]))
  {
    return $messages[$cod_payu];
  }
  return 'Ocurrió un error general. __';

}    

public function addTables(){

    $return = true;
    $return &= Db::getInstance()->execute("
        CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."sonda_payu` (
          `id_sonda_payu` int(11) NOT NULL AUTO_INCREMENT,
          `id_cart` int(11) NOT NULL,
          `date_add` datetime NOT NULL,
          `last_update` datetime DEFAULT NULL,
          `interval` int(11) NOT NULL DEFAULT '10',
          `module` varchar(30) DEFAULT NULL,
          `pasarela` varchar(30) DEFAULT NULL,
          PRIMARY KEY (`id_sonda_payu`),
          KEY `id_cart_idx` (`id_cart`) USING BTREE,
          KEY `module_idx` (`module`) USING BTREE,
          KEY `last_update_ixd` (`last_update`) USING BTREE
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
    );

    $return &= Db::getInstance()->execute("
        CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."response_sonda_payu` (
          `id_response_sonda_payu` int(11) NOT NULL AUTO_INCREMENT,
          `id_order` int(11) DEFAULT NULL,
          `id_cart` int(11) DEFAULT NULL,
          `response_ws` varchar(20000) DEFAULT NULL,
          `date_add` datetime DEFAULT NULL,
          `responseCode` varchar(30) DEFAULT NULL,
          `id_transaction` varchar(36) DEFAULT NULL,
          `id_payload` int(11) DEFAULT NULL,
          PRIMARY KEY (`id_response_sonda_payu`),
          KEY `id_order_idx` (`id_order`) USING BTREE,
          KEY `id_cart_idx` (`id_cart`) USING BTREE,
          KEY `responseCode_ixd` (`responseCode`) USING BTREE,
          KEY `id_transaction_idx` (`id_transaction`) USING BTREE,
          KEY `id_payload_ixd` (`id_payload`) USING BTREE
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
    );

    $return &= Db::getInstance()->execute("
      CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."pagos_payu` (
          `id_ps_pagos_payu` int(11) NOT NULL AUTO_INCREMENT,
          `fecha` datetime NOT NULL,
          `id_order` int(11) NOT NULL,
          `id_customer` int(11) NOT NULL,
          `json_request` varchar(4096) DEFAULT NULL,
          `json_response` varchar(3096) DEFAULT NULL,
          `method` varchar(256) DEFAULT NULL,
          `extras` varchar(2048) DEFAULT NULL,
          `status` enum('1','0') DEFAULT '1',
          `id_cart` int(11) DEFAULT NULL,
          `orderIdPayu` int(11) DEFAULT NULL,
          `transactionId` varchar(200) DEFAULT NULL,
          `valor` int(11) DEFAULT NULL,
          `id_address` int(11) DEFAULT NULL,
          `message` varchar(256) DEFAULT NULL,
          PRIMARY KEY (`id_ps_pagos_payu`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
    );

        $return &= Db::getInstance()->execute("
      CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."error_payu` (
          `id_ps_pagos_payu` int(11) NOT NULL AUTO_INCREMENT,
          `fecha` datetime NOT NULL,
          `id_order` int(11) NOT NULL,
          `id_customer` int(11) NOT NULL,
          `json_request` varchar(4096) DEFAULT NULL,
          `json_response` varchar(3096) DEFAULT NULL,
          `method` varchar(256) DEFAULT NULL,
          `extras` varchar(2048) DEFAULT NULL,
          `status` enum('1','0') DEFAULT '1',
          `id_cart` int(11) DEFAULT NULL,
          `orderIdPayu` int(11) DEFAULT NULL,
          `transactionId` varchar(200) DEFAULT NULL,
          `valor` int(11) DEFAULT NULL,
          `id_address` int(11) DEFAULT NULL,
          `message` varchar(256) DEFAULT NULL,
          PRIMARY KEY (`id_ps_pagos_payu`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
    );

    $return &= Db::getInstance()->execute("
      CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."log_payu_response` (
          `id_log_payu_response` int(11) NOT NULL AUTO_INCREMENT,
          `reponse` varchar(4096) DEFAULT NULL,
          `date` datetime DEFAULT NULL,
          `orderIdPayu` int(11) DEFAULT NULL,
          `transactionId` varchar(200) DEFAULT NULL,
          `valor` int(11) DEFAULT NULL,
          `id_address` int(11) DEFAULT NULL,
          `id_cart` int(11) DEFAULT NULL,
          `id_order` int(11) DEFAULT NULL,
          `id_customer` int(11) DEFAULT NULL,
          `message` varchar(256) DEFAULT NULL,
          PRIMARY KEY (`id_log_payu_response`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
    );

    $return &= Db::getInstance()->execute("
          CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."count_pay_cart` (
          `id_cout_pay_cart` int(11) NOT NULL AUTO_INCREMENT,
          `id_cart` int(11) DEFAULT NULL,
          `contador` int(11) DEFAULT NULL,
          PRIMARY KEY (`id_cout_pay_cart`),
          KEY `id_cart_idx` (`id_cart`) USING BTREE
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
    );

    $return &= Db::getInstance()->execute("
      CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."medios_de_pago` (
        `id_medio_de_pago` int(11) NOT NULL AUTO_INCREMENT,
        `nombre` varchar(100) DEFAULT NULL,
        `Activo` tinyint(4) DEFAULT NULL,
        `type` tinyint(4) DEFAULT NULL,
        `medio_de_pago` varchar(40) DEFAULT NULL,
       `nombre_alterno` varchar(40) DEFAULT NULL,
        PRIMARY KEY (`id_medio_de_pago`),
        KEY `id_medios_p_idx` (`id_medio_de_pago`) USING BTREE
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
    );

    $return &= Db::getInstance()->execute("
      CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."url_confirm_payu` (
      `id_url_confirm_payu` int(11) NOT NULL AUTO_INCREMENT,
      `token` varchar(32) NOT NULL,
      `url` varchar(1024) DEFAULT NULL,
      PRIMARY KEY (`id_url_confirm_payu`),
      KEY `token_idx` (`token`) USING BTREE
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
    );

    $return &= Db::getInstance()->execute("
  CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."payu_pse` (
  `id_payu_pse` int(11) NOT NULL AUTO_INCREMENT,
  `id_cart` int(11) DEFAULT NULL,
  `reference_pol` int(11) DEFAULT NULL,
  `id_customer` int(11) DEFAULT NULL,
  `transactionId` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `response_pse` varchar(2048) CHARACTER SET latin1 DEFAULT NULL,
  `lapTransactionState` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  `lapResponseCode` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id_payu_pse`),
  KEY `id_cart_idx` (`id_cart`) USING BTREE,
  KEY `reference_pol_idx` (`reference_pol`) USING BTREE,
  KEY `id_customer_idx` (`id_customer`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
    );

    $return &= Db::getInstance()->execute("
  INSERT INTO `"._DB_PREFIX_."medios_de_pago` (`id_medio_de_pago`, `nombre`, `Activo`, `type`, `medio_de_pago`, `nombre_alterno`) VALUES
(1,	'cashondelivery',	1,	0,	'Efectivo',	'Pago contra entrega'),
(2,	'Tarjeta_credito',	1,	0,	'Tarjeta_credito',	'Tarjeta_credito'),
(3,	'Baloto',	1,	1,	'Baloto',	'Baloto'),
(4,	'Efecty',	1,	1,	'Efecty',	'Efecty'),
(5,	'PSE',	1,	0,	'PSE',	'Payu_pse');"
    );
    
$return &=Configuration::updateValue('PAYU_LATAM_PUBLIC_KEY', "");
$return &=Configuration::updateValue('PAYU_LATAM_API_LOGIN', "");



  return $return;

}


 }
     


