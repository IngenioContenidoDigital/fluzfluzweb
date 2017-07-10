<?php
require_once(_PS_MODULE_DIR_ . 'payulatam/config.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/payulatam.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/paymentws.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/creditcards.class.php');

class PasarelaPagoCore extends PayUControllerWS {

    /**
    * Envia la solicitud de pago a la pasarela asociada al medio de pago
    */
    public static function payOrder($args) {
        switch ( $args['option_pay'] ) {
            case 'payulatam':
                return self::EnviarPagoPayu($args); 
            break;
        }
    }

    /**
    * Retorna estructura Json 
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
            $conf->pago_payu($args['id_order'], $args['id_customer'], $data, $response, $args['option_pay'], $response['code'], $args['id_cart'], $args['id_address_invoice']);	
            if($response['transactionResponse']['state'] == 'PENDING')	
                return (int) Configuration::get('PAYU_OS_PENDING');
            if($response['transactionResponse']['state'] == 'APPROVED')
                return (int) Configuration::get('PS_OS_PAYMENT'); 
        } else {
            $conf->error_payu($args['id_order'], $args['id_customer'], $data, $response, $args['option_pay'], $response['code'], $args['id_cart'], $args['id_address_invoice']);
            return (int) Configuration::get('PS_OS_ERROR');
        }
    }
    
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
    public static function get_extra_vars_payu($id_cart,$method){
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
        }
        return $extra_vars;
    }
    
    // función que genera una cadena aleatoria
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
}
