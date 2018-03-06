<?php

include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once(_PS_CLASS_DIR_.'Customer.php');

switch (Tools::getValue('action')) {
    case 'confirmCode':
        
        $code = Tools::getValue('code');
        $id_customer = Tools::getValue('customer');
        
        if ( Customer::validateCodeSMS($id_customer,$code) ) {
            try{
                $customer = new Customer($id_customer);
                $customer->active = 1;
                $customer->save();
                
                $response = 'true';
            }
            catch(Exception $e){
                $response = $e->getMessage();
            }
        } 
        else {
            $response = 'false';
        }
        echo $response;
        break;
    case 'resendCode':
        
        $id_customer = Tools::getValue('customer');
        $sendSMS = false;
        while ( !$sendSMS ) {
            $sendSMS = Customer::confirmCustomerSMS($id_customer);
        }
        if ( $sendSMS ) {
            $response = 'true';
        }
        
        echo $response;
        break;    
     default:
        break;
}