<?php

$useSSL = true;
require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/payulatam.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/config.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/paymentws.php');


class PayuPse extends PayUControllerWS{

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

        parent::process();

        // url para re intentos de pago
        $url_reintento=$_SERVER['HTTP_REFERER'];
        if(!strpos($_SERVER['HTTP_REFERER'], 'step=3')) {
            if (!strpos($_SERVER['HTTP_REFERER'], '?')) {
                $url_reintento.='?step=3';
            } else {
                $url_reintento.='&step=3';
            }
        }
        if ( $membership ) { $url_reintento = $_SERVER['HTTP_REFERER']; }

        // vaciar errores en el intento de pago anterior  
        if (isset($this->context->cookie->{'error_pay'})) {
            unset($this->context->cookie->{'error_pay'});
        }

        //echo '<pre>'; print_r($_POST); die();
        if (isset($_POST['pse_bank']) && isset($_POST['name_bank']) && !empty($_POST['pse_bank']))
        {
            // reglas de carrito para bines
            $payulatam = new PayULatam(); 
            $params = $this->initParams();
            $conf = new ConfPayu();
            $keysPayu = $conf->keys();
            
            if ( $params[4]['amount'] > 30000 ) {
                $keysPayu['pse-CO'] = (int)Configuration::get('PAYU_LATAM_ACCOUNT_ID');
            } else {
                $keysPayu['pse-CO'] = (int)Configuration::get('PAYU_LATAM_ACCOUNT_ID_2');
            }
            
            $customer = new Customer((int) $this->context->cart->id_customer);
            $id_cart = $this->context->cart->id;
            $id_address = $this->context->cart->id_address_delivery;
            $id_order = 0;

            $varRandn = $conf->randString();
            $varRandc = $conf->randString();
            setcookie($varRandn, $varRandc, time() + 900);


            $browser = array('ipAddress' => $_SERVER['SERVER_ADDR'],
                             'userAgent' => $_SERVER['HTTP_USER_AGENT']
                            );

            $addressdni = $customer->getAddresses(0);
            $billin_dni = $addressdni[0]['dni'];
            if ( $addressdni[0]['checkdigit'] != "" ) {
                $billin_dni .= "-".$addressdni[0]['checkdigit'];
            }
            $billingAddress = new Address($addressdni[0]['id_address']);
            $intentos = $conf->count_pay_cart($id_cart);

            $currency='';
            if($conf->isTest()){
                $currency='USD';
            } else {
                $currency=$params[9]['currency'];
            }

            $url  = '';
            if (Configuration::get('PS_SSL_ENABLED') || (!empty($_SERVER['HTTPS']) && Tools::strtolower($_SERVER['HTTPS']) != 'off')) {
                if (method_exists('Tools', 'getShopDomainSsl')) {
                    $url = 'https://'.Tools::getShopDomainSsl().__PS_BASE_URI__.'modules/'.$payulatam->name.'/';
                } else {
                    $url = 'https://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/'.$payulatam->name.'/';
                }
            } else {
                $url = 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/'.$payulatam->name.'/';
            }

            $reference_code = $params[2]['referenceCode'] . '_'.$intentos;
            $token_orden = md5($reference_code);

            if ( $params[5]['buyerEmail'] == "" || empty($params[5]['buyerEmail']) ) {
                $params[5]['buyerEmail'] = $customer->email;
            }
            
            $data = '{
                        "test":false,
                        "language":"es",
                        "command":"SUBMIT_TRANSACTION",
                        "merchant":{
                            "apiLogin":"' . $keysPayu['apiLogin'] . '",
                            "apiKey":"' . $keysPayu['apiKey'] . '"
                        },
                        "transaction":{
                            "order":{
                                "accountId":"' . $keysPayu['pse-CO'] . '",
                                "referenceCode":"' .$reference_code.'",
                                "description":"' . $description . '",
                                "language":"es",
                                "notifyUrl":"' . $conf->urlv() . '",
                                "signature":"' . $conf->sing($params[2]['referenceCode'] . '_'.$intentos.'~' . $params[4]['amount'] . '~'.$currency).'",
                                "buyer":{
                                    "fullName":"' . $this->context->customer->firstname . ' ' . $this->context->customer->lastname . '",
                                    "emailAddress":"' . $params[5]['buyerEmail'] . '",
                                    "dniNumber":"'.$billin_dni.'",
                                    "shippingAddress":{
                                        "street1":"",
                                        "city":"",
                                        "state":"",
                                        "country":"'.$this->context->country->iso_code.'",
                                        "phone":""
                                    }
                                },
                                "additionalValues":{
                                    "TX_VALUE":{
                                        "value":' . $params[4]['amount'] . ',
                                        "currency":"' . $currency . '"
                                    },
                                    "TX_TAX": {
                                       "value": 0,
                                       "currency":"' . $currency . '"
                                    },
                                    "TX_TAX_RETURN_BASE": {
                                       "value": 0,
                                       "currency":"' . $currency . '"
                                    }
				}
                            },
                            "payer":{
                                "fullName":"' . $this->context->customer->firstname . ' ' . $this->context->customer->lastname . '",
                                "emailAddress":"' . $params[5]['buyerEmail'] . '",
                                "dniNumber":"' . $_POST['pse_docNumber'] . '",
                                "contactPhone":"'.$billingAddress->phone.'"
                            },
                            "ipAddress":"' . $_SERVER['REMOTE_ADDR'] . '",
                            "cookie":"' . $varRandn . '",
                            "userAgent":"' . $browser['userAgent'] . '",
                            "type":"AUTHORIZATION_AND_CAPTURE",
                            "paymentMethod":"PSE",
                            "extraParameters":{
                                "PSE_REFERENCE1":"' . $_SERVER['REMOTE_ADDR'] . '",
                                "FINANCIAL_INSTITUTION_CODE":"' . $_POST['pse_bank'] . '",
                                "FINANCIAL_INSTITUTION_NAME":"' . $_POST['name_bank'] . '",
                                "USER_TYPE":"' . $_POST['pse_tipoCliente'] . '",
                                "PSE_REFERENCE2":"' . $_POST['pse_docType'] . '",
                                "PSE_REFERENCE3":"' . $_POST['pse_docNumber'] . '",
                                "RESPONSE_URL": "'.$url.'url_confirm.php?token='.$token_orden.'"
                            }
                        }
                    }';

