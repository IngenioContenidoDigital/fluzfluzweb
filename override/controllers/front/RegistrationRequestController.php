<?php

include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once(_PS_MODULE_DIR_ . 'allinone_rewards/allinone_rewards.php');
include_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsModel.php');

class RegistrationRequestController extends FrontController {

    public $php_self = 'registrationrequest';
    public $authRedirection = 'registrationrequest';
    public $ssl = true;
    

    public function setMedia() {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'registration_request.css');
    }

    public function initContent() {
        parent::initContent();
        
        $errors = array();

        $this->context->smarty->assign('countries', Country::getCountries($this->context->language->id, false, true));        
        $this->context->smarty->assign('cities', City::getCities());
        
        if (Tools::isSubmit('register')) {
            $firstname = Tools::getValue('firstname');
            $lastname = Tools::getValue('lastname');
            $username = Tools::getValue('username');
            //$password = Tools::getValue('password');
            $email = Tools::getValue('email');
            $phone = Tools::getValue('phone');
            $address1 = Tools::getValue('address');
            $country = Tools::getValue('country');
            $city = Tools::getValue('city');
            $typedocument = Tools::getValue('typedocument');
            $dni = Tools::getValue('dni');
            $codesponsor = Tools::getValue('code_sponsor');

            if ( $typedocument == 0 ) {
                if ( Validate::isIdentification($dni) ) {
                    $errors[] = "Identificacion Incorrecta";
                }
            } 

            if ( $typedocument == 2 ){
                if ( Validate::isIdentificationCE($dni) ) {
                    $errors[] = "Identificacion Incorrecta";
                }
            }

            if ( Customer::dniExists($dni, $email) ) {
                $errors[] = "Identificacion Existente";
            }

            if ( Customer::usernameExists($username) ) {
                $errors[] = "Username Existente";
            }

            if ( !Validate::isEmail($email) ) {
                $errors[] = "Email Incorrecto";
            }

            if ( Customer::customerExists($email) ) {
                $errors[] = "Email Existente";
            }
            
            $id_sponsor = "";
            if ( $codesponsor != "" ) {
                $id_sponsor = RewardsSponsorshipCodeModel::getIdSponsorByCode($codesponsor);
                if ( $id_sponsor == "" ) {
                    $errors[] = "Codigo de Patrocinio Incorrecto";
                }
            }
            
            if ( empty($errors) ) {
                $customer = new Customer();
                $customer->firstname = $firstname;
                $customer->lastname = $lastname;
                $customer->email = $email;
                $customer->passwd = Tools::encrypt($username);
                $customer->dni = $dni;
                $customer->phone = $phone;
                $customer->username = $username;
                $customer->id_default_group = 4;
                $customer->warning_kick_out = 0;
                $customer->kick_out = 0;
                $customer->active = 0;
                $customer->date_kick_out = date('Y-m-d H:i:s', strtotime('+30 day', strtotime(date("Y-m-d H:i:s"))));
                $customer->date_add = date('Y-m-d H:i:s', strtotime('+0 day', strtotime(date("Y-m-d H:i:s"))));
                $customer->method_add = 'Web / SolicitudRegistro';                
                $customer->add();
                Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."customer_group VALUES (".$customer->id.",3), (".$customer->id.",4)");
                
                $address = new Address();
                $address->id_customer = $customer->id;
                $address->dni = $customer->dni;
                $address->firstname = $customer->firstname;
                $address->lastname = $customer->lastname;
                $address->type_document = $typedocument;
                $address->id_country = $country;
                $address->city = $city; 
                $address->address1 = $address1;
                $address->address2 = "";
                $address->phone = $customer->phone;
                $address->phone_mobile = $customer->phone;
                $address->alias = 'Mi Direccion';
                $address->active = 1;
                $address->add();
                
                
                $sendSMS = false;
                while ( !$sendSMS ) {
                    $sendSMS = $customer->confirmCustomerSMS($customer->id);
                }
                
                if ( $sendSMS ) {
                    $this->context->smarty->assign('id_customer', $customer->id);
                    $this->context->smarty->assign('sendSMS', true);
                }
            } else {
                $this->context->smarty->assign('viewform', true);
            }
        } elseif (Tools::isSubmit('confirm')) {
            $id_customer = Tools::getValue('id_customer');
            $codesms = Tools::getValue('codesms');
            
            if ( Customer::validateCodeSMS($id_customer,$codesms) ) {
                $this->continueRegistration($id_customer);
                $this->context->smarty->assign('successfulregistration', true);
            } else {
                $errors[] = "Codigo Incorrecto";
                $this->context->smarty->assign('id_customer', $id_customer);
                $this->context->smarty->assign('sendSMS', true);
            }
        } elseif (Tools::isSubmit('resendSMS')) {
            $id_customer = Tools::getValue('id_customer');
            $sendSMS = false;
            while ( !$sendSMS ) {
                $sendSMS = Customer::confirmCustomerSMS($id_customer);
            }
            if ( $sendSMS ) {
                $this->context->smarty->assign('id_customer', $id_customer);
                $this->context->smarty->assign('sendSMS', true);
            }
        } else {
            $this->context->smarty->assign('viewform', true);
        }
        
        $this->context->smarty->assign('errorsform', $errors);
        
        $this->setTemplate(_PS_THEME_DIR_.'registration_request.tpl');
    }
    
    public function postProcess() {  }
    
    public function continueRegistration($id_customer) {
        $customer = new Customer($id_customer);
        $address = $customer->getAddresses();
        $address1 = $address[0]['address1'];
        
        $customer->active = 1;
        $customer->update();
        
        if ( $codesponsor != "" && $id_sponsor != "" ) {
            $tree = RewardsSponsorshipModel::_getTree($id_sponsor);
            array_shift($tree);
            $count_array = count($tree);

            if ($count_array < 2) {
                $sponsor = Db::getInstance()->getRow("SELECT c.id_customer, c.username, c.firstname, c.lastname, c.email, (2-COUNT(rs.id_sponsorship) ) sponsoships
                            FROM " . _DB_PREFIX_ . "customer c
                            LEFT JOIN " . _DB_PREFIX_ . "rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                            WHERE c.id_customer =".$id_sponsor);

                if ( !empty($sponsor) ) {
                    $sponsorship = new RewardsSponsorshipModel();
                    $sponsorship->id_sponsor = $sponsor['id_customer'];
                    $sponsorship->id_customer = $customer->id;
                    $sponsorship->firstname = $customer->firstname;
                    $sponsorship->lastname = $customer->lastname;
                    $sponsorship->email = $customer->email;
                    $sponsorship->channel = 1;
                    $send = "";
                    $sponsorship->save();

                    $code_generate = Allinone_rewardsSponsorshipModuleFrontController::generateIdCodeSponsorship($customer->username);
                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_sponsor, code_sponsor, code)
                                                VALUES ('.$customer->id.', "'.$codesponsor.'", "'.$code_generate.'")');
                }
            } else {
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
                    $sponsorship->save();

                    $code_generate = Allinone_rewardsSponsorshipModuleFrontController::generateIdCodeSponsorship($customer->username);
                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_sponsor, code_sponsor, code)
                                                VALUES ('.$customer->id.', "'.$codesponsor.'", "'.$code_generate.'")');
                }
            }
        } else {
            $query = "SELECT
                        c.id_customer,
                        (2 - COUNT(rs.id_sponsorship)) pendingsinvitation
                    FROM "._DB_PREFIX_."customer c
                    LEFT JOIN "._DB_PREFIX_."rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                    LEFT JOIN "._DB_PREFIX_."customer_group cg ON ( c.id_customer = cg.id_customer AND cg.id_group = 4 )
                    WHERE c.active = 1
                    AND c.kick_out = 0
                    AND c.autoaddnetwork = 0
                    GROUP BY c.id_customer
                    HAVING pendingsinvitation = 2
                    ORDER BY c.date_add ASC
                    LIMIT 1";
            $sponsor = Db::getInstance()->executeS($query);
            $sponsor = $sponsor[0];
            $sponsorship = new RewardsSponsorshipModel();
            $sponsorship->id_sponsor = $sponsor['id_customer'];
            $sponsorship->id_customer = $customer->id;
            $sponsorship->firstname = $customer->firstname;
            $sponsorship->lastname = $customer->lastname;
            $sponsorship->channel = 1;
            $sponsorship->email = $customer->email;
            $sponsorship->save();

            $code_generate = Allinone_rewardsSponsorshipModuleFrontController::generateIdCodeSponsorship($customer->username);
            Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_sponsor, code)
                                        VALUES ('.$customer->id.', "'.$code_generate.'")');
        }

        $link = new Link();
        $merchants_featured = Manufacturer::getManufacturersFeatured();
        $merchant = array_slice($merchants_featured, 0, 4);
        $table_merchants_featured = '<table cellspacing="10">
                                        <tr>
                                            <td>
                                                <a href="'.$link->getProductLink($merchant[0]['id_product'], $merchant[0]['link_rewrite']).'" title="'.$merchant[0]['name'].'">
                                                    <div style="background: url('._S3_PATH_.'m/m/'.$merchant[0]['id_manufacturer'].'.jpg) no-repeat; background-size: 100% 100%;">
                                                        <div style="height: 232px; display: table; text-align: center; min-width: 100%; padding: 10px;">
                                                            <div style="display: table-cell; vertical-align: middle;">
                                                                <img src="'._S3_PATH_.'m/'.$merchant[0]['id_manufacturer'].'.jpg" alt="'.$merchant[0]['name'].'" title="'.$merchant[0]['name'].'" style="max-width: 70%;">
                                                            </div>    
                                                        </div>
                                                    </div>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="'.$link->getProductLink($merchant[1]['id_product'], $merchant[1]['link_rewrite']).'" title="'.$merchant[1]['name'].'">
                                                    <div style="background: url('._S3_PATH_.'m/m/'.$merchant[1]['id_manufacturer'].'.jpg) no-repeat; background-size: 100% 100%;">
                                                        <div style="height: 232px; display: table; text-align: center; min-width: 100%; padding: 10px;">
                                                            <div style="display: table-cell; vertical-align: middle;">
                                                                <img src="'._S3_PATH_.'m/'.$merchant[1]['id_manufacturer'].'.jpg" alt="'.$merchant[1]['name'].'" title="'.$merchant[1]['name'].'" style="max-width: 70%;">
                                                            </div>    
                                                        </div>
                                                    </div>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <a href="'.$link->getProductLink($merchant[2]['id_product'], $merchant[2]['link_rewrite']).'" title="'.$merchant[2]['name'].'">
                                                    <div style="background: url('._S3_PATH_.'m/m/'.$merchant[2]['id_manufacturer'].'.jpg) no-repeat; background-size: 100% 100%;">
                                                        <div style="height: 232px; display: table; text-align: center; min-width: 100%; padding: 10px;">
                                                            <div style="display: table-cell; vertical-align: middle;">
                                                                <img src="'._S3_PATH_.'m/'.$merchant[2]['id_manufacturer'].'.jpg" alt="'.$merchant[2]['name'].'" title="'.$merchant[2]['name'].'" style="max-width: 70%;">
                                                            </div>    
                                                        </div>
                                                    </div>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="'.$link->getProductLink($merchant[3]['id_product'], $merchant[3]['link_rewrite']).'" title="'.$merchant[3]['name'].'">
                                                    <div style="background: url('._S3_PATH_.'m/m/'.$merchant[3]['id_manufacturer'].'.jpg) no-repeat; background-size: 100% 100%;">
                                                        <div style="height: 232px; display: table; text-align: center; min-width: 100%; padding: 10px;">
                                                            <div style="display: table-cell; vertical-align: middle;">
                                                                <img src="'._S3_PATH_.'m/'.$merchant[3]['id_manufacturer'].'.jpg" alt="'.$merchant[3]['name'].'" title="'.$merchant[3]['name'].'" style="max-width: 70%;">
                                                            </div>    
                                                        </div>
                                                    </div>
                                                </a>
                                            </td>
                                        </tr>
                                    </table>';

        $vars = array(
            '{username}' => $customer->username,
            //'{password}' =>  $password,
            '{password}' =>  Context::getContext()->link->getPageLink('password', true, null, 'token='.$customer->secure_key.'&id_customer='.(int)$customer->id.'&valid_auth=1'),
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{dni}' => $customer->dni,
            '{birthdate}' => $customer->birthday,
            '{address}' => $address1,
            '{phone}' => $customer->phone,
            '{merchants_featured}' => $table_merchants_featured,
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
        $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject), $vars, $customer->email, $customer->username);
        
        return true;        
    }
}
