<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once(_PS_MODULE_DIR_ . 'bankwire/bankwire.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/allinone_rewards.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsSponsorshipModel.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/controllers/front/sponsorship.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsSponsorshipCodeModel.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsModel.php');

class AuthController extends AuthControllerCore
{
    public function setMedia()
    {
        FrontController::setMedia();
        if (!$this->useMobileTheme()) {
            $this->addCSS(_THEME_CSS_DIR_.'authentication.css');
        }
        $this->addJqueryPlugin('typewatch');
        $this->addJS(array(
            _THEME_JS_DIR_.'tools/vatManagement.js',
            //_THEME_JS_DIR_.'tools/statesManagement.js',
            _THEME_JS_DIR_.'authentication.js',
            _PS_JS_DIR_.'jquery/plugins/jquery.creditCardValidator.js',
            _PS_JS_DIR_.'validate.js'
        ));
    }
    
    public function initContent()
    {
        FrontController::initContent();
        $this->context->smarty->assign('genders', Gender::getGenders());
        $this->assignDate();
        $this->assignCountries();
        $newsletter = Configuration::get('PS_CUSTOMER_NWSL') || (Module::isInstalled('blocknewsletter') && Module::getInstanceByName('blocknewsletter')->active);
        $this->context->smarty->assign('newsletter', $newsletter);
        $this->context->smarty->assign('optin', (bool)Configuration::get('PS_CUSTOMER_OPTIN'));
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]".'/es/inicio-sesion?back=my-account';
        $this->context->smarty->assign('url',$url);
        
        $back = Tools::getValue('back');
        $key = Tools::safeOutput(Tools::getValue('key'));
        $login_guest = Tools::safeOutput(Tools::getValue('create_account'));
        
        if($login_guest==2){
           $this->context->smarty->assign('login_guest',$login_guest);
        }