            //die($data);

            $response = $conf->sendJson($data);
            //echo "<pre>"; print_r($response); die();

            if ($response['code'] === 'ERROR') {
                $conf->error_payu($id_order, $customer->id, $data, $response, 'PSE', $response['transactionResponse']['state'], $this->context->cart->id, $id_address); 
                $this->deleteAccountFail($membership);
                $error_pay[]=$response;
            }
            elseif ($response['code'] === 'SUCCESS' && $response['transactionResponse']['state'] === 'PENDING' && $response['transactionResponse']['responseMessage'] != 'ERROR_CONVERTING_TRANSACTION_AMOUNTS')
            {
                $this->createPendingOrder(array(), 'PSE', utf8_encode($conf->getMessagePayu($response['transactionResponse']['responseCode'])), 'PAYU_OS_PENDING');
                $order = $conf->get_order($id_cart);
                $id_order = $order['id_order'];    
                $conf->pago_payu($id_order, $customer->id, $data, $response, 'Pse',$response['code'], $id_cart, $id_address);
                $url_base64 = strtr(base64_encode($response['transactionResponse']['extraParameters']['BANK_URL']), '+/=', '-_,');
                $string_send = __PS_BASE_URI__ . 'order-confirmation.php?key=' . $customer->secure_key . '&id_cart=' . (int) $id_cart . '&id_module='.(int)$payulatam->id.'&id_order=' . (int) $order['id_order'] . '&bankdest2=' . $url_base64;
                $conf->url_confirm_payu($token_orden,__PS_BASE_URI__ . 'order-confirmation.php?key=' . $customer->secure_key . '&id_cart=' . (int) $id_cart . '&id_module='.(int)$payulatam->id.'&id_order=' . (int) $order['id_order']);

                if ( $response['transactionResponse']['state'] !== 'PENDING' ) {
                    $this->createAccountSuccess($membership, $customer->id);
                }

                Tools::redirectLink($string_send);
                exit();
            } else {
                $conf->error_payu($id_order, $customer->id, $data, $response, 'PSE', $response['transactionResponse']['state'], $this->context->cart->id, $id_address);
                $this->deleteAccountFail($membership);
                $error_pay[]=array('ERROR'=>utf8_encode($conf->getMessagePayu($response['transactionResponse']['responseCode']))); 
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
}

$payuPse = new PayuPse();
$payuPse->run();
