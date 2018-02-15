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

include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once(_PS_MODULE_DIR_ . 'allinone_rewards/allinone_rewards.php');
include_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsModel.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsSponsorshipModel.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/controllers/front/sponsorship.php');

class businessController extends FrontController {

    public $auth = true;
    public $ssl = true;
    public function setMedia() {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_ . 'business.css');
    }

    public function initContent() {
        parent::initContent();

        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
        array_shift($tree);
        
        $id_customer = $this->context->customer->id;
        $this->context->smarty->assign('id_customer',$id_customer);
        
        $name_customer = $this->context->customer->username;
        $this->context->smarty->assign('username',$name_customer);
        
        $totals = RewardsModel::getAllTotalsByCustomer((int) $this->context->customer->id);
        $pointsAvailable = round(isset($totals[RewardsStateModel::getValidationId()]) ? (float) $totals[RewardsStateModel::getValidationId()] : 0);
        $this->context->smarty->assign('pointsAvailable', $pointsAvailable);
        
        $list_business = $this->network();
        $total_users = (count($list_business));
        
        $statistics = $this->statistics();
        $this->context->smarty->assign('statistics', $statistics);
        
        $this->context->smarty->assign('s3', _S3_PATH_);
        
        $countries = Country::getCountries($this->context->language->id, false, true);
        $this->context->smarty->assign('countries', $countries);
        
        $this->context->smarty->assign('cities', City::getCities());
        
        $this->context->smarty->assign('all_fluz', $total_users);
        $this->context->smarty->assign('network', $list_business);
        
        /* Funciones Historial de Compras */
        $limit = 5;
        $history_purchase = $this->history_purchase_employee($list_business, $limit);
        $this->context->smarty->assign('history_purchase', $history_purchase);
        
        /* Funciones Historial de Transferencias */
        $history_transfer = $this->history_business();
        $this->context->smarty->assign('history_transfer', $history_transfer);
        
        /* Funciones Historial de Compras Fluz */
        $history_fluz = $this->history_fluz();
        $this->context->smarty->assign('history_fluz', $history_fluz);
        
        $this->setTemplate(_PS_THEME_DIR_ . 'business.tpl');
    }

    public function postProcess() {

        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
        array_shift($tree);
        
        if(Tools::isSubmit('export-excel')){
            
            $history_transfer = $this->history_business();
            $report = "<html>
                        <head>
                            <meta http-equiv=?Content-Type? content=?text/html; charset=utf-8? />
                        </head>
                            <body>
                                <table>
                                    <tr>
                                        <th>Id transferencia</th>
                                        <th>Id cliente</th>
                                        <th>Nombre</th>
                                        <th>Apellido</th>
                                        <th>Fecha de transferencia</th>
                                        <th>Numero de empleados</th>
                                        <th>Tipo de transferencia</th>
                                        <th>Fluz transferidos</th>
                                        <th>Precio en fluz</th>";
            
            $report .= "</tr>";
            
            foreach ($history_transfer as $data)
                {
                    $report .= "<tr>
                            <td>".$data['id_transferencia']."</td>
                            <td>".$data['id_cliente']."</td>
                            <td>".$data['nombre']."</td>
                            <td>".$data['apellido']."</td>
                            <td>".$data['fecha_transferencia']."</td>
                            <td>".$data['numero_empleados']."</td>
                            <td>".$data['tipo_transferencia']."</td>
                            <td>".$data['fluz_transferidos']."</td>
                            <td>".$data['precio_fluz']."</td>";
                }
            $report .= "         </table>
                        </body>
                    </html>";    
            
            header("Content-Type: application/vnd.ms-excel");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("content-disposition: attachment;filename=history_business.xls");
            die($report);
        }
        
        if(Tools::isSubmit('export-excel-purchase')){
            
            $history_purchase = $this->network();
            $limit = 10000;
            $purchase_employee = $this->history_purchase_employee($history_purchase, $limit);
            
            $report = "<html>
                        <head>
                            <meta http-equiv=?Content-Type? content=?text/html; charset=utf-8? />
                        </head>
                            <body>
                                <table>
                                    <tr>
                                        <th>Id Order</th>
                                        <th>Id Customer</th>
                                        <th>Nombre</th>
                                        <th>Cedula</th>
                                        <th>Email</th>
                                        <th>Nombre del producto</th>
                                        <th>Cantidad</th>
                                        <th>Valor Total</th>";
            
            $report .= "</tr>";
            
            foreach ($purchase_employee as $data)
                {
                    foreach ($data['details'] as $x){
                        $report .= "<tr>
                            <td>".$x['id_order']."</td>
                            <td>".$data['id_customer']."</td>
                            <td>".$data['firstname']."</td>
                            <td>".$data['dni']."</td>
                            <td>".$data['email']."</td>
                            <td>".$x['product_name']."</td>
                            <td>".$x['sum_quantity']."</td>
                            <td>".$x['sum_total']."</td>";
                    }
                    
                }
            $report .= "         </table>
                        </body>
                    </html>";    
            
            header("Content-Type: application/vnd.ms-excel");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("content-disposition: attachment;filename=history_purchase.xls");
            die($report);
        }
        
        if(Tools::isSubmit('export-excel-shopping-fluz')){

            $history_fluz = $this->history_fluz();
            
            $report = "<html>
                        <head>
                            <meta http-equiv=?Content-Type? content=?text/html; charset=utf-8? />
                        </head>
                            <body>
                                <table>
                                    <tr>
                                        <th>Referencia</th>
                                        <th>Pago</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Total Fluz</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Prec. Unit</th>
                                        <th>Fluz Unit</th>
                                        <th>Total Producto</th>
                                        <th>Total Fluz Producto</th>
                                    </tr>";
            
            foreach ($history_fluz as $order)
            {
                foreach ($order['products'] as $details)
                {
                    $report .= "<tr>
                                    <td>".$order['reference']."</td>
                                    <td>".$order['payment']."</td>
                                    <td>".$order['state']."</td>
                                    <td>".$order['date']."</td>
                                    <td>".$order['total']."</td>
                                    <td>".$order['total_fluz']."</td>
                                    <td>".$details['product_name']."</td>
                                    <td>".$details['product_quantity']."</td>
                                    <td>".round($details['product_price'])."</td>
                                    <td>".$details['product_fluz']."</td>
                                    <td>".round($details['product_price_total'])."</td>
                                    <td>".$details['product_fluz_total']."</td>
                                </tr>";
                }
            }
            
            $report .= "         </table>
                        </body>
                    </html>";    
            
            header("Content-Type: application/vnd.ms-excel");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("content-disposition: attachment;filename=history_purchase_fluz.xls");
            die($report);
        }
        
        if (Tools::isSubmit('add-employee')) {
            
            $array_error = array();
            $image_url = "";    
            $FirstNameEmployee = Tools::getValue('firstname');
            $LastNameEmployee = Tools::getValue('lastname');
            $username = Tools::getValue('username');
            $EmailEmployee = Tools::getValue('email');
            $passwordDni = Tools::getValue('dni');
            $point_used_add = "";
            $phone_user = Tools::getValue('phone_invoice');
            $address_customer = Tools::getValue('address_customer');
            $city_custom = Tools::getValue('city');
            $id_country = Tools::getValue('id_country');
            
            $valid_dni = Db::getInstance()->getRow('SELECT COUNT(dni) as dni 
                                                    FROM '._DB_PREFIX_.'customer WHERE dni = "'.$passwordDni.'" ');
            
            $valid_username = Db::getInstance()->getRow('SELECT COUNT(username)  as username 
                                                    FROM '._DB_PREFIX_.'customer WHERE username = "'.$username.'" ');
            
            $valid_phone = Db::getInstance()->getRow('SELECT COUNT(phone) as phone  
                                                    FROM '._DB_PREFIX_.'customer WHERE phone = "'.$phone_user.'" ');
            
            if (isset($_SERVER['HTTPS'])) {
               $image_url = 'https://'.Configuration::get('PS_SHOP_DOMAIN').'/img/business/'.$this->context->customer->id.'.png'; 
            }
            else{
               $image_url = 'http://'.Configuration::get('PS_SHOP_DOMAIN').'/img/business/'.$this->context->customer->id.'.png'; 
            }

            if (empty($FirstNameEmployee) || empty($LastNameEmployee) || !Validate::isName($FirstNameEmployee) || !Validate::isName($LastNameEmployee)) {
                $error['name'] = 'name invalid';
            }
            elseif (Tools::isSubmit('add-employee') && !Validate::isEmail($EmailEmployee)) {
                $error['email'] = 'email invalid';
            }
            elseif (empty($passwordDni)) {
                $error['dni'] = 'No se ha ingresado correctamente el campo Cedula';
            }
            else if($valid_dni['dni'] > 0){
                $error['dni_exists'] = 'dni exists';
                $error['cedula'] = $passwordDni;
            }
            else if($valid_phone['phone'] > 0){
                $error['valid_phone'] = 'valid phone';
                $error['phone'] = $phone_user;
            }
            else if($valid_username['username'] > 0){
                $error['valid_username'] = 'valid username';
                $error['username'] = $username;
            }
            elseif (RewardsSponsorshipModel::isEmailExists($EmailEmployee) || Customer::customerExists($EmailEmployee)) {
                $customerKickOut = Db::getInstance()->getValue("SELECT kick_out FROM " . _DB_PREFIX_ . "customer WHERE email = '" . $EmailEmployee . "'");
                if ($customerKickOut == 0) {
                    $error['email_exists'] = 'email exists';
                    $error['email'] = $EmailEmployee;
                }
            }
            
            array_push($array_error, $error); 
            $error = $array_error;
            
            if($error[0]==''){
                array_shift($error);
            }
            
            $code_generate = Allinone_rewardsSponsorshipModuleFrontController::generateIdCodeSponsorship($username);
            
            if (empty($error)) {
                
                $customer = new Customer();
                $customer->firstname = $FirstNameEmployee;
                $customer->lastname = $LastNameEmployee;
                $customer->email = $EmailEmployee;
                $customer->passwd = Tools::encrypt($passwordDni);
                $customer->dni = $passwordDni;
                $customer->phone = $phone_user;
                $customer->username = $username;
                $customer->id_default_group = 4;
                $customer->id_lang = $this->context->customer->id_lang;
                $customer->field_work = $this->context->customer->field_work;
                $customer->date_kick_out = date('Y-m-d H:i:s', strtotime('+30 day', strtotime(date("Y-m-d H:i:s"))));
                $customer->date_add = date('Y-m-d H:i:s', strtotime('+0 day', strtotime(date("Y-m-d H:i:s"))));
                $customer->method_add = 'Web / Business';
                $customer->add();
                
                Db::getInstance()->execute('INSERT  INTO ps_customer_group (id_customer, id_group)  
                        VALUES ( '.$customer->id.' ,3)');
                
                $address = new Address();
                $address->id_country = $id_country;
                $address->dni = $customer->dni;
                $address->id_customer = $customer->id;
                $address->alias = 'Mi Direccion';
                $address->firstname = $customer->firstname;
                $address->lastname = $customer->lastname;
                $address->address1 = $address_customer;
                $address->address2 = $address_customer;
                $address->city = $city_custom;
                $address->phone = $customer->phone;
                $address->phone_mobile = $customer->phone;
                $address->type_document = 0;
                $address->active = 1;
                $address->add();
                
                if($point_used_add != ''){
                
                    Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "transfers_fluz (id_customer, id_sponsor_received, date_add)
                                                VALUES (" . (int) $this->context->customer->id . ", ".$customer->id.",'" . date("Y-m-d H:i:s") . "')");

                    $query_t = 'SELECT id_transfers_fluz FROM ' . _DB_PREFIX_ . 'transfers_fluz WHERE id_customer=' . (int) $this->context->customer->id . ' ORDER BY id_transfers_fluz DESC';
                    $row_t = Db::getInstance()->getRow($query_t);
                    $id_transfer = $row_t['id_transfers_fluz'];

                    Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                            . "                          VALUES ('2', " . (int) $this->context->customer->id . ", 0,NULL,'0','0'," . -1 * $point_used_add . ",'loyalty', 'TransferFluzBusiness','" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "', " . (int) $id_transfer . ")");

                    Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                        . "                          VALUES ('2', " . (int) $customer->id . ", 0,NULL,'0','0'," . $point_used_add . ",'loyalty','TransferFluzBusiness','" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "', " . (int) $id_transfer . ")");
                }
                $count_array = count($tree);
                
                if ($count_array < 2) {
                    $sponsor = Db::getInstance()->getRow("SELECT c.id_customer, c.username, c.firstname, c.lastname, c.email, (2-COUNT(rs.id_sponsorship) ) sponsoships
                                FROM " . _DB_PREFIX_ . "customer c
                                LEFT JOIN " . _DB_PREFIX_ . "rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                                WHERE c.id_customer =" . $this->context->customer->id);
                    
                    if (!empty($sponsor)) {
                        $sponsorship = new RewardsSponsorshipModel();
                        $sponsorship->id_sponsor = $sponsor['id_customer'];
                        $sponsorship->id_customer = $customer->id;
                        $sponsorship->firstname = $FirstNameEmployee;
                        $sponsorship->lastname = $LastNameEmployee;
                        $sponsorship->email = $EmailEmployee;
                        $sponsorship->channel = 1;
                        $send = "";
                        if ($sponsorship->save()) {
                            
                                $vars = array(
                                '{username}' => $customer->username,
                                '{password}' =>  Context::getContext()->link->getPageLink('password', true, null, 'token='.$customer->secure_key.'&id_customer='.(int)$customer->id.'&valid_auth=1'),                
                                '{firstname}' => $customer->firstname,
                                '{lastname}' => $customer->lastname,
                                '{dni}' => $customer->dni,
                                '{birthdate}' => $customer->birthday,
                                '{address}' => $address_customer,
                                '{phone}' => $phone_user,
                                '{img_business}' => $image_url,    
                                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                                '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                                '{shop_url_personal}' => Context::getContext()->link->getPageLink('identity', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                                '{learn_more_url}' => "http://reglas.fluzfluz.co",
                            );
                            
                            Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_sponsor, code)
                                                           VALUES ('.$customer->id.', "'.$code_generate.'")');    
                                
                            AuthController::sendNotificationSponsor($customer->id);
                            
                            $template = 'welcome_fluzfluz_business';
                            $prefix_template = '16-welcome_fluzfluz_business';

                            $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
                            $row_subject = Db::getInstance()->getRow($query_subject);
                            $message_subject = $row_subject['subject_mail'];

                            $allinone_rewards = new allinone_rewards();
                            $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject),$vars, $sponsorship->email, $customer->firstname.' '.$customer->lastname);

                            $invitation_sent = true;
                        }
                    } else {
                        $error['sponsor'] = 'no sponsor';
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
                        $sponsorship->firstname = $FirstNameEmployee;
                        $sponsorship->lastname = $LastNameEmployee;
                        $sponsorship->email = $EmailEmployee;
                        $sponsorship->channel = 1;
                        $send = "";
                        if ($sponsorship->save()) {
                        
                        $vars = array(
                            '{username}' => $customer->username,
                            '{password}' =>  Context::getContext()->link->getPageLink('password', true, null, 'token='.$customer->secure_key.'&id_customer='.(int)$customer->id.'&valid_auth=1'),                
                            '{firstname}' => $customer->firstname,
                            '{lastname}' => $customer->lastname,
                            '{dni}' => $customer->dni,
                            '{birthdate}' => $customer->birthday,
                            '{address}' => $address_customer,
                            '{phone}' => $phone_user,
                            '{img_business}' => $image_url,
                            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                            '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                            '{shop_url_personal}' => Context::getContext()->link->getPageLink('identity', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                            '{learn_more_url}' => "http://reglas.fluzfluz.co",
                        );
                        
                        Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_sponsor, code)
                                                           VALUES ('.$customer->id.', "'.$code_generate.'")');
                        
                        AuthController::sendNotificationSponsor($customer->id);
                        
                        $template = 'welcome_fluzfluz_business';
                        $prefix_template = '16-welcome_fluzfluz_business';
                        
                        $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
                        $row_subject = Db::getInstance()->getRow($query_subject);
                        $message_subject = $row_subject['subject_mail'];
                        
                        $allinone_rewards = new allinone_rewards();
                        $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject),$vars, $sponsorship->email, $customer->firstname.' '.$customer->lastname);
                        
                        $invitation_sent = true;
                        }
                    }
                }
                Tools::redirect($this->context->link->getPageLink('confirmtransfercustomer', true));
            }
            $this->context->smarty->assign('error', $error);
        }
        
        if (Tools::isSubmit('upload-employee')) {
            
            $process = false;
            if ( isset($_POST["upload-employee"]) ) {
                
                if ( isset($_FILES["file"])) {
                    
                    //if there was an error uploading the file
                    if ($_FILES["file"]["error"] > 0) {
                         echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
                    }
                    else {

                         //if file already exists
                         if (file_exists("csvcustomer/" . $_FILES["file"]["name"])) {
                           $error_csv['csv'] = "already exists";
                           $error_csv['csv_name'] = $_FILES["file"]["name"];
                           $process = true;
                         }
                         else {
                                //Store file in directory "upload" with the name of "uploaded_file.txt"
                                $storagename = $_FILES["file"]["name"];
                                move_uploaded_file($_FILES["file"]["tmp_name"], "csvcustomer/" . $storagename);
                            }
                        }
                } 
                else {
                     echo "No file selected <br />";
                }
            }
            
            $filename = "csvcustomer/" . $storagename;
            $list_customer = $this->csv_to_array($filename);
            
            $array_error = array();
            $image_url = "";
            array_push($array_error, $error_csv);
            array_map('current', $array_error);
            $number_to_import = COUNT($list_customer);
            
            if($number_to_import < 80){
                $template = 'welcome_fluzfluz_business';
                $prefix_template = '16-welcome_fluzfluz_business';

                $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
                $row_subject = Db::getInstance()->getRow($query_subject);
                $message_subject = $row_subject['subject_mail'];
                
                if (isset($_SERVER['HTTPS'])) {
                    $image_url = 'https://'.Configuration::get('PS_SHOP_DOMAIN').'/img/business/'.$this->context->customer->id.'.png'; 
                 }
                else{
                    $image_url = 'http://'.Configuration::get('PS_SHOP_DOMAIN').'/img/business/'.$this->context->customer->id.'.png'; 
                 }
                 
                foreach($list_customer as $datacustomer){

                    $error_csv = "";
                    $valid_dni = Db::getInstance()->getRow('SELECT COUNT(dni) as dni 
                                                        FROM '._DB_PREFIX_.'customer WHERE dni = "'.$datacustomer['cedula'].'" ');

                    $valid_username = Db::getInstance()->getRow('SELECT COUNT(username)  as username 
                                                        FROM '._DB_PREFIX_.'customer WHERE username = "'.$datacustomer['Username'].'" ');

                    $valid_phone = Db::getInstance()->getRow('SELECT COUNT(phone) as phone  
                                                        FROM '._DB_PREFIX_.'customer WHERE phone = "'.$datacustomer['Telefono Empleado'].'" ');
                    
                    if (empty($datacustomer['First Name']) || empty($datacustomer['Last Name']) || !Validate::isName($datacustomer['First Name']) || !Validate::isName($datacustomer['Last Name'])) {
                        $error_csv['name'] = 'name invalid';
                        $error_csv['name_custom'] = $datacustomer['First Name'];
                        unlink($filename);
                    } 
                    elseif (Tools::isSubmit('upload-employee') && !Validate::isEmail($datacustomer['Email'])) {
                        $error_csv['email'] = 'email invalid';
                        $error_csv['email_customer'] = $datacustomer['Email'];
                        unlink($filename);
                    } 
                    else if($valid_dni['dni'] > 0){
                        $error_csv['dni_exists'] = 'dni exists';
                        $error_csv['cedula'] = $datacustomer['cedula'];
                        unlink($filename);
                    }
                    else if($valid_username['username'] > 0){
                        $error_csv['valid_username'] = 'valid username';
                        $error_csv['username'] = $datacustomer['Username'];
                        unlink($filename);
                    }
                    else if($valid_phone['phone'] > 0){
                        $error_csv['valid_phone'] = 'valid phone';
                        $error_csv['phone'] = $datacustomer['Telefono Empleado'];
                        unlink($filename);
                    }
                    else if(empty($datacustomer['cedula'])) {
                        $error_csv = 'No se ha ingresado correctamente el campo Cedula';
                        unlink($filename);
                    } 
                    elseif (RewardsSponsorshipModel::isEmailExists($datacustomer['Email']) || Customer::customerExists($datacustomer['Email'])) {
                            $customerKickOut = Db::getInstance()->getValue("SELECT kick_out FROM " . _DB_PREFIX_ . "customer WHERE email = '" . $datacustomer['Email'] . "'");
                        if ($customerKickOut == 0) {
                            $error_csv['email_exists'] = 'email exists';
                            $error_csv['email'] = $datacustomer['Email'];
                            $this->context->smarty->assign('email',$datacustomer['Email']);
                            unlink($filename);
                        }
                    }
                    
                $code_generate = Allinone_rewardsSponsorshipModuleFrontController::generateIdCodeSponsorship($datacustomer['Username']);
                   
                if (empty($error_csv)) {

                    $customer = new Customer();
                    $customer->firstname = $datacustomer['First Name'];
                    $customer->lastname = $datacustomer['Last Name'];
                    //$customer->active = $datacustomer['Active (0/1)'];
                    $customer->username = $datacustomer['Username'];
                    //$customer->id_gender = $datacustomer['Titles ID (Mr=1 , Ms=2)'];
                    $customer->dni = $datacustomer['cedula'];
                    $customer->email = $datacustomer['Email'];
                    $customer->phone = $datacustomer['Telefono Empleado'];
                    $customer->passwd = Tools::encrypt($datacustomer['cedula']);
                    $customer->date_kick_out = date('Y-m-d H:i:s', strtotime('+30 day', strtotime(date("Y-m-d H:i:s"))));
                    $customer->date_add = date('Y-m-d H:i:s', strtotime('+30 day', strtotime(date("Y-m-d H:i:s"))));
                    //$customer->newsletter = $datacustomer['Newsletter (0/1)'];
                    //$customer->birthday = $datacustomer['Birthday (yyyy-mm-dd)'];
                    $customer->id_default_group = 4;
                    $customer->id_lang = $this->context->customer->id_lang;
                    $customer->field_work = $this->context->customer->field_work;
                    $customer->method_add = 'Web / Business';
                    $customer->add();
                    
                    Db::getInstance()->execute('INSERT  INTO ps_customer_group (id_customer, id_group)  
                        VALUES ( '.$customer->id.' ,3)');
                    
                    $address = new Address();
                    $address->id_country = 69;
                    $address->dni = $customer->dni;
                    $address->id_customer = $customer->id;
                    $address->alias = 'Mi Direccion';
                    $address->firstname = $customer->firstname;
                    $address->lastname = $customer->lastname;
                    $address->address1 = $datacustomer['Direccion Empleado'];
                    $address->address2 = $datacustomer['Direccion Empleado'];
                    $address->city = $datacustomer['Ciudad'];
                    $address->phone = $datacustomer['Telefono Empleado'];
                    $address->phone_mobile = $datacustomer['Telefono Empleado'];
                    $address->type_document = 0;
                    $address->active = 1;
                    $address->add();
                    
                    $count_array = count($tree);

                    if ($count_array < 2) {

                        $sponsor = Db::getInstance()->getRow("SELECT c.id_customer, c.username, c.firstname, c.lastname, c.email, (2-COUNT(rs.id_sponsorship) ) sponsoships
                                    FROM " . _DB_PREFIX_ . "customer c
                                    LEFT JOIN " . _DB_PREFIX_ . "rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                                    WHERE c.id_customer =" . $this->context->customer->id);

                        if (!empty($sponsor)) {
                            $array_p['id'] = $customer->id;
                            array_push($tree, $array_p);

                            $sponsorship = new RewardsSponsorshipModel();
                            $sponsorship->id_sponsor = $sponsor['id_customer'];
                            $sponsorship->id_customer = $customer->id;
                            $sponsorship->firstname = $datacustomer['First Name'];
                            $sponsorship->lastname = $datacustomer['Last Name'];
                            $sponsorship->email = $datacustomer['Email'];
                            $sponsorship->channel = 1;
                            $send = "";
                            if ($sponsorship->save()) {
                                $vars = array(
                                '{username}' => $customer->username,
                                '{password}' =>  Context::getContext()->link->getPageLink('password', true, null, 'token='.$customer->secure_key.'&id_customer='.(int)$customer->id),                
                                '{firstname}' => $customer->firstname,
                                '{lastname}' => $customer->lastname,
                                '{dni}' => $customer->dni,
                                '{birthdate}' => $customer->birthday,
                                '{address}' => $address->address1,
                                '{phone}' => $datacustomer['Telefono Empleado'],
                                '{img_business}' => $image_url,
                                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                                '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                                '{shop_url_personal}' => Context::getContext()->link->getPageLink('identity', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                                '{learn_more_url}' => "http://reglas.fluzfluz.co",
                                );
                                
                                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_sponsor, code)
                                                           VALUES ('.$customer->id.', "'.$code_generate.'")');
                                
                                AuthController::sendNotificationSponsor($customer->id);

                                $allinone_rewards = new allinone_rewards();
                                $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject),$vars, $sponsorship->email, $customer->firstname.' '.$customer->lastname);

                                $invitation_sent = true;
                            }
                        } 
                        else 
                        {
                            $error_csv = 'no sponsor';
                        }
                    }
                    else {

                        $array_p['id'] = $customer->id;
                        array_push($tree, $array_p);
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
                            $sponsorship->firstname = $datacustomer['First Name'];
                            $sponsorship->lastname = $datacustomer['Last Name'];
                            $sponsorship->email = $datacustomer['Email'];
                            $sponsorship->channel = 1;
                            $send = "";
                            if ($sponsorship->save()) {

                                $vars = array(
                                '{username}' => $customer->username,
                                '{password}' =>  Context::getContext()->link->getPageLink('password', true, null, 'token='.$customer->secure_key.'&id_customer='.(int)$customer->id),                
                                '{firstname}' => $customer->firstname,
                                '{lastname}' => $customer->lastname,
                                '{dni}' => $customer->dni,
                                '{birthdate}' => $customer->birthday,
                                '{address}' => $address->address1,
                                '{phone}' => $datacustomer['Telefono Empleado'],
                                '{img_business}' => $image_url,  
                                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                                '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                                '{shop_url_personal}' => Context::getContext()->link->getPageLink('identity', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                                '{learn_more_url}' => "http://reglas.fluzfluz.co",
                                );
                                
                                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_sponsor, code)
                                                           VALUES ('.$customer->id.', "'.$code_generate.'")');
                                
                                AuthController::sendNotificationSponsor($customer->id);

                                $allinone_rewards = new allinone_rewards();
                                $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject),$vars, $sponsorship->email, $customer->firstname.' '.$customer->lastname);

                                $invitation_sent = true;
                                }
                            }
                        }
                               //Tools::redirect($this->context->link->getPageLink('confirmtransfercustomer', true));
                    }
                    else{
                            array_push($array_error, $error_csv); 
                            //$this->context->smarty->assign('error', $error);
                    }
                    $process = true;
                }

                if($array_error[0]==''){
                    array_shift($array_error);
                }

                if($process == true){
                    if(empty($array_error)){
                             Tools::redirect($this->context->link->getPageLink('confirmtransfercustomer', true));
                           }
                    else{
                        $error_csv = $array_error;
                        $this->context->smarty->assign('error_csv', $error_csv);
                    }       
                }
            }
            else{
                $error_csv[0]['csv_number'] = "registro";
                unlink($filename);
                $this->context->smarty->assign('error_csv', $error_csv);
            }
        }
        
        switch (Tools::getValue('action')) {
            case 'allFLuz':
                $pointUsed = Tools::getValue('ptoUsed');
                $point_used = RewardsModel::getRewardReadyForDisplay($pointUsed, $this->context->currency->id);
                
                $query_credits = "SELECT r.id_customer AS id_customer, SUM(r.credits) AS total_credits FROM "._DB_PREFIX_."rewards AS r WHERE r.id_reward_state=2 AND r.id_customer=".(int)$this->context->customer->id." GROUP BY r.id_customer";
                $row_credits = Db::getInstance()->getRow($query_credits);
                
                if($row_credits['total_credits']>=$point_used){
                
                Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "transfers_fluz (id_customer, id_sponsor_received, date_add)
                                            VALUES (" . (int) $this->context->customer->id . ", 0,'" . date("Y-m-d H:i:s") . "')");

                $query_t = 'SELECT id_transfers_fluz FROM ' . _DB_PREFIX_ . 'transfers_fluz WHERE id_customer=' . (int) $this->context->customer->id . ' ORDER BY id_transfers_fluz DESC';
                $row_t = Db::getInstance()->getRow($query_t);
                $id_transfer = $row_t['id_transfers_fluz'];

                Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                        . "                          VALUES ('2', " . (int) $this->context->customer->id . ", 0,NULL,'0','0'," . -1 * $point_used . ",'loyalty', 'TransferFluzBusiness','" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "', " . (int) $id_transfer . ")");

                $list_all = Tools::getValue('listEdit');
                $list_var_all = json_decode($list_all, true);
                
                foreach ($list_var_all as $network) {
                    
                    if (!empty($network)){
                    
                    $query_t = 'SELECT id_transfers_fluz, date_add FROM ' . _DB_PREFIX_ . 'transfers_fluz WHERE id_customer=' . (int) $this->context->customer->id . ' ORDER BY id_transfers_fluz DESC';
                    $row_t = Db::getInstance()->getRow($query_t);
                    $id_transfer = $row_t['id_transfers_fluz'];
                    $date_add = $row_t['date_add'];
                    
                    Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                            . "                          VALUES ('2', " . (int) $network['id_sponsor'] . ", 0,NULL,'0','0'," . $network['amount'] . ",'loyalty','TransferFluzBusiness','" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "', " . (int) $id_transfer . ")");
                    
                    $customer_send = new Customer($network['id_sponsor']);
                    $total_paid = round(RewardsModel::getMoneyReadyForDisplay($network['amount'], 1));
                    
                    $data = array(
                    '{username}' => $customer_send->username,
                    '{username_send}' => $this->context->customer->username,
                    '{date}' => $date_add,
                    '{total_points_granted}'=> $network['amount'],
                    '{total_paid}' => Tools::displayPrice($total_paid,1, false),
                    );

                    $template = 'send_free_fluz';
                    $prefix_template = '16-send_free_fluz';

                    $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
                    $row_subject = Db::getInstance()->getRow($query_subject);
                    $message_subject = $row_subject['subject_mail'];

                    $allinone_rewards = new allinone_rewards();
                    $allinone_rewards->sendMail(1, $template, $allinone_rewards->getL($message_subject), $data, $customer_send->email, $customer_send->firstname.' '.$customer_send->lastname);
                   }
                  }
                  die('success');
                }
                die(true);
                break;    
            case 'editFLuz':
                $pto_total = Tools::getValue('ptosTotal');
                $pointsTotal = RewardsModel::getRewardReadyForDisplay($pto_total, $this->context->currency->id);
                
                $query_credits = "SELECT r.id_customer AS id_customer, SUM(r.credits) AS total_credits FROM "._DB_PREFIX_."rewards AS r WHERE r.id_reward_state=2 AND r.id_customer=".(int)$this->context->customer->id." GROUP BY r.id_customer";
                $row_credits = Db::getInstance()->getRow($query_credits);
                
                if($row_credits['total_credits']>=$pointsTotal){
                
                Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "transfers_fluz (id_customer, id_sponsor_received, date_add)
                                            VALUES (" . (int) $this->context->customer->id . ", 0,'" . date("Y-m-d H:i:s") . "')");

                $query_t = 'SELECT id_transfers_fluz FROM ' . _DB_PREFIX_ . 'transfers_fluz WHERE id_customer=' . (int) $this->context->customer->id . ' ORDER BY id_transfers_fluz DESC';
                $row_t = Db::getInstance()->getRow($query_t);
                $id_transfer = $row_t['id_transfers_fluz'];

                Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                        . "                          VALUES ('2', " . (int) $this->context->customer->id . ", 0,NULL,'0','0'," . -1 * $pointsTotal . ",'loyalty', 'TransferFluzBusiness','" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "', " . (int) $id_transfer . ")");
                
                $point_used = Tools::getValue('listEdit');
                $list_var = json_decode($point_used, true);
                
                foreach ($list_var as $network) {
                    $query_t = 'SELECT id_transfers_fluz FROM ' . _DB_PREFIX_ . 'transfers_fluz WHERE id_customer=' . (int) $this->context->customer->id . ' ORDER BY id_transfers_fluz DESC';
                    $row_t = Db::getInstance()->getRow($query_t);
                    $id_transfer = $row_t['id_transfers_fluz'];

                    Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                            . "                          VALUES ('2', " . (int) $network['id_sponsor'] . ", 0,NULL,'0','0'," . $network['amount'] . ",'loyalty','TransferFluzBusiness','" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "', " . (int) $id_transfer . ")");
                
                    $customer_send = new Customer($network['id_sponsor']);
                    $total_paid = round(RewardsModel::getMoneyReadyForDisplay($network['amount'], 1));
                    
                    $data = array(
                    '{username}' => $customer_send->username,
                    '{username_send}' => $this->context->customer->username,
                    '{date}' => $date_add,
                    '{total_points_granted}'=> $network['amount'],
                    '{total_paid}' => Tools::displayPrice($total_paid,1, false),
                    );

                    $template = 'send_free_fluz';
                    $prefix_template = '16-send_free_fluz';

                    $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
                    $row_subject = Db::getInstance()->getRow($query_subject);
                    $message_subject = $row_subject['subject_mail'];

                    $allinone_rewards = new allinone_rewards();
                    $allinone_rewards->sendMail(1, $template, $allinone_rewards->getL($message_subject), $data, $customer_send->email, $customer_send->firstname.' '.$customer_send->lastname);
                }
                    die('success');
                }
                die(true);
                break;
            
            case 'submitcopy':
                
                $listcopy = Tools::getValue('listcopy');
                $list_var_copy = json_decode($listcopy, true);
                $process = false;
                $array_error = array();
                $image_url = "";
                $number_to_import = COUNT($list_var_copy);
            
                if($number_to_import < 150){
                    $template = 'welcome_fluzfluz_business';
                    $prefix_template = '16-welcome_fluzfluz_business';

                    $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
                    $row_subject = Db::getInstance()->getRow($query_subject);
                    $message_subject = $row_subject['subject_mail'];
                    
                    if (isset($_SERVER['HTTPS'])) {
                        $image_url = 'https://'.Configuration::get('PS_SHOP_DOMAIN').'/img/business/'.$this->context->customer->id.'.png'; 
                     }
                    else{
                        $image_url = 'http://'.Configuration::get('PS_SHOP_DOMAIN').'/img/business/'.$this->context->customer->id.'.png'; 
                     }

                    foreach($list_var_copy as $datacustomer){ 
                            ini_set('max_execution_time', 0);
                            $error_csv = "";
                            $valid_dni = Db::getInstance()->getRow('SELECT COUNT(dni) as dni 
                                                        FROM '._DB_PREFIX_.'customer WHERE dni = "'.$datacustomer['cedula'].'" ');

                            $valid_username = Db::getInstance()->getRow('SELECT COUNT(username)  as username 
                                                        FROM '._DB_PREFIX_.'customer WHERE username = "'.$datacustomer['Username'].'" ');

                            $valid_phone = Db::getInstance()->getRow('SELECT COUNT(phone) as phone  
                                                        FROM '._DB_PREFIX_.'customer WHERE phone = "'.$datacustomer['Telefono Empleado'].'" ');
                             
                            if (empty($datacustomer['First Name']) || empty($datacustomer['Last Name']) || !Validate::isName($datacustomer['Last Name']) || !Validate::isName($datacustomer['Last Name'])) {
                                $error_csv['name'] = 'name invalid';
                                $error_csv['name_customer'] = $datacustomer['First Name'];
                            } 
                            else if (!Validate::isEmail($datacustomer['Email'])) {
                                $error_csv['email'] = 'email invalid';
                                $error_csv['email_customer'] = $datacustomer['Email'];
                            } 
                            else if($valid_dni['dni'] > 0){
                                $error_csv['dni_exists'] = 'dni exists';
                                $error_csv['cedula'] = $datacustomer['cedula'];
                            }
                            else if($valid_username['username'] > 0){
                                $error_csv['valid_username'] = 'valid username';
                                $error_csv['username'] = $datacustomer['Username'];
                            }
                            else if($valid_phone['phone'] > 0){
                                $error_csv['valid_phone'] = 'valid phone';
                                $error_csv['phone'] = $datacustomer['Telefono Empleado'];
                            }
                            else if (empty($datacustomer['cedula'])) {
                                $error_csv = 'No se ha ingresado correctamente el campo Cedula';
                            } 
                            elseif (RewardsSponsorshipModel::isEmailExists($datacustomer['Email']) || Customer::customerExists($datacustomer['Email'])) {
                                    $customerKickOut = Db::getInstance()->getValue("SELECT kick_out FROM " . _DB_PREFIX_ . "customer WHERE email = '" . $datacustomer['Email'] . "'");
                                if ($customerKickOut == 0) {
                                    $error_csv['email_exists'] = 'email exists';
                                    $error_csv['email_exists_customer'] = $datacustomer['Email'];
                                }
                            }
                        
                        $code_generate = Allinone_rewardsSponsorshipModuleFrontController::generateIdCodeSponsorship($datacustomer['Username']);
    
                        if ($error_csv == "") {

                            $customer = new Customer();
                            $customer->firstname = $datacustomer['First Name'];
                            $customer->lastname = $datacustomer['Last Name'];
                            $customer->username = $datacustomer['Username'];
                            $customer->dni = $datacustomer['cedula'];
                            $customer->email = $datacustomer['Email'];
                            $customer->passwd = Tools::encrypt($datacustomer['cedula']);
                            $customer->date_kick_out = date('Y-m-d H:i:s', strtotime('+30 day', strtotime(date("Y-m-d H:i:s"))));
                            $customer->date_add = date('Y-m-d H:i:s', strtotime('+30 day', strtotime(date("Y-m-d H:i:s"))));
                            $customer->id_default_group = 4;
                            $customer->id_lang = $this->context->customer->id_lang;
                            $customer->field_work = $this->context->customer->field_work;
                            $customer->phone = $datacustomer['Telefono Empleado'];
                            $customer->method_add = 'Web / Business';
                            $customer->add();
                            
                            Db::getInstance()->execute('INSERT  INTO ps_customer_group (id_customer, id_group)  
                            VALUES ( '.$customer->id.' ,3)');
                            
                            $address = new Address();
                            $address->id_country = 69;
                            $address->dni = $customer->dni;
                            $address->id_customer = $customer->id;
                            $address->alias = 'Mi Direccion';
                            $address->firstname = $customer->firstname;
                            $address->lastname = $customer->lastname;
                            $address->address1 = $datacustomer['Direccion Empleado'];
                            $address->address2 = $datacustomer['Direccion Empleado'];
                            $address->city = $datacustomer['Ciudad'];
                            $address->phone = $datacustomer['Telefono Empleado'];
                            $address->phone_mobile = $datacustomer['Telefono Empleado'];
                            $address->type_document = 0;
                            $address->active = 1;
                            $address->add();

                            $count_array = count($tree);

                            if ($count_array < 2) {

                                $sponsor = Db::getInstance()->getRow("SELECT c.id_customer, c.username, c.firstname, c.lastname, c.email, (2-COUNT(rs.id_sponsorship) ) sponsoships
                                            FROM " . _DB_PREFIX_ . "customer c
                                            LEFT JOIN " . _DB_PREFIX_ . "rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                                            WHERE c.id_customer =" . $this->context->customer->id);

                                if (!empty($sponsor)) {

                                    $array_p['id'] = $customer->id;
                                    array_push($tree, $array_p);

                                    $sponsorship = new RewardsSponsorshipModel();
                                    $sponsorship->id_sponsor = $sponsor['id_customer'];
                                    $sponsorship->id_customer = $customer->id;
                                    $sponsorship->firstname = $datacustomer['First Name'];
                                    $sponsorship->lastname = $datacustomer['Last Name'];
                                    $sponsorship->email = $datacustomer['Email'];
                                    $sponsorship->channel = 1;
                                    $send = "";
                                    
                                    if ($sponsorship->save()) {
                                        $vars = array(
                                        '{username}' => $customer->username,
                                        '{password}' =>  Context::getContext()->link->getPageLink('password', true, null, 'token='.$customer->secure_key.'&id_customer='.(int)$customer->id),                
                                        '{firstname}' => $customer->firstname,
                                        '{lastname}' => $customer->lastname,
                                        '{dni}' => $customer->dni,
                                        '{birthdate}' => $customer->birthday,
                                        '{address}' => $address->address1,
                                        '{phone}' => $datacustomer['Telefono Empleado'],
                                        '{img_business}' => $image_url,
                                        '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                                        '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                                        '{shop_url_personal}' => Context::getContext()->link->getPageLink('identity', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                                        '{learn_more_url}' => "http://reglas.fluzfluz.co",
                                        );
                                        
                                        Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_sponsor, code)
                                                           VALUES ('.$customer->id.', "'.$code_generate.'")');

                                        $allinone_rewards = new allinone_rewards();
                                        $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject),$vars, $sponsorship->email, $customer->firstname.' '.$customer->lastname);
                                    }
                                } 
                                else 
                                {
                                    $error_csv = 'no sponsor';
                                }
                            }
                            else {
                                $array_p['id'] = $customer->id;
                                array_push($tree, $array_p);

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
                                    $sponsorship->firstname = $datacustomer['First Name'];
                                    $sponsorship->lastname = $datacustomer['Last Name'];
                                    $sponsorship->email = $datacustomer['Email'];
                                    $sponsorship->channel = 1;
                                    $send = "";
                                    if ($sponsorship->save()) {
                                        $vars = array(
                                        '{username}' => $customer->username,
                                        '{password}' =>  Context::getContext()->link->getPageLink('password', true, null, 'token='.$customer->secure_key.'&id_customer='.(int)$customer->id),                
                                        '{firstname}' => $customer->firstname,
                                        '{lastname}' => $customer->lastname,
                                        '{dni}' => $customer->dni,
                                        '{birthdate}' => $customer->birthday,
                                        '{address}' => $address->address1,
                                        '{phone}' => $datacustomer['Telefono Empleado'],
                                        '{img_business}' => $image_url,
                                        '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                                        '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                                        '{shop_url_personal}' => Context::getContext()->link->getPageLink('identity', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                                        '{learn_more_url}' => "http://reglas.fluzfluz.co",
                                        );
                                        
                                        Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_sponsor, code)
                                                           VALUES ('.$customer->id.', "'.$code_generate.'")');
                                        
                                        $allinone_rewards = new allinone_rewards();
                                        $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject),$vars, $sponsorship->email, $customer->firstname.' '.$customer->lastname);

                                        }
                                    }
                                }
                            }
                            else{
                                    array_push($array_error, $error_csv); 
                                    //$this->context->smarty->assign('error', $error);
                                }
                                $process = true;
                            }

                        if($array_error[0]==''){
                            array_shift($array_error);
                        }

                        if($process == true){
                                if(empty($array_error)){
                                          die(true);
                                       }
                                else{

                                    $error_csv = $array_error;
                                    $json_error = json_encode($error_csv);
                                    die($json_error);
                                    //$this->context->smarty->assign('error_csv', $error_csv);
                                }       
                            }
                }
                else{
                    $error_csv[0]['csv_number'] = "registro";
                    $json_error = json_encode($error_csv);
                    die($json_error);
                }
                break;
            case 'uploadtransfers':
                
                    $employee_b = Db::getInstance()->executeS('SELECT id_customer, firstname, lastname, phone, email, dni, username FROM ps_customer WHERE field_work = "' . $this->context->customer->field_work . '" AND id_customer !=' . $this->context->customer->id);
                    $net_business = array_merge($tree, $employee_b);
                
                    $list_transfer = Tools::getValue('list_transfer');
                    $list_var_transfer = json_decode($list_transfer, true);
                    $totals = RewardsModel::getAllTotalsByCustomer((int) $this->context->customer->id);
                    $pointsAvailable = round(isset($totals[RewardsStateModel::getValidationId()]) ? (float) $totals[RewardsStateModel::getValidationId()] : 0);
                    $pointsAvailablemoney = $pointsAvailable*25;
                    $sum = 0;
                    $error = true;

                    foreach($list_var_transfer as $datacustomer){ 
                        
                        $q_valid = Db::getInstance()->executeS('SELECT id_customer FROM '._DB_PREFIX_.'customer 
                                    WHERE email = "'.$datacustomer['email'].'" && dni = "'.$datacustomer['cedula'].'"');
                        
                        if(empty($q_valid)){
                            $error = 'Email '.$datacustomer['email'].' o Cedula '.$datacustomer['cedula'].' No Existe en tu red Fluz Fluz Empresa. Por Favor Revisar tu CSV.';
                        }
                        
                        $sum += $datacustomer['montotransferencia'];
                    }
                    if($sum > $pointsAvailablemoney){
                        $error = 'El Dinero disponible no cubre el valor de esta transaccion. Por Favor Revisar tu CSV.';
                    }
                    die($error);
                break;
            case 'kickoutemployee':
                
                    $id_employee = Tools::getValue('id_employee');
                    
                    $expulsion_empresa = Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'customer
                                    SET field_work = NULL, group_business = NULL
                                    WHERE id_customer='.$id_employee);
                    
                    die($id_employee);
                    /*$customer = new Customer($id_employee);
                    $customer->kick_out = 1;
                    $customer->active = 0;
                    $customer->update();
                    require_once(_PS_ROOT_DIR_.'/kickoutcustomers.php');*/
                break;
            default:
                break;
        }
    }
    
    function network(){
        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
        array_shift($tree);
        
        foreach ($tree as &$network) {
            $sql = 'SELECT id_customer, firstname, lastname, phone, username, email, dni, field_work, group_business
                    FROM ' . _DB_PREFIX_ . 'customer 
                    WHERE id_customer =' . $network['id'];
            $row_sql = Db::getInstance()->getRow($sql);

            $network['id_customer'] = $row_sql['id_customer'];
            $network['firstname'] = $row_sql['firstname'];
            $network['lastname'] = $row_sql['lastname'];
            $network['email'] = $row_sql['email'];
            $network['phone'] = $row_sql['phone'];
            $network['dni'] = $row_sql['dni'];
            $network['username'] = $row_sql['username'];
            $network['field_work'] = $row_sql['field_work'];
            $network['group_business'] = $row_sql['group_business'];
        }
        
        $employee_b = Db::getInstance()->executeS('SELECT id_customer, firstname, lastname, phone, email, dni, username, field_work FROM ps_customer WHERE field_work = "' . $this->context->customer->field_work . '" AND id_customer !=' . $this->context->customer->id);
        $net_business = array_merge($tree, $employee_b);
        
        foreach ($net_business as &$val) {
            if ($val['username'] != "" && $val['group_business'] != "" && $val['group_business'] == $this->context->customer->group_business) {
                    $list_business[$val['id_customer']] = $val;
            }      
            else if ($val['username'] != "" && $val['field_work'] != "" && $val['field_work'] == $this->context->customer->field_work) {
                        $list_business[$val['id_customer']] = $val;
                    }
        }
        
        
        $list_business = array_values($list_business);
        
        return $list_business;
    }
    
    function statistics(){
        $statistics = array();
        $network = $this->network();
        
        $ids = "";
        foreach ($network as $customer) {
            $ids = $ids.$customer['id_customer'].",";
        }
        $ids = substr( $ids, 0, -1 );
       
        $fechaingreso = new DateTime($this->context->customer->date_add);
        $fechaactual = new DateTime();
        $diferencia = $fechaingreso->diff($fechaactual);
        $meses = ( $diferencia->y * 12 ) + $diferencia->m + 1;
        $meses = $meses >= 12 ? 12 : $meses;
        
        for ($i = 0 ; $i <= $meses ; $i++) {
            $date_sta = date("Y-m",strtotime("-".$i." month"));
            
            $query = "SELECT DATE_FORMAT(date_add,'%Y-%m') date, COUNT(id_customer) customers
                        FROM "._DB_PREFIX_."customer
                        WHERE id_customer IN (".$ids.")
                        GROUP BY date
                        HAVING date = '".$date_sta."'";
            $customer_qty = Db::getInstance()->getRow($query);
            
            $query = "SELECT DATE_FORMAT(o.date_add,'%Y-%m') date, SUM(r.credits) fluz, SUM(r.credits)*c.value fluzcop
                        FROM "._DB_PREFIX_."orders o
                        INNER JOIN "._DB_PREFIX_."rewards r ON ( o.id_order = r.id_order AND r.id_reward_state = 2 AND r.credits > 0 )
                        LEFT JOIN "._DB_PREFIX_."configuration c ON ( c.name = 'REWARDS_VIRTUAL_VALUE_1' )
                        WHERE o.id_customer IN (".$ids.")
                        AND o.current_state = 2
                        GROUP BY date
                        HAVING date = '".$date_sta."'";
            $fluz = Db::getInstance()->getRow($query);
            
            $query = "SELECT DATE_FORMAT(date_add,'%Y-%m') date, COUNT(id_order) orders
                        FROM "._DB_PREFIX_."orders
                        WHERE id_customer IN (".$ids.")
                        AND current_state = 2
                        GROUP BY date
                        HAVING date = '".$date_sta."'";
            $orders_qty = Db::getInstance()->getRow($query);
            
            $query = "SELECT SUM(od.product_price) total, COUNT(DISTINCT o.id_order) orders, c.name category
                        FROM "._DB_PREFIX_."orders o
                        INNER JOIN "._DB_PREFIX_."order_detail od ON ( o.id_order = od.id_order )
                        INNER JOIN "._DB_PREFIX_."product p ON ( od.product_id = p.id_product )
                        INNER JOIN "._DB_PREFIX_."category_lang c ON ( p.id_category_default = c.id_category AND c.id_lang = 1 )
                        WHERE o.id_customer IN (".$ids.")
                        AND o.current_state = 2
                        AND DATE_FORMAT(o.date_add,'%Y-%m') = '".$date_sta."'
                        GROUP BY category
                        ORDER BY orders DESC
                        LIMIT 10";
            $categories = Db::getInstance()->executeS($query);
            
            $query = "SELECT SUM(od.product_quantity) qty, m.name manufacturer, m.id_manufacturer, c.id_category, c.link_rewrite
                        FROM "._DB_PREFIX_."orders o
                        INNER JOIN "._DB_PREFIX_."order_detail od ON ( o.id_order = od.id_order )
                        INNER JOIN "._DB_PREFIX_."product p ON ( od.product_id = p.id_product )
                        LEFT JOIN "._DB_PREFIX_."manufacturer m ON ( p.id_manufacturer = m.id_manufacturer )
                        LEFT JOIN "._DB_PREFIX_."category_lang c ON ( m.category = c.id_category AND c.id_lang = 1 )
                        WHERE o.id_customer IN (".$ids.")
                        AND o.current_state = 2
                        AND DATE_FORMAT(o.date_add,'%Y-%m') = '".$date_sta."'
                        GROUP BY manufacturer
                        ORDER BY qty DESC
                        LIMIT 5";
            $manufacturers = Db::getInstance()->executeS($query);
            
            $date_arr = explode("-",$date_sta);
            $month = $date_arr[1];
            $year = $date_arr[0];
            switch ($date_arr[1]) {
                case "01": $month = "Enero"; break;
                case "02": $month = "Febrero"; break;
                case "03": $month = "Marzo"; break;
                case "04": $month = "Abril"; break;
                case "05": $month = "Mayo"; break;
                case "06": $month = "Junio"; break;
                case "07": $month = "Julio"; break;
                case "08": $month = "Agosto"; break;
                case "09": $month = "Septiembre"; break;
                case "10": $month = "Octubre"; break;
                case "11": $month = "Noviembre"; break;
                case "12": $month = "Diciembre"; break;
            }
            
            $statistics[$date_sta]['year'] = $year;
            $statistics[$date_sta]['month'] = $month;
            $statistics[$date_sta]['customers'] = $customer_qty['customers']=="" ? 0 : $customer_qty['customers'];
            $statistics[$date_sta]['fluz'] = $fluz['fluz']=="" ? 0 : $fluz['fluz'];
            $statistics[$date_sta]['fluzcop'] = $fluz['fluzcop']=="" ? 0 : $fluz['fluzcop'];
            $statistics[$date_sta]['orders'] = $orders_qty['orders']=="" ? 0 : $orders_qty['orders'];
            $statistics[$date_sta]['categories'] = $categories;
            $statistics[$date_sta]['manufacturers'] = $manufacturers;
        }
        
        $customers_qty_acu = 0;
        $reversed = array_reverse($statistics);
        foreach ($reversed as &$data) {
            $data['customers'] = $customers_qty_acu += $data['customers'];
        }

        $statistics = array_reverse($reversed);        
        return $statistics;
    }
    
    function  history_business(){
        $query_history = 'SELECT tf.id_transfers_fluz as id_transferencia, r.id_customer as id_cliente, c.firstname as nombre, c.lastname as apellido, DATE_FORMAT(tf.date_add, "%d/%m/%Y") as fecha_transferencia, 
                            (SELECT COUNT(r.id_transfer_fluz) FROM ps_rewards r WHERE r.id_transfer_fluz = tf.id_transfers_fluz AND r.reason = "TransferFluzBusiness") AS numero_empleados,
                            r.reason as tipo_transferencia, r.credits as fluz_transferidos , (r.credits * 25) as precio_fluz
                            FROM '._DB_PREFIX_.'transfers_fluz tf
                            LEFT JOIN '._DB_PREFIX_.'rewards r ON(r.id_transfer_fluz = tf.id_transfers_fluz)
                            LEFT JOIN '._DB_PREFIX_.'customer c ON (r.id_customer = c.id_customer)
                            WHERE tf.id_customer = '.$this->context->customer->id.' AND r.reason = "TransferFluzBusiness"';
        $history_transfer = Db::getInstance()->executeS($query_history);
        
        return $history_transfer;
    }
    
    function  history_fluz(){
        $query_fluz = 'SELECT
                            o.id_order,
                            o.reference,
                            o.payment,
                            os.name state,
                            DATE_FORMAT(o.date_add, "%d/%m/%Y") date
                        FROM '._DB_PREFIX_.'orders o
                        INNER JOIN '._DB_PREFIX_.'order_state_lang os ON ( o.current_state = os.id_order_state AND os.id_lang = 1 )
                        LEFT JOIN '._DB_PREFIX_.'configuration c ON ( c.name = "REWARDS_VIRTUAL_VALUE_1" )
                        WHERE o.id_customer = '.$this->context->customer->id.'
                        ORDER BY o.date_add DESC';
        $history_fluz = Db::getInstance()->executeS($query_fluz);
        
        foreach ($history_fluz as &$history) {
            $query = 'SELECT
                            od.product_id,
                            od.product_reference,
                            od.product_name,
                            od.product_quantity,
                            od.product_price,
                            ((od.product_price/c.value)*(rp.value/100)) product_fluz,
                            (od.product_price*od.product_quantity) product_price_total,
                            (((od.product_price/c.value)*(rp.value/100))*od.product_quantity) product_fluz_total
                        FROM '._DB_PREFIX_.'order_detail od
                        INNER JOIN '._DB_PREFIX_.'rewards_product rp ON ( od.product_id = rp.id_product )
                        LEFT JOIN '._DB_PREFIX_.'configuration c ON ( c.name = "REWARDS_VIRTUAL_VALUE_1" )
                        WHERE od.product_reference LIKE "%mfluz%"
                        AND od.id_order = '.$history['id_order'];
            $history['products'] = Db::getInstance()->executeS($query);
            
            $total = 0;
            $total_fluz = 0;
            foreach ($history['products'] as $products) {
                $total += $products['product_price_total'];
                $total_fluz += $products['product_fluz_total'];
            }
            
            $history['total'] = $total;
            $history['total_fluz'] = $total_fluz;
        }
        
        return $history_fluz;
    }
    
    function history_purchase_employee($list_business, $limit){
        
        $array_purchase = array();
        
        foreach ($list_business as $employee){
            
            $array_purchase[ $employee['id_customer'] ] = $employee;
            
            $query_purchase = 'SELECT O.id_order,OD.product_price, O.total_paid, OD.product_name, OD.product_quantity, O.date_add,
                                (SELECT SUM(OD.product_quantity) FROM ps_orders AS O 
                                        LEFT JOIN ps_order_detail AS OD ON (O.id_order = OD.id_order)  
                                        LEFT JOIN ps_product p ON (p.id_product = OD.product_id)
                                        WHERE O.id_customer = '.$employee['id_customer'].' AND O.current_state = 2
                                        AND p.reference NOT LIKE "MFLUZ%") AS sum_quantity,
                                (SELECT SUM(O.total_paid) FROM ps_orders AS O 
                                        LEFT JOIN ps_order_detail AS OD ON (O.id_order = OD.id_order)  
                                        LEFT JOIN ps_product p ON (p.id_product = OD.product_id)
                                        WHERE O.id_customer = '.$employee['id_customer'].' AND O.current_state = 2
                                        AND p.reference NOT LIKE "MFLUZ%") AS sum_total
                                FROM '._DB_PREFIX_.'orders AS O 
                                LEFT JOIN '._DB_PREFIX_.'order_detail AS OD ON (O.id_order = OD.id_order)
                                LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = OD.product_id)
                                WHERE O.id_customer = '.$employee['id_customer'].'
                                AND O.current_state = 2
                                AND p.reference NOT LIKE "MFLUZ%"
                                ORDER BY O.id_order DESC
                                LIMIT '.$limit.'';
            $array_purchase[ $employee['id_customer'] ]['details'] = Db::getInstance()->executeS($query_purchase);
            
            if ( empty($array_purchase[ $employee['id_customer'] ]['details']) ) {
                unset($array_purchase[ $employee['id_customer'] ]);
            }
        }
        
        return $array_purchase; 
    }
            
    function csv_to_array($filename='', $delimiter=';')
    {
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
            {
                if(!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        
        return $data;
    }
    
}

?>