        if (!empty($key)) {
            $back .= (strpos($back, '?') !== false ? '&' : '?').'key='.$key;
        }
        if ($back == Tools::secureReferrer(Tools::getValue('back'))) {
            $this->context->smarty->assign('back', html_entity_decode($back));
        } else {
            $this->context->smarty->assign('back', Tools::safeOutput($back));
        }
        if (Tools::getValue('display_guest_checkout')) {
            if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
                $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
            } else {
                $countries = Country::getCountries($this->context->language->id, true, true);
            }
            $this->context->smarty->assign(array(
                    'inOrderProcess' => true,
                    'PS_GUEST_CHECKOUT_ENABLED' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
                    'PS_REGISTRATION_PROCESS_TYPE' => Configuration::get('PS_REGISTRATION_PROCESS_TYPE'),
                    'sl_country' => (int)$this->id_country,
                    'countries' => $countries
                ));
        }
        if (Tools::getValue('create_account')) {
            $this->context->smarty->assign('email_create', 1);
        }
        if (Tools::getValue('multi-shipping') == 1) {
            $this->context->smarty->assign('multi_shipping', true);
        } else {
            $this->context->smarty->assign('multi_shipping', false);
        }
        $this->context->smarty->assign('field_required', $this->context->customer->validateFieldsRequiredDatabase());
        $this->assignAddressFormat();
        // Call a hook to display more information on form
        $this->context->smarty->assign(array(
            'HOOK_CREATE_ACCOUNT_FORM' => Hook::exec('displayCustomerAccountForm'),
            'HOOK_CREATE_ACCOUNT_TOP' => Hook::exec('displayCustomerAccountFormTop')
        ));

        if ( isset($_POST['email']) && $_POST['email'] != "" && !empty($_POST['email']) ) {
            setcookie("datamailemail", $_POST['email'], time() + (86400), "/");
            setcookie("datamailfirstname", $_POST['customer_firstname'], time() + (86400), "/");
            setcookie("datamaillastname", $_POST['customer_lastname'], time() + (86400), "/");
        } else {
            $_POST['email'] = $_COOKIE["datamailemail"];
            $_POST['customer_firstname'] = $_COOKIE["datamailfirstname"];
            $_POST['customer_lastname'] = $_COOKIE["datamaillastname"];
        }

        $this->context->smarty->assign('cities', City::getCities());

        $this->context->smarty->assign('PS_BUY_MEMBERSHIP', Configuration::get('PS_BUY_MEMBERSHIP'));

        // Just set $this->template value here in case it's used by Ajax
        
        if(Tools::getValue("back") == 'business'){
            $this->setTemplate(_PS_THEME_DIR_.'authentication_business.tpl');
        }
        else{
            $this->setTemplate(_PS_THEME_DIR_.'authentication.tpl');
        }
        
        if ($this->ajax) {
            // Call a hook to display more information on form
            $this->context->smarty->assign(array(
                    'PS_REGISTRATION_PROCESS_TYPE' => Configuration::get('PS_REGISTRATION_PROCESS_TYPE'),
                    'genders' => Gender::getGenders()
                ));
            $return = array(
                'hasError' => !empty($this->errors),
                'errors' => $this->errors,
                'page' => $this->context->smarty->fetch($this->template),
                'token' => Tools::getToken(false)
            );
            $this->ajaxDie(Tools::jsonEncode($return));
        }
    }
    
    protected function assignCountries()
    {
        $this->id_country = (int)Tools::getCountry();
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $countries = Country::getCountries($this->context->language->id, false, true);
        }
        $this->context->smarty->assign(array(
                'countries' => $countries,
                'PS_REGISTRATION_PROCESS_TYPE' => Configuration::get('PS_REGISTRATION_PROCESS_TYPE'),
                'sl_country' => (int)$this->id_country,
                'vat_management' => Configuration::get('VATNUMBER_MANAGEMENT')
            ));
    }
    public function postProcess()
    {
        if (Tools::isSubmit('submitIdentity')) {
            $this->prueba();
        }
        if (Tools::isSubmit('SubmitCreate')) {
            $this->processSubmitCreate();
        }
        if (Tools::isSubmit('submitAccount') || Tools::isSubmit('submitGuestAccount')) {
            $this->processSubmitAccount();
        }
        if (Tools::isSubmit('SubmitLogin')) {
            $this->processSubmitLogin();
        }
    }
    
    protected function processSubmitLogin($validate)
    {
        Hook::exec('actionBeforeAuthentication');
        $passwd = trim(Tools::getValue('passwd'));
        $_POST['passwd'] = null;
        $email = trim(Tools::getValue('email'));
        
        setcookie('citymanufacturerfilter', '', time()+604800,'/');
        setcookie('manufacturerfilter', '', time()+604800,'/');
            
        if (empty($email)) {
            $this->errors[] = Tools::displayError('An email address required.');
        } elseif (!Validate::isEmail($email)) {
            $this->errors[] = Tools::displayError('Invalid email address.');
        } elseif (empty($passwd)) {
            $this->errors[] = Tools::displayError('Password is required.');
        } elseif (!Validate::isPasswd($passwd)) {
            $this->errors[] = Tools::displayError('Invalid password.');
        } else {
            $customer = new Customer();
            $authentication = $customer->getByEmail(trim($email), trim($passwd));
            if (isset($authentication->active) && !$authentication->active) {
                /*if($validate == 1){
                    $this->errors[] = Tools::displayError('Hemos enviado un correo para verificar tu cuenta.');
                }else{*/
                    $this->errors[] = Tools::displayError('Your account isn\'t available at this time, please contact us');
                //}
            } elseif (!$authentication || !$customer->id) {
                $this->errors[] = Tools::displayError('Authentication failed.');
            /* VALIDACION COMPRA DE LICENCIA COMPLETA
            } elseif ( !$customer->customerPurchaseLicense($email) ) {
                $this->errors[] = Tools::displayError('Por favor verifica el estado de tu afiliacion, tu proceso de registro esta incompleto. Si tienes una invitacion por favor realiza el proceso de registro nuevamente.');*/
            } else {
                $this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);
                $this->context->cookie->id_customer = (int)($customer->id);
                $this->context->cookie->customer_lastname = $customer->lastname;
                $this->context->cookie->customer_firstname = $customer->firstname;
                $this->context->cookie->logged = 1;
                $customer->logged = 1;
                $this->context->cookie->is_guest = $customer->isGuest();
                $this->context->cookie->passwd = $customer->passwd;
                $this->context->cookie->email = $customer->email;
                // Add customer to the context
                $this->context->customer = $customer;
                /*if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id)) {
                    $this->context->cart = new Cart($id_cart);
                } else {
                    $id_carrier = (int)$this->context->cart->id_carrier;
                    $this->context->cart->id_carrier = 0;
                    $this->context->cart->setDeliveryOption(null);
                    $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)($customer->id));
                    $this->context->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)($customer->id));
                }
                $this->context->cart->id_customer = (int)$customer->id;
                $this->context->cart->secure_key = $customer->secure_key;
                if ($this->ajax && isset($id_carrier) && $id_carrier && Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                    $delivery_option = array($this->context->cart->id_address_delivery => $id_carrier.',');
                    $this->context->cart->setDeliveryOption($delivery_option);
                }
                $this->context->cart->save();*/
                $this->context->cookie->id_cart = (int)$this->context->cart->id;
                $this->context->cookie->write();
                $this->context->cart->autosetProductAddress();
                Hook::exec('actionAuthentication', array('customer' => $this->context->customer));
                // Login information have changed, so we check if the cart rules still apply
                CartRule::autoRemoveFromCart($this->context);
                CartRule::autoAddToCart($this->context);
                if (!$this->ajax) {
                    $back = Tools::getValue('back','my-account');
                    if ($back == Tools::secureReferrer($back)) {
                        Tools::redirect(html_entity_decode($back));
                    }
                    Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : "my-account"));
                }
            }
        }
        if ($this->ajax) {
            $return = array(
                'hasError' => !empty($this->errors),
                'errors' => $this->errors,
                'token' => Tools::getToken(false)
            );
            $this->ajaxDie(Tools::jsonEncode($return));
        } else {
            $this->context->smarty->assign('authentification_error', $this->errors);
        }
    }
    
    protected function processSubmitAccount()
    {
        $login_guest = Tools::getValue('login_code');
        $user_key = Tools::getValue('username');
        $code_generate = Allinone_rewardsSponsorshipModuleFrontController::generateIdCodeSponsorship($user_key);
        
        if($login_guest==2)
        {
            $id_sponsor = RewardsSponsorshipCodeModel::getIdSponsorByCode(Tools::getValue('code_sponsor'));
            $code_sponsor = RewardsSponsorshipCodeModel::getCodeSponsorById($id_sponsor);
            $verified_reward = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'rewards_distribute WHERE method_add = "'.Tools::getValue('code_sponsor').'"');
            $totals = RewardsModel::getAllTotalsByCustomer((int)$verified_reward[0]['id_customer']);
            $totalAvailable = round(isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0);
            
            if ( Tools::getValue('code_sponsor') == "" || empty($id_sponsor)) {
                $this->errors[] = Tools::displayError('Codigo de Patrocino Incorrecto o No Existe. Por Favor valida tu Codigo.', false);
            }
            
            if (Validate::isEmail($email = Tools::getValue('email')) && !empty($email)) {
                $activeCustom = Db::getInstance()->getValue("SELECT active FROM "._DB_PREFIX_."customer WHERE email LIKE '%".$email."%'");
                $kickoutCustom = Db::getInstance()->getValue("SELECT COUNT(*) num FROM "._DB_PREFIX_."rewards_sponsorship_kick_out WHERE email = '".$email."'");
                if (Customer::customerExists($email) && $activeCustom == 1 && $kickoutCustom == 0 ) {
                    $this->errors[] = Tools::displayError('An account using this email address has already been registered.', false);
                }
            }

            if ( Tools::getValue('typedocument') == 0 ) {
                if ( Validate::isIdentification( Tools::getValue('gover') ) || Tools::getValue('gover') == "" ) {
                    $this->errors[] = Tools::displayError('Government Id es incorrecto');
                }
            } 
            else if ( Tools::getValue('typedocument') == 2 ){
                if ( Validate::isIdentificationCE( Tools::getValue('gover') ) || Tools::getValue('gover') == "" ) {
                    $this->errors[] = Tools::displayError('Government Id es incorrecto');
                }
            }
            else {
                if ( Tools::getValue('gover') == "" ) {
                    $this->errors[] = Tools::displayError('Government Id es incorrecto');
                }
                if ( Tools::getValue('checkdigit') == "" ) {
                    $this->errors[] = Tools::displayError('Codigo de verificacion incorrecto');
                }
            }

            if ( Tools::getValue('address1') == "" ) {
                $this->errors[] = Tools::displayError('Direccion es incorrecta');
            }

            if ( Tools::getValue('city') == "" ) {
                $this->errors[] = Tools::displayError('Ciudad es incorrecta');
            }

            $addresses_types = array('address');
            if (!Configuration::get('PS_ORDER_PROCESS_TYPE') && Configuration::get('PS_GUEST_CHECKOUT_ENABLED') && Tools::getValue('invoice_address')) {
                $addresses_types[] = 'address_invoice';
            }
            $error_phone = false;
            if (Configuration::get('PS_ONE_PHONE_AT_LEAST')) {
                if (Tools::isSubmit('submitGuestAccount') || !Tools::getValue('is_new_customer')) {
                    if (!Tools::getValue('phone') && !Tools::getValue('phone_mobile')) {
                        $error_phone = true;
                    }
                } elseif (((Configuration::get('PS_REGISTRATION_PROCESS_TYPE') && Configuration::get('PS_ORDER_PROCESS_TYPE'))
                        || (Configuration::get('PS_ORDER_PROCESS_TYPE') && !Tools::getValue('email_create'))
                        || (Configuration::get('PS_REGISTRATION_PROCESS_TYPE') && Tools::getValue('email_create')))
                        && (!Tools::getValue('phone') && !Tools::getValue('phone_mobile'))) {
                    $error_phone = true;
                }
            }

            if ($error_phone) {
                $this->errors[] = Tools::displayError('You must register at least one phone number.');
            }
            
            if (count($this->errors)) {
                if (!Tools::getValue('is_new_customer')) {
                    unset($_POST['passwd']);
                }
            }
                
            if(!empty($id_sponsor) && empty($this->errors))
            {
                $tree = RewardsSponsorshipModel::_getTree($id_sponsor);
                $sql_count_customer = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'rewards_sponsorship WHERE id_sponsor = '.$tree[0]['id']);
                array_shift($tree);
                //$count_array = count($tree);
                
                $customer = new Customer();
                $customer->firstname = Tools::getValue('customer_firstname');
                $customer->lastname = Tools::getValue('customer_lastname');
                $customer->email = Tools::getValue('email');
                $customer->passwd = Tools::encrypt(Tools::getValue('passwd'));
                $customer->dni = Tools::getValue('gover');
                $customer->phone =  Tools::getValue('phone_mobile');
                $customer->username =  Tools::getValue('username');
                $customer->id_default_group = 4;
                $customer->active = 1;
                $customer->date_kick_out = date('Y-m-d H:i:s', strtotime('+30 day', strtotime(date("Y-m-d H:i:s"))));
                $customer->date_add = date('Y-m-d H:i:s', strtotime('+0 day', strtotime(date("Y-m-d H:i:s"))));
                $customer->method_add = 'Web / Referidos';                
                //$customer->add();
                $customer->save();

                Db::getInstance()->execute('INSERT  INTO ps_customer_group (id_customer, id_group)  
                        VALUES ( '.$customer->id.' ,3)');

                $address = new Address();
                $address->id_country = 69;
                $address->dni = $customer->dni;
                $address->id_customer = $customer->id;
                $address->alias = 'Mi Direccion';
                $address->firstname = $customer->firstname;
                $address->lastname = $customer->lastname;
                $address->address1 = Tools::getValue('address1');
                $address->city = Tools::getValue('city');
                $address->phone = $customer->phone;
                $address->phone_mobile = $customer->phone;
                $address->type_document = Tools::getValue('typedocument');
                $address->active = 1;
                $address->add();

                if(empty($this->errors)){

                        if ($sql_count_customer < 2)
                        {
                            $sponsor = Db::getInstance()->getRow("SELECT c.id_customer, c.username, c.firstname, c.lastname, c.email, (2-COUNT(rs.id_sponsorship) ) sponsoships
                                        FROM " . _DB_PREFIX_ . "customer c
                                        LEFT JOIN " . _DB_PREFIX_ . "rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                                        WHERE c.id_customer =".$id_sponsor);

                            if (!empty($sponsor)) {
                                $sponsorship = new RewardsSponsorshipModel();
                                $sponsorship->id_sponsor = $sponsor['id_customer'];
                                $sponsorship->id_customer = $customer->id;
                                $sponsorship->firstname = $customer->firstname;
                                $sponsorship->lastname = $customer->lastname;
                                $sponsorship->email = $customer->email;
                                $sponsorship->channel = 1;
                                //$send = "";
                                if ($sponsorship->save()) {

                                    $this->sendConfirmationMail($customer);
                                    
                                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_sponsor, code_sponsor, code)
                                                                VALUES ('.$customer->id.', "'.$code_sponsor.'", "'.$code_generate.'")');    

                                    //AuthController::sendNotificationSponsor($customer->id);
                                   
                                    }
                                
                                $this->create_account = true;
                                //$this->context->smarty->assign('email_create', 1);
                                $sendSMS = false;
                                while ( !$sendSMS ) {
                                    $sendSMS = $customer->confirmCustomerSMS($customer->id);
                                }

                                if ( $sendSMS ) {
                                    $this->context->smarty->assign('id_customer', $customer->id);
                                    $this->context->smarty->assign('codesponsor', $code_sponsor);
                                    $this->context->smarty->assign('id_sponsor', $id_sponsor);
                                    $this->context->smarty->assign('sendSMS', true);
                                }
                                $this->updateContext($customer);
                                //$this->processSubmitLogin($validate = 1);    

                        } else {
                            $this->errors[] = 'no sponsor';
                        }
                    }
                    else{
                            $array_sponsor = array();
                            foreach ($tree as $network) {
                                $sponsor = Db::getInstance()->getRow("SELECT c.id_customer, c.username, c.firstname, c.lastname, c.email, (2-COUNT(rs.id_sponsorship) ) sponsoships
                                        FROM " . _DB_PREFIX_ . "customer c
                                        LEFT JOIN " . _DB_PREFIX_ . "rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                                        WHERE c.id_customer =" . (int) $network['id'] . "
                                        HAVING sponsoships > 0");

                                if( $sponsor != '' && $sponsor['id_customer'] && $sponsor['id_customer'] != ''){
                                    array_push($array_sponsor, $sponsor);
                                }
                            }
                            $sort_array = array_filter($array_sponsor);

                            usort($sort_array, function($a, $b) {
                                return $a['id_customer'] - $b['id_customer'];
                            });

                            $sponsor_a = reset($sort_array);
                            
                            if (!empty($sponsor_a) && ($sponsor_a['sponsoships'] > 0)) {

                                $sponsorship = new RewardsSponsorshipModel();
                                $sponsorship->id_sponsor = $sponsor_a['id_customer'];
                                $sponsorship->id_customer = $customer->id;
                                $sponsorship->firstname = $customer->firstname;
                                $sponsorship->lastname = $customer->lastname;
                                $sponsorship->email = $customer->email;
                                $sponsorship->channel = 1;

                                if ($sponsorship->save()) {

                                    $this->sendConfirmationMail($customer);
                                    
                                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_sponsor, code_sponsor, code)
                                                                VALUES ('.$customer->id.', "'.$code_sponsor.'", "'.$code_generate.'")');
                                    
                                    //AuthController::sendNotificationSponsor($customer->id);
                                    
                                    }
                                    $this->create_account = true;
                                    //$this->context->smarty->assign('email_create', 1);
                                    
                                    $sendSMS = false;
                                    while ( !$sendSMS ) {
                                        $sendSMS = $customer->confirmCustomerSMS($customer->id);
                                    }

                                    if ( $sendSMS ) {
                                        $this->context->smarty->assign('id_customer', $customer->id);
                                        $this->context->smarty->assign('codesponsor', $code_sponsor);
                                        $this->context->smarty->assign('id_sponsor', $id_sponsor);
                                        $this->context->smarty->assign('sendSMS', true);
                                    }
                                    
                                    $this->updateContext($customer);
                                    //Tools::redirect('index.php?controller=authentication');
                                    //$this->processSubmitLogin($validate = 1);
                                    //Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : "my-account"));
                            }
                            else 
                            {
                                $this->errors[] = 'no sponsor';
                            } 
                        }
                    }
                    if(!empty($verified_reward) && $verified_reward[0]['active'] == 1 && $totalAvailable >= $verified_reward[0]['credits']){
                        
                        $reward = new RewardsModel();
                        $reward->plugin = 'loyalty';
                        $reward->id_customer = $customer->id;
                        $reward->id_reward_state = 2;
                        $reward->credits = $verified_reward[0]['credits'];
                        $reward->reason = 'Registro Con Patrocinio';
                        $reward->date_add = date('Y-m-d H:i:s', strtotime('+0 day', strtotime(date("Y-m-d H:i:s"))));
                        $reward->save();
                        
                        $reward_sponsor = new RewardsModel();
                        $reward_sponsor->plugin = 'loyalty';
                        $reward_sponsor->id_customer = $verified_reward[0]['id_customer'];
                        $reward_sponsor->id_reward_state = 2;
                        $reward_sponsor->credits = -$verified_reward[0]['credits'];
                        $reward_sponsor->reason = 'Registro Con Patrocinio';
                        $reward_sponsor->date_add = date('Y-m-d H:i:s', strtotime('+0 day', strtotime(date("Y-m-d H:i:s"))));
                        $reward_sponsor->save();
                    }
                }
                
                else{
                    $this->errors[] = Tools::displayError('An error occurred while creating your account.');
                }    
                //Hook::exec('actionBeforeSubmitAccount');
            }
        else
            {
            Hook::exec('actionBeforeSubmitAccount');
            $methodPayment = "";
            if ( Configuration::get('PS_BUY_MEMBERSHIP') ) {
                if ( isset($_POST['nombre']) && isset($_POST['numerot']) && isset($_POST['Month']) && isset($_POST['year']) && isset($_POST['codigot']) &&
                     !empty($_POST['nombre']) && !empty($_POST['numerot']) && !empty($_POST['Month']) && !empty($_POST['year']) && !empty($_POST['codigot']) &&
                     $_POST['nombre'] != "" && $_POST['numerot'] != "" && $_POST['Month'] != "" && $_POST['year'] != "" && $_POST['codigot'] != "" )
                {
                    $methodPayment = "cc";
                } elseif ( isset($_POST['psebank']) && isset($_POST['psetypedoc']) && isset($_POST['psenumdoc']) && isset($_POST['namebank']) &&
                            !empty($_POST['psebank']) && !empty($_POST['psetypedoc']) && !empty($_POST['psenumdoc']) && !empty($_POST['namebank']) &&
                            $_POST['psebank'] != "" && $_POST['psetypedoc'] != "" && $_POST['psenumdoc'] != "" && $_POST['namebank'] != "" )
                {
                    $methodPayment = "pse";
                    $_POST['pse_bank'] = $_POST['psebank'];
                    $_POST['name_bank'] = $_POST['namebank'];
                    $_POST['pse_tipoCliente'] = $_POST['psetypecustomer'];
                    $_POST['pse_docType'] = $_POST['psetypedoc'];
                    $_POST['pse_docNumber'] = $_POST['psenumdoc'];
                } else {
                    $this->errors[] = Tools::displayError('Por favor indique un medio de pago');
                }
            }

            $this->create_account = true;
            //$this->context->smarty->assign('email_create', 1);

            // New Guest customer
            if (!Tools::getValue('is_new_customer', 1) && !Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
                $this->errors[] = Tools::displayError('You cannot create a guest account.');
            }
            if (!Tools::getValue('is_new_customer', 1)) {
                $_POST['passwd'] = md5(time()._COOKIE_KEY_);
            }
            if ($guest_email = Tools::getValue('guest_email')) {
                $_POST['email'] = $guest_email;
            }
            // Checked the user address in case he changed his email address
            if (Validate::isEmail($email = Tools::getValue('email')) && !empty($email)) {
                $activeCustom = Db::getInstance()->getValue("SELECT active FROM "._DB_PREFIX_."customer WHERE email LIKE '%".$email."%'");
                $kickoutCustom = Db::getInstance()->getValue("SELECT COUNT(*) num FROM "._DB_PREFIX_."rewards_sponsorship_kick_out WHERE email = '".$email."'");
                if (Customer::customerExists($email) && $activeCustom == 1 && $kickoutCustom == 0 ) {
                    $this->errors[] = Tools::displayError('An account using this email address has already been registered.', false);
                }
            }
            // Preparing customer
            $customer = new Customer();
            $lastnameAddress = Tools::getValue('lastname');
            $firstnameAddress = Tools::getValue('firstname');
            $_POST['lastname'] = Tools::getValue('customer_lastname', $lastnameAddress);
            $_POST['firstname'] = Tools::getValue('customer_firstname', $firstnameAddress);

            if ( Tools::getValue('typedocument') == 0 ) {
                if ( Validate::isIdentification( Tools::getValue('gover') ) || Tools::getValue('gover') == "" ) {
                    $this->errors[] = Tools::displayError('Government Id es incorrecto');
                }
            } 
            else if ( Tools::getValue('typedocument') == 2 ){
                if ( Validate::isIdentificationCE( Tools::getValue('gover') ) || Tools::getValue('gover') == "" ) {
                    $this->errors[] = Tools::displayError('Government Id es incorrecto');
                }
            }
            else {
                if ( Tools::getValue('gover') == "" ) {
                    $this->errors[] = Tools::displayError('Government Id es incorrecto');
                }
                if ( Tools::getValue('checkdigit') == "" ) {
                    $this->errors[] = Tools::displayError('Codigo de verificacion incorrecto');
                }
            }
            /*if ( Tools::getValue('days') == "" || Tools::getValue('months') == "" || Tools::getValue('years') == "" ) {
                $this->errors[] = Tools::displayError('Fecha de nacimiento incorrecta');
            }*/
            if ( Tools::getValue('address1') == "" ) {
                $this->errors[] = Tools::displayError('Direccion es incorrecta');
            }
            /*if ( Tools::getValue('address2') == "" ) {
                $this->errors[] = Tools::displayError('Direccion (Linea 2) es incorrecta');
            }*/
            if ( Tools::getValue('city') == "" ) {
                $this->errors[] = Tools::displayError('Ciudad es incorrecta');
            }
            
            $addresses_types = array('address');
            if (!Configuration::get('PS_ORDER_PROCESS_TYPE') && Configuration::get('PS_GUEST_CHECKOUT_ENABLED') && Tools::getValue('invoice_address')) {
                $addresses_types[] = 'address_invoice';
            }
            $error_phone = false;
            if (Configuration::get('PS_ONE_PHONE_AT_LEAST')) {
                if (Tools::isSubmit('submitGuestAccount') || !Tools::getValue('is_new_customer')) {
                    if (!Tools::getValue('phone') && !Tools::getValue('phone_mobile')) {
                        $error_phone = true;
                    }
                } elseif (((Configuration::get('PS_REGISTRATION_PROCESS_TYPE') && Configuration::get('PS_ORDER_PROCESS_TYPE'))
                        || (Configuration::get('PS_ORDER_PROCESS_TYPE') && !Tools::getValue('email_create'))
                        || (Configuration::get('PS_REGISTRATION_PROCESS_TYPE') && Tools::getValue('email_create')))
                        && (!Tools::getValue('phone') && !Tools::getValue('phone_mobile'))) {
                    $error_phone = true;
                }
            }
            if ($error_phone) {
                $this->errors[] = Tools::displayError('You must register at least one phone number.');
            }

            $this->errors = array_unique(array_merge($this->errors, $customer->validateController()));
            // Check the requires fields which are settings in the BO
            $this->errors = $this->errors + $customer->validateFieldsRequiredDatabase();
            if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE') && !$this->ajax && !Tools::isSubmit('submitGuestAccount')) {
                if (!count($this->errors)) {
                    
                    $this->processCustomerNewsletter($customer);
                    $customer->firstname = Tools::ucwords($customer->firstname);
                    $customer->dni = Tools::getValue("gover");
                    $customer->kick_out = 0;
                    $customer->birthday = (empty($_POST['years']) ? '' : (int)Tools::getValue('years').'-'.(int)Tools::getValue('months').'-'.(int)Tools::getValue('days'));
                    /*if (!Validate::isBirthDate($customer->birthday)) {
                        $this->errors[] = Tools::displayError('Invalid date of birth.');
                    }*/
                    // New Guest customer
                    $customer->is_guest = (Tools::isSubmit('is_new_customer') ? !Tools::getValue('is_new_customer', 1) : 0);
                    $customer->active = 0;
                    $customer->phone =  Tools::getValue('phone_mobile');
                    $customer->method_add = 'Web';

                    // Validate exist username
                    if ( Customer::usernameExists( Tools::getValue("username") ) ) {
                        $this->errors[] = Tools::displayError('El nombre de usuario ya se encuentra en uso.');
                    }

                    // Validate dni
                    if ( Customer::dniExists( Tools::getValue("gover"),Tools::getValue('email') ) ) {
                        $this->errors[] = Tools::displayError('El numero de identificacion ya se encuentra en uso.');
                    }

                    if (!count($this->errors)) {

                        $customerLoaded = false;
                        $customExists = Customer::customerExists( Tools::getValue('email') );

                        $customer->date_kick_out = date ( 'Y-m-d H:i:s' , strtotime ( '+30 day' , strtotime ( date("Y-m-d H:i:s") ) ) );
                        $customer->warning_kick_out = 0;
                        $customer->method_add = 'Web';

                        if ( $customExists ) {
                            $idCustom = Customer::getCustomersByEmail( Tools::getValue('email') );
                            $customer = new Customer($idCustom[0]['id_customer']);
                            $customer->username = Tools::getValue("username");
                            $customer->firstname = Tools::getValue("customer_firstname");
                            $customer->lastname = Tools::getValue("customer_lastname");
                            $customer->passwd = Tools::encrypt( Tools::getValue("passwd") );
                            $customer->dni = Tools::getValue("gover");
                            $customer->kick_out = 0;
                            $customer->active=0;
                            $customer->phone =  Tools::getValue('phone_mobile');
                            $customer->date_kick_out = date ( 'Y-m-d H:i:s' , strtotime ( '+60 day' , strtotime ( date("Y-m-d H:i:s") ) ) );
                            $customer->date_add = date('Y-m-d H:i:s', strtotime('+0 day', strtotime(date("Y-m-d H:i:s"))));
                            $customer->birthday = (empty($_POST['years']) ? '' : (int)Tools::getValue('years').'-'.(int)Tools::getValue('months').'-'.(int)Tools::getValue('days'));
                            $customer->method_add = 'Web';
                            
                            $sendSMS = false;
                            while ( !$sendSMS ) {
                                $sendSMS = $customer->confirmCustomerSMS($customer->id);
                            }

                            if ( $sendSMS ) {
                                $this->context->smarty->assign('id_customer', $customer->id);
                                $this->context->smarty->assign('codesponsor', $code_sponsor);
                                $this->context->smarty->assign('id_sponsor', $id_sponsor);
                                $this->context->smarty->assign('sendSMS', true);
                            }
                            
                            $customer->update();
                            $customerLoaded = true;
                            
                        } else {
                            $customerLoaded = $customer->add();
                            $verified_reward = Db::getInstance()->executeS('SELECT *, SUM(credits) as credits_back FROM '._DB_PREFIX_.'rewards_distribute 
                                                WHERE date_from BETWEEN (SELECT date_from FROM '._DB_PREFIX_.'rewards_distribute 
                                                WHERE method_add = "Backoffice" AND active = 1 ORDER BY date_from ASC LIMIT 1) AND NOW() 
                                                AND method_add = "Backoffice" AND active = 1
                                                   ');
                            if($verified_reward[0]['id_rewards_distribute'] != ''){
                                
                                $reward = new RewardsModel();
                                $reward->plugin = 'loyalty';
                                $reward->id_customer = $customer->id;
                                $reward->id_reward_state = 2;
                                $reward->credits = $verified_reward[0]['credits_back'];
                                $reward->reason = 'Registro Backoffice';
                                $reward->date_add = date('Y-m-d H:i:s', strtotime('+0 day', strtotime(date("Y-m-d H:i:s"))));
                                $reward->add();
                                
                            }
                        }
                        
                        if ( $customerLoaded ) {
                            if (!$customer->is_guest) {
                                if (!$this->sendConfirmationMail($customer)) {
                                    $this->errors[] = Tools::displayError('The email cannot be sent.');
                                }
                            }
                            $sendSMS = false;
                            while ( !$sendSMS ) {
                                $sendSMS = $customer->confirmCustomerSMS($customer->id);
                            }

                            if ( $sendSMS ) {
                                $this->context->smarty->assign('id_customer', $customer->id);
                                $this->context->smarty->assign('codesponsor', $code_sponsor);
                                $this->context->smarty->assign('id_sponsor', $id_sponsor);
                                $this->context->smarty->assign('sendSMS', true);
                            }
                            $this->updateContext($customer);

                            if ($this->context->cookie->id_cart)
                            {
                                $cart = new Cart($this->context->cookie->id_cart);
                            }

                            if (!isset($cart) OR !$cart->id)
                            {
                                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'rewards_sponsorship` SET `id_customer` = '.(int)$customer->id.' WHERE `email` = "'.$customer->email.'"');

                                if ( $customExists ) {
                                    $addressexist = $customer->getAddresses(0);
                                    $address = new Address($addressexist[0]['id_address']);
                                } else {
                                    $address = new Address();
                                }

                                $address->id_customer = $customer->id;
                                $address->id_country = Tools::getValue("id_country");
                                $address->alias = 'Mi Direccion';
                                $address->lastname = Tools::getValue("customer_lastname");
                                $address->firstname = Tools::getValue("customer_firstname");
                                $address->type_document = Tools::getValue("typedocument");
                                $address->dni = Tools::getValue("gover");
                                $address->checkdigit = ( empty(Tools::getValue("checkdigit")) || Tools::getValue("checkdigit") == "" ) ? "" : Tools::getValue("checkdigit");
                                $address->address1 = Tools::getValue("address1");
                                // $address->address2 = Tools::getValue("address2");
                                if(Tools::getValue("city_input")!= ''){
                                   $address->city = Tools::getValue("city_input"); 
                                }
                                else{
                                   $address->city = Tools::getValue("city");  
                                }
                                $address->phone = Tools::getValue("phone_mobile");

                                if ( $customExists ) {
                                    $address->update();
                                } else {
                                    $address->add();
                                }

                                // Customer::addCard($customer->id, $customer->secure_key, $_POST['numerot'], $customer->firstname." ".$customer->lastname, '', $_POST['Month']."/".$_POST['year']);

                                $addresscreate = $customer->getAddresses(0);

                                /*$cart = new Cart();
                                $cart->id_customer = (int)($customer->id);
                                $cart->id_lang = (int)($this->context->cookie->id_lang);
                                $cart->id_address_delivery = $addresscreate[0]['id_address'];
                                $cart->id_address_invoice = $addresscreate[0]['id_address'];
                                $cart->id_currency = (int)($this->context->cookie->id_currency);
                                $cart->recyclable = 0;
                                $cart->gift = 0;
                                $cart->add();
                                $this->context->cookie->id_cart = (int)($cart->id);
                                $cart->update();

                                $valorProduct = ( isset($_POST['valorSlider']) ) ? $_POST['valorSlider'] : 0;
                                $row = DB::getInstance()->getRow( 'SELECT id_product FROM `'._DB_PREFIX_.'product` WHERE `price` = '.(int)$valorProduct.' AND reference LIKE "MFLUZ%"' );
                                $idProduct = $row['id_product'];
                                $this->context->cart = $cart;
                                $this->context->cart->updateQty(1,$idProduct,NULL,FALSE);
                                $cart->update();*/

                                if ( Configuration::get('PS_BUY_MEMBERSHIP') ) {
                                    switch ( $methodPayment ) {
                                        case "cc":
                                            require_once(_PS_MODULE_DIR_ . 'payulatam/credit_card.php');
                                            break;
                                        case "pse":
                                            require_once(_PS_MODULE_DIR_ . 'payulatam/payuPse.php');
                                            break;
                                    }
                                } else {
                                    Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."customer_group WHERE id_customer = ".$customer->id);
                                    Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."customer_group VALUES (".$customer->id.",3), (".$customer->id.",4)");
                                    Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."customer SET id_default_group = 4 WHERE id_customer = ".$customer->id);
                                    /* REGISTRAR COMPRA DE LICENCIA DE 0 PESOS
                                    $payment_module = Module::getInstanceByName('bankwire');
                                    $payment_module->validateOrder($cart->id, 2, 0, 'Pedido Gratuito');*/
                                    
                                    
                                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_sponsor, code_sponsor, code)
                                                                VALUES ('.$customer->id.', "'.$code_sponsor.'", "'.$code_generate.'")');
                                    
                                    $this->sendNotificationSponsor($customer->id);
                                    Tools::redirect($this->context->link->getPageLink('my-account', true));
                                }

                                /*$customer = new Customer($cart->id_customer);
                                if (!Validate::isLoadedObject($customer)) {
                                        Tools::redirect('index.php?controller=order&step=1');
                                }
                                $currency = $this->context->currency;
                                $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
                                Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : 'my-account'));
                                $mailVars = array(
                                        '{bankwire_owner}' => Configuration::get('BANK_WIRE_OWNER'),
                                        '{bankwire_details}' => nl2br(Configuration::get('BANK_WIRE_DETAILS')),
                                        '{bankwire_address}' => nl2br(Configuration::get('BANK_WIRE_ADDRESS'))
                                );
                                //Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$payment->id.'&id_order='.$payment->currentOrder.'&key='.$customer->secure_key);
                                Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : 'my-account'));
                                $query = 'SELECT COUNT(id_order) FROM `'._DB_PREFIX_.'orders` WHERE `id_customer` = '.(int)$customer->id;
                                $countOrder = Db::getInstance()->getValue($query);
                                if ((int)$countOrder == 1){
                                   //$grupos = 'INSERT INTO '._DB_PREFIX_.'customer_group(id_customer, id_group) VALUES ('.(int)$customer->id.',4)';
                                   //Db::getInstance()->execute($grupos);
                                }*/
                            }

                            Hook::exec('actionCustomerAccountAdd', array(
                                '_POST' => $_POST,
                                'newCustomer' => $customer
                            ));
                            if ($this->ajax) {
                                $return = array(
                                    'hasError' => !empty($this->errors),
                                    'errors' => $this->errors,
                                    'isSaved' => true,
                                    'id_customer' => (int)$this->context->cookie->id_customer,
                                    'id_address_delivery' => $this->context->cart->id_address_delivery,
                                    'id_address_invoice' => $this->context->cart->id_address_invoice,
                                    'token' => Tools::getToken(false)
                                );
                                $this->ajaxDie(Tools::jsonEncode($return));
                            }
                            if (($back = Tools::getValue('back')) && $back == Tools::secureReferrer($back)) {
                                Tools::redirect(html_entity_decode($back));
                            }

                            // redirection: if cart is not empty : redirection to the cart
                            if (count($this->context->cart->getProducts(true)) > 0) {

                                Tools::redirect('index.php?controller=order');
                            }
                            // else : redirection to the account
                            else {
                                Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : 'my-account'));
                            }
                            
                            //$this->processSubmitLogin($validate = 1);
                        } else {
                            $this->errors[] = Tools::displayError('An error occurred while creating your account.');
                        }

                    }

                }

            } 
            else {
                // if registration type is in one step, we save the address
                $_POST['lastname'] = $lastnameAddress;
                $_POST['firstname'] = $firstnameAddress;
                $post_back = $_POST;
                // Preparing addresses
                foreach ($addresses_types as $addresses_type) {
                    $$addresses_type = new Address();
                    $$addresses_type->id_customer = 1;
                    if ($addresses_type == 'address_invoice') {
                        foreach ($_POST as $key => &$post) {
                            if ($tmp = Tools::getValue($key.'_invoice')) {
                                $post = $tmp;
                            }
                        }
                    }
                    $this->errors = array_unique(array_merge($this->errors, $$addresses_type->validateController()));
                    if ($addresses_type == 'address_invoice') {
                        $_POST = $post_back;
                    }
                    if (!($country = new Country($$addresses_type->id_country)) || !Validate::isLoadedObject($country)) {
                        $this->errors[] = Tools::displayError('Country cannot be loaded with address->id_country');
                    }
                    if (!$country->active) {
                        $this->errors[] = Tools::displayError('This country is not active.');
                    }
                    $postcode = $$addresses_type->postcode;
                    /* Check zip code format */
                    if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                        $this->errors[] = sprintf(Tools::displayError('The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s'), str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))));
                    } elseif (empty($postcode) && $country->need_zip_code) {
                        $this->errors[] = Tools::displayError('A Zip / Postal code is required.');
                    } elseif ($postcode && !Validate::isPostCode($postcode)) {
                        $this->errors[] = Tools::displayError('The Zip / Postal code is invalid.');
                    }
                    if ($country->need_identification_number && (!Tools::getValue('dni') || !Validate::isDniLite(Tools::getValue('dni')))) {
                        $this->errors[] = Tools::displayError('The identification number is incorrect or has already been used.');
                    } elseif (!$country->need_identification_number) {
                        $$addresses_type->dni = null;
                    }
                    if (Tools::isSubmit('submitAccount') || Tools::isSubmit('submitGuestAccount')) {
                        if (!($country = new Country($$addresses_type->id_country, Configuration::get('PS_LANG_DEFAULT'))) || !Validate::isLoadedObject($country)) {
                            $this->errors[] = Tools::displayError('Country is invalid');
                        }
                    }
                    $contains_state = isset($country) && is_object($country) ? (int)$country->contains_states: 0;
                    $id_state = isset($$addresses_type) && is_object($$addresses_type) ? (int)$$addresses_type->id_state: 0;
                    if ((Tools::isSubmit('submitAccount') || Tools::isSubmit('submitGuestAccount')) && $contains_state && !$id_state) {
                        $this->errors[] = Tools::displayError('This country requires you to choose a State.');
                    }
                }
            }

            /*if (!@checkdate(Tools::getValue('months'), Tools::getValue('days'), Tools::getValue('years')) && !(Tools::getValue('months') == '' && Tools::getValue('days') == '' && Tools::getValue('years') == '')) {
                $this->errors[] = Tools::displayError('Invalid date of birth');
            }*/
            if (!count($this->errors)) {
                if (Customer::customerExists(Tools::getValue('email'))) {
                    $this->errors[] = Tools::displayError('An account using this email address has already been registered. Please enter a valid password or request a new one. ', false);
                }
                $this->processCustomerNewsletter($customer);
                $customer->birthday = (empty($_POST['years']) ? '' : (int)Tools::getValue('years').'-'.(int)Tools::getValue('months').'-'.(int)Tools::getValue('days'));
                /*if (!Validate::isBirthDate($customer->birthday)) {
                    $this->errors[] = Tools::displayError('Invalid date of birth');
                }*/
                if (!count($this->errors)) {
                    $customer->active = 0;
                    // New Guest customer
                    if (Tools::isSubmit('is_new_customer')) {
                        $customer->is_guest = !Tools::getValue('is_new_customer', 1);
                    } else {
                        $customer->is_guest = 0;
                    }
                    if (!$customer->add()) {
                        $this->errors[] = Tools::displayError('An error occurred while creating your account.');
                    } else {
                        foreach ($addresses_types as $addresses_type) {
                            $$addresses_type->id_customer = (int)$customer->id;
                            if ($addresses_type == 'address_invoice') {
                                foreach ($_POST as $key => &$post) {
                                    if ($tmp = Tools::getValue($key.'_invoice')) {
                                        $post = $tmp;
                                    }
                                }
                            }
                            $this->errors = array_unique(array_merge($this->errors, $$addresses_type->validateController()));
                            if ($addresses_type == 'address_invoice') {
                                $_POST = $post_back;
                            }
                            if (!count($this->errors) && (Configuration::get('PS_REGISTRATION_PROCESS_TYPE') || $this->ajax || Tools::isSubmit('submitGuestAccount')) && !$$addresses_type->add()) {
                                $this->errors[] = Tools::displayError('An error occurred while creating your address.');
                            }
                        }
                        if (!count($this->errors)) {
                            if (!$customer->is_guest) {
                                $this->context->customer = $customer;
                                $customer->cleanGroups();
                                // we add the guest customer in the default customer group
                                $customer->addGroups(array((int)Configuration::get('PS_CUSTOMER_GROUP')));

                                if (!$this->sendConfirmationMail($customer)) {
                                    $this->errors[] = Tools::displayError('The email cannot be sent.');
                                }
                            } else {
                                $customer->cleanGroups();
                                // we add the guest customer in the guest customer group
                                $customer->addGroups(array((int)Configuration::get('PS_GUEST_GROUP')));
                            }
                            $this->updateContext($customer);
                            $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)$customer->id);
                            $this->context->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)$customer->id);
                            if (isset($address_invoice) && Validate::isLoadedObject($address_invoice)) {
                                $this->context->cart->id_address_invoice = (int)$address_invoice->id;
                            }
                            if ($this->ajax && Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                                $delivery_option = array((int)$this->context->cart->id_address_delivery => (int)$this->context->cart->id_carrier.',');
                                $this->context->cart->setDeliveryOption($delivery_option);
                            }
                            // If a logged guest logs in as a customer, the cart secure key was already set and needs to be updated
                            $this->context->cart->update();
                            // Avoid articles without delivery address on the cart
                            $this->context->cart->autosetProductAddress();
                            Hook::exec('actionCustomerAccountAdd', array(
                                    '_POST' => $_POST,
                                    'newCustomer' => $customer
                                ));
                            if ($this->ajax) {
                                $return = array(
                                    'hasError' => !empty($this->errors),
                                    'errors' => $this->errors,
                                    'isSaved' => true,
                                    'id_customer' => (int)$this->context->cookie->id_customer,
                                    'id_address_delivery' => $this->context->cart->id_address_delivery,
                                    'id_address_invoice' => $this->context->cart->id_address_invoice,
                                    'token' => Tools::getToken(false)
                                );
                                $this->ajaxDie(Tools::jsonEncode($return));
                            }
                            // if registration type is in two steps, we redirect to register address
                            if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE') && !$this->ajax && !Tools::isSubmit('submitGuestAccount')) {
                                Tools::redirect('index.php?controller=address');
                            }
                            if (($back = Tools::getValue('back')) && $back == Tools::secureReferrer($back)) {
                                Tools::redirect(html_entity_decode($back));
                            }
                            // redirection: if cart is not empty : redirection to the cart
                            if (count($this->context->cart->getProducts(true)) > 0) {
                                Tools::redirect('index.php?controller=order'.($multi = (int)Tools::getValue('multi-shipping') ? '&multi-shipping='.$multi : ''));
                            }
                            // else : redirection to the account
                            else {
                                Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : 'my-account'));
                            }
                        }
                    }
                }
            }
            if (count($this->errors)) {
                //for retro compatibility to display guest account creation form on authentication page
                if (Tools::getValue('submitGuestAccount')) {
                    $_GET['display_guest_checkout'] = 1;
                }
                if (!Tools::getValue('is_new_customer')) {
                    unset($_POST['passwd']);
                }
                if ($this->ajax) {
                    $return = array(
                        'hasError' => !empty($this->errors),
                        'errors' => $this->errors,
                        'isSaved' => false,
                        'id_customer' => 0
                    );
                    $this->ajaxDie(Tools::jsonEncode($return));
                }
                    $this->context->smarty->assign('account_error', $this->errors);
            }
        }
    }
    protected function sendConfirmationMail(Customer $customer)
    {
        if (!Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
        
        $vars = array(
                '{username}' => $customer->username,
                '{password}' =>  Context::getContext()->link->getPageLink('password', true, null, 'token='.$customer->secure_key.'&id_customer='.(int)$customer->id.'&valid_auth=1'),                
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{dni}' => $customer->dni,
                '{birthdate}' => $customer->birthday,
                '{address}' => Tools::getValue("address1"),
                '{phone}' => Tools::getValue("phone_mobile"),
                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                '{shop_url_personal}' => Context::getContext()->link->getPageLink('identity', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                '{learn_more_url}' => "http://reglas.fluzfluz.co",
            );
        
                $template = 'welcome_fluzfluz';
                $prefix_template = '16-welcome_fluzfluz';

                $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
                $row_subject = Db::getInstance()->getRow($query_subject);
                $message_subject = $row_subject['subject_mail'];

                $allinone_rewards = new allinone_rewards();
                $allinone_rewards->sendMail($this->context->language->id, $template, $allinone_rewards->getL($message_subject),$vars, $customer->email, $customer->firstname.' '.$customer->lastname);
            }
    }
    
    public static function sendNotificationSponsor( $id_customer )
    {
        $query = "SELECT c.id_customer, c.username, c.email, SUM(r.credits) points
                    FROM "._DB_PREFIX_."rewards_sponsorship rs
                    LEFT JOIN "._DB_PREFIX_."customer c ON ( rs.id_sponsor = c.id_customer )
                    LEFT JOIN "._DB_PREFIX_."rewards r ON (rs.id_sponsor = r.id_customer AND r.id_reward_state = 2)
                    WHERE rs.id_customer = ".$id_customer;
        $sponsor = Db::getInstance()->getRow($query);
        
        $contributor_count = Db::getInstance()->getValue("SELECT COUNT(*) contributor_count
                                                            FROM "._DB_PREFIX_."customer
                                                            WHERE active = 1");
        
        $points_count = Db::getInstance()->getValue("SELECT SUM(credits) points_count
                                                        FROM "._DB_PREFIX_."rewards
                                                        WHERE id_reward_state = 2");
        
        $vars = array(
            '{username}' => $sponsor['username'],
            '{img_url}' => _PS_IMG_DIR_,
            '{points}' => $sponsor['points'] == "" ? 0 : round($sponsor['points']),
            '{contributor_count}' => $contributor_count,
            '{points_count}' => round($points_count),
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id)
        );
        
        $template = 'sponsorship-registration';
        $prefix_template = '16-sponsorship-registration';

        $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
        $row_subject = Db::getInstance()->getRow($query_subject);
        $message_subject = $row_subject['subject_mail'];
        
        $allinone_rewards = new allinone_rewards();
        $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject),$vars, $sponsor['email'], $sponsor['username']);
         
        /*Mail::Send(
            Context::getContext()->language->id,
            'notificationusersponsor',
            'Tu invitado se ha unido al Network',
            $vars,
            $sponsor['email'],
            $sponsor['username']
        );*/
    }
}

?>
