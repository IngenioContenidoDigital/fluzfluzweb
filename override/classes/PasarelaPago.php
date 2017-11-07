<?php
require_once(_PS_MODULE_DIR_ . 'payulatam/config.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/payulatam.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/paymentws.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/creditcards.class.php');

class PasarelaPagoCore extends PayUControllerWS {

    /**
    * Envia la solicitud de pago a la pasarela asociada al medio de pago
    */ 
    public static function EnviarPagoPayu($args) {
        $conf = new ConfPayu();
        $payuControll = new PayUControllerWS();
        $context = Context::getContext();

        $intentos = $conf->count_pay_cart($args['id_cart']);
        $keysPayu = $conf->keys();
        $params = $payuControll->initParams();

        $customer = new Customer((int) $context->cart->id_customer);
        $address = new Address($context->cart->id_address_invoice);

        $productsCart = $context->cart->getProducts();
        $description = "(Cliente: ".$context->customer->username."). Productos: ";
        foreach ($productsCart as $product) {
            $description .= "[".$product['name'].",".$product['cart_quantity']."] ";
        }

        $currency = $params[9]['currency'];
        $paymentMethod = self::getFranquicia($args['numerot'], 'payulatam');

        $_deviceSessionId = NULL;
        if (isset($context->cookie->deviceSessionId) && !empty($context->cookie->deviceSessionId) && strlen($context->cookie->deviceSessionId) === 32) {
            $_deviceSessionId = $context->cookie->deviceSessionId;
        } elseif (isset($_POST['deviceSessionId']) && !empty($_POST['deviceSessionId']) && strlen($_POST['deviceSessionId']) === 32) {
            $_deviceSessionId = $_POST['deviceSessionId'];
        } else {
            $_deviceSessionId = md5($context->cookie->timestamp);
        }
        
        switch ( $args['option_pay'] ) {
            case 'Tarjeta_credito':
                $data = '{
                            "language":"es",
                            "command":"SUBMIT_TRANSACTION",
                            "merchant":{
                                "apiKey":"' . $keysPayu['apiKey'] . '",
                                "apiLogin":"' . $keysPayu['apiLogin'] . '"
                            },
                            "transaction":{
                                "order":{
                                    "accountId":"' . $keysPayu['accountId'] . '",
                                    "referenceCode":"' . $params[2]['referenceCode'] . '_'.$intentos.'",
                                    "description":"' . $description . '",
                                    "language":"' . $params[10]['lng'] . '",
                                    "notifyUrl":"' . $conf->urlv() . '",
                                    "signature":"' . $conf->sing($params[2]['referenceCode'] . '_'.$intentos.'~' . $params[4]['amount'] . '~'.$currency).'",
                                    "additionalValues":{
                                        "TX_VALUE":{
                                            "value":' . $params[4]['amount'] . ',
                                            "currency":"'.$currency.'"
                                        }
                                    },
                                    "buyer": {
                                        "fullName": "'.$customer->firstname.' '.$customer->lastname.'",
                                        "contactPhone": "'.$address->phone.'",
                                        "emailAddress":"'. $params[5]['buyerEmail'].'",
                                        "dniNumber":"'.$customer->dni.'",   
                                        "shippingAddress": {
                                            "street1": "",
                                            "street2":"",    
                                            "city": "",
                                            "state": "",
                                            "country":"'.$context->country->iso_code.'",
                                            "postalCode": "",
                                            "phone": ""
                                        }
                                    },      
                                    "shippingAddress":{
                                        "street1":"",
                                        "street2":"",
                                        "city":"",
                                        "state":"",
                                        "country":"'.$context->country->iso_code.'",
                                        "postalCode":"",
                                        "phone":""
                                    }
                                },
                                "payer":{
                                    "fullName":"'.$customer->firstname.' '.$customer->lastname.'",
                                    "emailAddress":"'. $params[5]['buyerEmail'].'",
                                    "contactPhone":"'.$address->phone.'",
                                    "dniNumber":"'.$customer->dni.'",
                                    "billingAddress":{
                                        "street1":"",
                                        "street2":"",
                                        "city":"",
                                        "state":"",
                                        "country":"'.$context->country->iso_code.'",
                                       "postalCode":"",
                                       "phone":""
                                    }      
                                },
                                "creditCard":{
                                    "number":"' . $args['numerot'] . '",
                                    "securityCode":"' . $args['codigot'] . '",
                                    "expirationDate":"' . $args['date'] . '",
                                    "name":"';
                                    $data .= $args['nombre'];
                                    $data.='"
                                },
                                "extraParameters":{
                                    "INSTALLMENTS_NUMBER":'.$args['cuotas'].'
                                },
                                "type":"AUTHORIZATION_AND_CAPTURE",
                                "paymentMethod":"' . $paymentMethod . '",
                                "paymentCountry":"';
                                $data .= $context->country->iso_code;
                                $data.='",
                                "deviceSessionId": "'.$_deviceSessionId.'",
                                "ipAddress": "'.$_SERVER['REMOTE_ADDR'].'",
                                "userAgent": "'.$_SERVER['HTTP_USER_AGENT'].'",
                                "cookie": "'.md5($context->cookie->timestamp).'"  
                            },
                            "test":';
                            if ($conf->isTest()) {
                                $data .= 'true';
                            } else {
                                $data .= 'false';
                            }
                            $data .= '
                        }';
                break;
            case 'PSE':
                $data = '{
                            "test":false,
                            "language":"es",
                            "command":"SUBMIT_TRANSACTION",
                            "merchant":{
                                "apiKey":"' . $keysPayu['apiKey'] . '",
                                "apiLogin":"' . $keysPayu['apiLogin'] . '"
                            },
                            "transaction":{
                                "order":{
                                    "accountId":"' . $keysPayu['accountId'] . '",
                                    "referenceCode":"' . $params[2]['referenceCode'] . '_'.$intentos.'",
                                    "description":"' . $description . '",
                                    "language":"' . $params[10]['lng'] . '",
                                    "notifyUrl":"' . $conf->urlv() . '",
                                    "signature":"' . $conf->sing($params[2]['referenceCode'] . '_'.$intentos.'~' . $params[4]['amount'] . '~'.$currency).'",
                                    "buyer": {
                                        "fullName": "'.$customer->firstname.' '.$customer->lastname.'",
                                        "contactPhone": "'.$address->phone.'",
                                        "emailAddress":"'. $params[5]['buyerEmail'].'",
                                        "dniNumber":"'.$customer->dni.'",   
                                        "shippingAddress": {
                                            "street1": "",
                                            "street2":"",    
                                            "city": "",
                                            "state": "",
                                            "country":"'.$context->country->iso_code.'",
                                            "postalCode": "",
                                            "phone": ""
                                        }
                                    },   
                                    "additionalValues":{
                                        "TX_VALUE":{
                                            "value":' . $params[4]['amount'] . ',
                                            "currency":"'.$currency.'"
                                        }
                                    }
                                },
                                "payer":{
                                    "fullName":"'.$customer->firstname.' '.$customer->lastname.'",
                                    "emailAddress":"'. $params[5]['buyerEmail'].'",
                                    "contactPhone":"'.$address->phone.'",
                                    "dniNumber":"'.$customer->dni.'",
                                    "billingAddress":{
                                        "street1":"",
                                        "street2":"",
                                        "city":"",
                                        "state":"",
                                        "country":"'.$context->country->iso_code.'",
                                       "postalCode":"",
                                       "phone":""
                                    }      
                                },
                                "ipAddress": "'.$_SERVER['REMOTE_ADDR'].'",
                                "userAgent": "'.$_SERVER['HTTP_USER_AGENT'].'",
                                "cookie": "'.md5($context->cookie->timestamp).'",
                                "type":"AUTHORIZATION_AND_CAPTURE",
                                "paymentMethod":"PSE",
                                "extraParameters":{
                                    "PSE_REFERENCE1":"' . $_SERVER['REMOTE_ADDR'] . '",
                                    "FINANCIAL_INSTITUTION_CODE":"' . $args['pse_bank'] . '",
                                    "FINANCIAL_INSTITUTION_NAME":"' . $args['name_bank'] . '",
                                    "USER_TYPE":"' . $args['pse_tipoCliente'] . '",
                                    "PSE_REFERENCE2":"' . $args['pse_docType'] . '",
                                    "PSE_REFERENCE3":"' . $args['pse_docNumber'] . '"
                                }
                            }
                        }';
                break;
        }
                    
        //error_log("\n\n".print_r($data, true),3,"/tmp/error.log");

        $response_Payu = $conf->sendJson($data);

        $subs = substr($args['numerot'], 0, (strlen($args['numerot']) - 4));
        $nueva = '';
        for ($i = 0; $i <= strlen($subs); $i++) {
            $nueva = $nueva . '*';
        }
        $data = str_replace('"number":"' . $subs, '"number":"' . $nueva, $data);
        $data = str_replace('"securityCode":"' . $args['codigot'], '"securityCode":"' . '****', $data);

        return self::validatePayu($response_Payu, $data, $args);
    }

    /**
    * Valida la transaccion enviada a Payu y retorna en estado para orden
    */
    public static function validatePayu($response, $data, $args)
    {
        $conf = new ConfPayu();
        if (!empty($response['transactionResponse']['state']) && ($response['transactionResponse']['state'] === 'PENDING' || $response['transactionResponse']['state'] === 'APPROVED')){
            $conf->pago_payu($args['id_order'], $args['id_customer'], $data, $response, $args['method'], $response['code'], $args['id_cart'], $args['id_address_invoice']);	
            if($response['transactionResponse']['state'] == 'PENDING')	
                return (int) Configuration::get('PAYU_OS_PENDING');
            if($response['transactionResponse']['state'] == 'APPROVED')
                return (int) Configuration::get('PS_OS_PAYMENT'); 
        } else {
            $conf->error_payu($args['id_order'], $args['id_customer'], $data, $response, $args['method'], $response['code'], $args['id_cart'], $args['id_address_invoice']);
            return (int) Configuration::get('PS_OS_ERROR');
        }
    }
    
    /**
    * Retorna franquicia de la tarjeta de credito
    */
    public static function getFranquicia($cart_number, $pasarela){
        $arraypaymentMethod =  array("VISA"=>'VISA','DISCOVER'=>'DINERS','AMERICAN EXPRESS'=>'AMEX','MASTERCARD'=>'MASTERCARD');
        $arraypaymentMethod2 =  array("VISA"=>'VISA','DISCOVER'=>'DINERS','AMERICAN EXPRESS'=>'AmEx','MASTERCARD'=>'MasterCard', 'DinersClub'=>'DinersClub','UnionPay'=>'UnionPay');
        $CCV = new CreditCardValidator();
        $CCV->Validate($cart_number);
        $key = $CCV->GetCardName($CCV->GetCardInfo()['type']);

        if($CCV->GetCardInfo()['status'] == 'invalid'){
            return json_encode(array('ERROR'=>'El numero de la tarjeta no es valido.'));
        }

        switch ($pasarela) {
            case 'payulatam':
            return (array_key_exists(strtoupper($key), $arraypaymentMethod)) ? $arraypaymentMethod[strtoupper($key)] : 'N/A'; 
            break;
            default:
            return (array_key_exists(strtoupper($key), $arraypaymentMethod2[strtoupper($key)])) ? $arraypaymentMethod2[strtoupper($key)] : 'N/A'; 
            break;
        }
    }
    
    /**
    * Retorna variables extras payulatam
    */
    public static function get_extra_vars_payu($id_cart,$method,$secure_key="",$order=""){
        $payulatam = new PayULatam();
        $extra_vars =  array();

        $sql = "SELECT json_response 
                FROM "._DB_PREFIX_."pagos_payu 
                WHERE id_cart =".(int) $id_cart;

        if ($rs = Db::getInstance()->getValue($sql)) {
            $response = json_decode(stripslashes($rs),TRUE);

            if (isset($response['transactionResponse']['extraParameters']['BAR_CODE'])) {
                $extra_vars =  array('method'=>$method,
                                     'cod_pago'=>$response['transactionResponse']['extraParameters']['REFERENCE'],
                                     'fechaex'=> date('d/m/Y', substr($response['transactionResponse']['extraParameters']['EXPIRATION_DATE'], 0, -3)),
                                     'bar_code'=>$response['transactionResponse']['extraParameters']['BAR_CODE']);
            }elseif (isset($response['transactionResponse']['extraParameters']['URL_PAYMENT_RECEIPT_HTML'])) {
                $extra_vars =  array('method'=>$method,
                                     'cod_pago'=>$response['transactionResponse']['extraParameters']['REFERENCE'],
                                     'fechaex'=> date('d/m/Y', substr($response['transactionResponse']['extraParameters']['EXPIRATION_DATE'], 0, -3)));
            }

            $url_pay_pse = $response['transactionResponse']['extraParameters']['BANK_URL'];
            $extra_vars['url_pay_pse'] = $url_pay_pse;
        }

        return $extra_vars;
    }

    /**
    * Retorna una cadena aleatoria
    */
    public static function randString ($length = 32)
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
    
    /**
    * Retorna lista de bancos disponibles
    */
    public static function get_bank_pse()
    {
        $conf = new ConfPayu();
        $keysPayu = $conf->keys();
        
        $js_send = '{
                        "language":"es",
                        "command":"GET_BANKS_LIST",
                        "merchant":{
                            "apiLogin":"'.$keysPayu['apiLogin'].'",
                            "apiKey":"'.$keysPayu['apiKey'].'"
                        },
                        "test":false,
                        "bankListInformation":{
                            "paymentMethod":"PSE",
                            "paymentCountry":"CO"
                        }
                    }';

        $xml_send = '<request>
                        <language>es</language>
                        <command>GET_BANKS_LIST</command>
                        <merchant>
                            <apiLogin>'.$keysPayu['apiLogin'].'</apiLogin>
                            <apiKey>'.$keysPayu['apiKey'].'</apiKey>
                        </merchant>
                        <isTest></isTest>
                        <bankListInformation>
                            <paymentMethod>PSE</paymentMethod>
                            <paymentCountry>CO</paymentCountry>
                        </bankListInformation>
                    </request>';
        
        $bancos = array();

//        $PayuBanks = $conf->sendXml($js_send)['bankListResponse']['banks'][0]['bank'];
        $response = $conf->sendXml($xml_send);
        
        $array_baks = NULL;
        if(!empty($response['paymentResponse']['error'])){
          return array('error' => 1, 'description' => $response['paymentResponse']['error']);
        }
        else {
          $PayuBanks = $response['bankListResponse']['banks'][0]['bank'];
          foreach ($PayuBanks as $row){
            $array_baks[] = array('value' => $row['pseCode'], 'name' => $row['description']);	
          }
        }
            
        return $array_baks;
    }
}
