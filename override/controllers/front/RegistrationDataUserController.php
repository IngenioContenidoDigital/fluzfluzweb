<?php

include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');

class RegistrationDataUserController extends FrontController {

    public $php_self = 'registrationdatauser';
    public $authRedirection = 'registrationdatauser';
    public $ssl = true;
    

    public function setMedia() {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'registration_data_user.css');
    }

    public function initContent() {
        parent::initContent();
        
        $errors = array();

        $this->context->smarty->assign('countries', Country::getCountries($this->context->language->id, false, true));        
        $this->context->smarty->assign('cities', City::getCities());
        
        $data = base64_decode( Tools::getValue('data') );
        $data = explode("&",$data);
        
        if ( $data[0] != "" && count($data) != 3 ) {
            Tools::redirect("pagenotfound");
        }
        
        $this->context->smarty->assign('data', $data);
        
        if (Tools::isSubmit('register')) {
            $name = Tools::getValue('name');
            $username = Tools::getValue('username');
            $email = Tools::getValue('email');
            $phone = Tools::getValue('phone');
            $address1 = Tools::getValue('address');
            $country = Tools::getValue('country');
            $city = Tools::getValue('city');
            $typedocument = Tools::getValue('typedocument');
            $dni = Tools::getValue('dni');

            if ( $typedocument == "Cedula de Ciudadania" ) {
                if ( Validate::isIdentification($dni) ) {
                    $errors[] = "El numero de identificacion es incorrecto.";
                }
            }

            if ( $typedocument == "Cedula de Extranjeria" ) {
                if ( Validate::isIdentificationCE($dni) ) {
                    $errors[] = "El numero de identificacion es incorrecto.";
                }
            }

            if ( !Validate::isEmail($email) ) {
                $errors[] = "El correo electronico es incorrecto.";
            }
            
            if ( empty($errors) ) {
                Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."user_registration_data (name, username, email, phone, address, country, city, typedocument, dni, date_add)
                                            VALUES ('".$name."', '".$username."', '".$email."', '".$phone."', '".$address1."', '".$country."', '".$city."', '".$typedocument."', '".$dni."', NOW())");
                
                $this->context->smarty->assign('successfulregistration', true);
                $this->context->smarty->assign('viewform', false);
            } else {
                $this->context->smarty->assign('viewform', true);
            }
        } else {
            $this->context->smarty->assign('viewform', true);
        }
        
        $this->context->smarty->assign('errorsform', $errors);
        
        $this->setTemplate(_PS_THEME_DIR_.'registration_data_user.tpl');
    }
    
    public function postProcess() {  }
    
}
