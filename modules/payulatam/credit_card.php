<?php

$useSSL = true;
require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/payulatam.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/config.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/paymentws.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/creditcards.class.php');

class PayuCreditCard extends PayUControllerWS {

    public $ssl = true;

    public function setMedia() {
        parent::setMedia();
    }

    public function process() {
        if (empty($this->context->cart->id)) {
            Tools::redirect('/');
        }

        $membership = false;
        $productsCart = $this->context->cart->getProducts();
        $description = "(Cliente: ".$this->context->customer->username."). Productos: ";
        foreach ($productsCart as $product) {
            $description .= "[".$product['name'].",".$product['cart_quantity']."] ";
            if ( substr($product['reference'],0,5) == "MFLUZ" ) {
                $membership = true;
            }
        }

        //echo '<pre>'; print_r($_POST); die();
        if ((isset($_POST['numerot']) && !empty($_POST['numerot']) && strlen($_POST['numerot']) > 13 && strlen((int) $_POST['numerot']) < 17
            && isset($_POST['nombre']) && !empty($_POST['nombre']) && isset($_POST['codigot']) && !empty($_POST['codigot']) && 
            isset($_POST['Month']) && !empty($_POST['Month']) && isset($_POST['year']) && !empty($_POST['year'])  && isset($_POST['cuotas']) && !empty($_POST['cuotas'])) 
            || (isset($_POST['token_id']) && !empty($_POST['token_id']) && isset($_POST['openpay_device_session_id']) && !empty($_POST['openpay_device_session_id']) ) )
        {
            parent::process();

            // url para re intentos de pago
            $url_reintento=$_SERVER['HTTP_REFERER'];
            if(!strpos($_SERVER['HTTP_REFERER'], 'step=3')) {
                if(!strpos($_SERVER['HTTP_REFERER'], '?')) {
                    $url_reintento.='?step=3';
                } else {
                    $url_reintento.='&step=3';
                }
            }
            if ( $membership ) { $url_reintento = $_SERVER['HTTP_REFERER']; }

            // vaciar errores en el intento de pago anterior  
            if(isset($this->context->cookie->{'error_pay'})){
              unset($this->context->cookie->{'error_pay'});
            }

            $params = $this->initParams();
            // se optinen los datos del formulario de pago    
            $post = array('nombre'  =>  (Tools::getValue('nombre')) ? Tools::getValue('nombre') : Tools::getValue('holder'),
                          'numerot' =>  (Tools::getValue('numerot')) ? Tools::getValue('numerot') : Tools::getValue('card'),
                          'codigot' =>  (Tools::getValue('codigot')) ? Tools::getValue('codigot') : Tools::getValue('cvv'),
                          'date'    =>  Tools::getValue('year').'/'.Tools::getValue('Month'),
                          'cuotas'  =>  Tools::getValue('cuotas'),
                          'Month'   =>  Tools::getValue('Month'),
                          'year'    =>  Tools::getValue('Year')
                        ); 

            $conf = new ConfPayu();
      
            if($conf->exist_cart_in_pagos($this->context->cart->id)) {
                if(isset($this->context->cookie->{'url_confirmation'})) {
                    Tools::redirectLink(json_decode($this->context->cookie->{'url_confirmation'}));
                }
                Tools::redirectLink('/');
                exit();
            }

            if($this->getFranquicia($post['numerot'], 'payulatam') == 'AMEX' &&  strlen($post['codigot'])  != 4) {
                $this->context->cookie->{'error_pay'} = json_encode($arrayName = array('code_cvc' => 'El código de verificación no corresponde con la franquicia de su tarjeta', ));
                Tools::redirectLink($url_reintento);
                exit();
            } elseif ($this->getFranquicia($post['numerot'], 'payulatam') != 'AMEX' && strlen($post['codigot']) != 3) {
                $this->context->cookie->{'error_pay'} = json_encode($arrayName = array('code_cvc' => 'El código de verificación no corresponde con la franquicia de su tarjeta', ));
                Tools::redirectLink($url_reintento);
                exit();
            }
    
            $keysPayu = $conf->keys();
            
            if ( $params[4]['amount'] > 30000 ) {
                $keysPayu['accountId'] = (int)Configuration::get('PAYU_LATAM_ACCOUNT_ID');
            } else {
                $keysPayu['accountId'] = (int)Configuration::get('PAYU_LATAM_ACCOUNT_ID_2');
            }

            $address = new Address($this->context->cart->id_address_delivery); 
            $billingAddress = new Address($this->context->cart->id_address_invoice); 
            $id_order = 0;
            
            $customer = new Customer((int) $this->context->cart->id_customer);
            $id_cart = $this->context->cart->id;
            $id_address = $this->context->cart->id_address_delivery;
            $addressdni = $customer->getAddresses(0);
            $dni = $addressdni[0]['dni'];
            if ( $addressdni[0]['checkdigit'] != "" ) {
                $dni .= "-".$addressdni[0]['checkdigit'];
            }
            $reference_code = $customer->id . '_' . $id_cart . '_' . $id_order . '_' . $id_address;
            $_deviceSessionId = NULL;

            if (isset($this->context->cookie->deviceSessionId) && !empty($this->context->cookie->deviceSessionId) && strlen($this->context->cookie->deviceSessionId) === 32) {
                $_deviceSessionId = $this->context->cookie->deviceSessionId;
            } elseif (isset($_POST['deviceSessionId']) && !empty($_POST['deviceSessionId']) && strlen($_POST['deviceSessionId']) === 32) {
                $_deviceSessionId = $_POST['deviceSessionId'];
            } else {
                $_deviceSessionId = md5($this->context->cookie->timestamp);
            }

            $intentos = $conf->count_pay_cart($id_cart);
            $paymentMethod = $this->getFranquicia($post['numerot'], 'payulatam');
            $currency='';

            if($conf->isTest()) {
                $currency='USD';
            } else {
                $currency=$params[9]['currency'];
            }
    
            if ( $params[5]['buyerEmail'] == "" || empty($params[5]['buyerEmail']) ) {
                $params[5]['buyerEmail'] = $customer->email;
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
                                    },
                                      "TX_TAX": {
                                        "value":' . 0 . ',
                                        "currency":"'.$currency.'"
                                    },
                                },
                                "buyer": {
                                    "fullName": "'.$customer->firstname.' '.$customer->lastname.'",
                                    "contactPhone": "'.$address->phone.'",
                                    "emailAddress":"'. $params[5]['buyerEmail'].'",
                                    "dniNumber":"'.$dni.'",   
                                    "shippingAddress": {
                                        "street1": "",
                                        "street2":"",    
                                        "city": "",
                                        "state": "",
                                        "country":"'.$this->context->country->iso_code.'",
                                        "postalCode": "",
                                        "phone": ""
                                    }
                                },      
                                "shippingAddress":{
                                    "street1":"",
                                    "street2":"",
                                    "city":"",
                                    "state":"",
                                    "country":"'.$this->context->country->iso_code.'",
                                    "postalCode":"",
                                    "phone":""
                                }
                            },
                            "payer":{
                                "fullName":"'.$customer->firstname.' '.$customer->lastname.'",
                                "emailAddress":"'. $params[5]['buyerEmail'].'",
                                "contactPhone":"'.$billingAddress->phone.'",
                                "dniNumber":"'.$dni.'",
                                "billingAddress":{
                                    "street1":"",
                                    "street2":"",
                                    "city":"",
                                    "state":"",
                                    "country":"'.$this->context->country->iso_code.'",
                                   "postalCode":"",
                                   "phone":""
                                }      
                            },
                            "creditCard":{
                                "number":"' . $post['numerot'] . '",
                                "securityCode":"' . $post['codigot'] . '",
                                "expirationDate":"' . $post['date'] . '",
                                "name":"';
                                if($conf->isTest()){
                                    $data.='APPROVED';
                                }else{
                                    $data.=$post['nombre'];
                                }
                                $data.='"
                            },
                            "extraParameters":{
                                "INSTALLMENTS_NUMBER":'.$post['cuotas'].'
                            },
                            "type":"AUTHORIZATION_AND_CAPTURE",
                            "paymentMethod":"' . $paymentMethod . '",
                            "paymentCountry":"';
                                if($conf->isTest()){
                                    $data.='PA';
                                }else{
                                    $data.=$this->context->country->iso_code;
                                }
                            $data.='",
                            "deviceSessionId": "'.$_deviceSessionId.'",
                            "ipAddress": "'.$_SERVER['REMOTE_ADDR'].'",
                            "userAgent": "'.$_SERVER['HTTP_USER_AGENT'].'",
                            "cookie": "'.md5($this->context->cookie->timestamp).'"  
                        },
                        "test":';
                        if($conf->isTest()){
                            $data.='true';
                        }else{
                            $data.='false';
                        }
                        $data.='          
                    }';

            //die($data);

            $response = $conf->sendJson($data);
            //echo "<pre>"; print_r($response); die();

            $subs = substr($post['numerot'], 0, (strlen($post['numerot']) - 4));
            $nueva = '';

            for ($i = 0; $i <= strlen($subs); $i++) {
                $nueva = $nueva . '*';
            }

            $data = str_replace('"number":"' . $subs, '"number":"' . $nueva, $data);
            $data = str_replace('"securityCode":"' . $post['codigot'], '"securityCode":"' . '****', $data);

            // colector Errores Payu
            $error_pay = array();

            if ($response['code'] === 'ERROR') {
                $conf->error_payu($id_order, $customer->id, $data, $response, 'Tarjeta_credito', $response['transactionResponse']['state'], $this->context->cart->id, $id_address); 
                $this->deleteAccountFail($membership);
                $error_pay[]=$response;
            }
            elseif ($response['code'] === 'SUCCESS' && ( $response['transactionResponse']['state'] === 'PENDING' || $response['transactionResponse']['state'] === 'APPROVED' ) && $response['transactionResponse']['responseMessage'] != 'ERROR_CONVERTING_TRANSACTION_AMOUNTS')
            {
                $conf->pago_payu($id_order, $customer->id, $data, $response, 'Tarjeta_credito', $response['transactionResponse']['state'], $this->context->cart->id, $id_address);                       
                if($response['transactionResponse']['state'] === 'APPROVED') {
                    $this->createPendingOrder(array(), 'Tarjeta_credito', utf8_encode($conf->getMessagePayu($response['transactionResponse']['responseCode'])), 'PS_OS_PAYMENT');
                } else{
                    $this->createPendingOrder(array(), 'Tarjeta_credito', utf8_encode($conf->getMessagePayu($response['transactionResponse']['responseCode'])), 'PAYU_OS_PENDING');  
                }
                $order = $conf->get_order($id_cart);
                $id_order = $order['id_order'];
                $payulatam = new PayULatam();
                $url_confirmation = __PS_BASE_URI__ . 'order-confirmation.php?key=' . $customer->secure_key . '&id_cart=' . (int) $this->context->cart->id . '&id_module='.(int)$payulatam->id.'&id_order=' . (int) $order['id_order'];
                $this->context->cookie->{'url_confirmation'} = json_encode($url_confirmation);
                
                if ( $response['transactionResponse']['state'] !== 'PENDING' ) {
                    $this->createAccountSuccess($membership, $customer->id);
                }
                if($response['transactionResponse']['state'] === 'APPROVED') {
                    $qstate="UPDATE "._DB_PREFIX_."rewards SET id_reward_state= 2 WHERE id_customer=".(int)$customer->id." AND id_order=".(int) $order['id_order'];
                    Db::getInstance()->execute($qstate);
                }
                Tools::redirectLink($url_confirmation);
                exit();
            } else {
                $conf->error_payu($id_order, $customer->id, $data, $response, 'Tarjeta_credito', $response['transactionResponse']['state'], $this->context->cart->id, $id_address);
                $this->deleteAccountFail($membership);
                $error_pay[]=array('ERROR' => utf8_encode($conf->getMessagePayu($response['transactionResponse']['responseCode'])));
            }

            $this->context->cookie->{'error_pay'} = json_encode($error_pay);
            Tools::redirectLink($url_reintento);
            exit();
        } else {
            $this->context->cookie->{'error_pay'} = json_encode(array('ERROR'=>'Valida tus datos he intenta de nuevo.'));
            $this->deleteAccountFail($membership);
            Tools::redirectLink($url_reintento); 
            exit();   
        }
    }

    public function displayContent() {
        parent::displayContent();
        self::$smarty->display(_PS_MODULE_DIR_ . 'payulatam/tpl/success.tpl');
    }
                    
    /**
    * Retorna el la franquicia a la que pertenece un numero de TC
    */
    public function getFranquicia($cart_number, $pasarela){
        require_once(_PS_MODULE_DIR_ . 'payulatam/creditcards.class.php');

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
}

$payuCreditCard = new  PayuCreditCard();
$payuCreditCard->run();
