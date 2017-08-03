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

        foreach ($tree as &$network) {
            $sql = 'SELECT id_customer, firstname, lastname, phone, username, email, dni 
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
        }

        $employee_b = Db::getInstance()->executeS('SELECT id_customer, firstname, lastname, phone, email, dni, username FROM ps_customer WHERE field_work = "' . $this->context->customer->field_work . '" AND id_customer !=' . $this->context->customer->id);
        
        $net_business = array_merge($tree, $employee_b);
        
        foreach ($net_business as $val) {
            if ($val['username'] != "" ) {
                    $list_business[$val['id_customer']] = $val;
                }      
        }
        $list_business = array_values($list_business);
        
        $total_users = (count($list_business));
        $this->context->smarty->assign('all_fluz', $total_users);
        $this->context->smarty->assign('network', $list_business);
        
        /* Funciones Historial de Transferencias */
        
        $history_transfer = $this->history_business();
        $this->context->smarty->assign('history_transfer', $history_transfer);
        
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
        
        if (Tools::isSubmit('add-employee')) {
            $error = "";
            $image_url = "";    
            $FirstNameEmployee = Tools::getValue('firstname');
            $LastNameEmployee = Tools::getValue('lastname');
            $EmailEmployee = Tools::getValue('email');
            $passwordDni = Tools::getValue('dni');
            $point_used_add = Tools::getValue('ptosusedhiddenadde');
            $phone_user = Tools::getValue('phone_invoice');
            
            if (isset($_SERVER['HTTPS'])) {
               $image_url = 'https://'.Configuration::get('PS_SHOP_DOMAIN').'/img/business/'.$this->context->customer->id.'.png'; 
            }
            else{
               $image_url = 'http://'.Configuration::get('PS_SHOP_DOMAIN').'/img/business/'.$this->context->customer->id.'.png'; 
            }

            if (empty($FirstNameEmployee) || empty($LastNameEmployee) || !Validate::isName($FirstNameEmployee) || !Validate::isName($LastNameEmployee)) {
                $error = 'name invalid';
            } elseif (Tools::isSubmit('add-employee') && !Validate::isEmail($EmailEmployee)) {
                $error = 'email invalid';
            } elseif (empty($passwordDni)) {
                $error = 'No se ha ingresado correctamente el campo Cedula';
            } elseif (empty($point_used_add)) {
                $error = 'No se ha ingresado correctamente el campo Amount';
            } elseif (RewardsSponsorshipModel::isEmailExists($EmailEmployee) || Customer::customerExists($EmailEmployee)) {
                $customerKickOut = Db::getInstance()->getValue("SELECT kick_out FROM " . _DB_PREFIX_ . "customer WHERE email = '" . $EmailEmployee . "'");
                if ($customerKickOut == 0) {
                    $error = 'email exists';
                    $mails_exists[] = $EmailEmployee;
                }
            }

            if ($error == "") {

                $customer = new Customer();
                $customer->firstname = $FirstNameEmployee;
                $customer->lastname = $LastNameEmployee;
                $customer->email = $EmailEmployee;
                $customer->passwd = Tools::encrypt($passwordDni);
                $customer->dni = $passwordDni;
                $customer->username = "$FirstNameEmployee" . "$LastNameEmployee";
                $customer->id_default_group = $this->context->customer->id_default_group;
                $customer->id_lang = $this->context->customer->id_lang;
                $customer->field_work = $this->context->customer->field_work;
                $customer->date_kick_out = date('Y-m-d H:i:s', strtotime('+30 day', strtotime(date("Y-m-d H:i:s"))));
                $customer->add();

                Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "transfers_fluz (id_customer, id_sponsor_received, date_add)
                                            VALUES (" . (int) $this->context->customer->id . ", 0,'" . date("Y-m-d H:i:s") . "')");

                $query_t = 'SELECT id_transfers_fluz FROM ' . _DB_PREFIX_ . 'transfers_fluz WHERE id_customer=' . (int) $this->context->customer->id . ' ORDER BY id_transfers_fluz DESC';
                $row_t = Db::getInstance()->getRow($query_t);
                $id_transfer = $row_t['id_transfers_fluz'];

                Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                        . "                          VALUES ('2', " . (int) $this->context->customer->id . ", 0,NULL,'0','0'," . -1 * $point_used_add . ",'loyalty', 'TransferFluzBusiness','" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "', " . (int) $id_transfer . ")");

                Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                        . "                          VALUES ('2', " . (int) $customer->id . ", 0,NULL,'0','0'," . $point_used_add . ",'loyalty','TransferFluzBusiness','" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "', " . (int) $id_transfer . ")");

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
                                '{password}' => $passwordDni,
                                '{firstname}' => $customer->firstname,
                                '{lastname}' => $customer->lastname,
                                '{dni}' => $customer->dni,
                                '{birthdate}' => $customer->birthday,
                                '{address}' => 'No Disponible',
                                '{phone}' => $phone_user,
                                '{img_business}' => $image_url,    
                                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                                '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                                '{shop_url_personal}' => Context::getContext()->link->getPageLink('identity', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                                '{learn_more_url}' => "http://reglas.fluzfluz.co",
                            );

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
                        $error = 'no sponsor';
                    }
                } else {
                    $array_sponsor = array();
                    foreach ($tree as $network) {
                        $sponsor = Db::getInstance()->getRow("SELECT c.id_customer, c.username, c.firstname, c.lastname, c.email, (2-COUNT(rs.id_sponsorship) ) sponsoships
                                FROM " . _DB_PREFIX_ . "customer c
                                LEFT JOIN " . _DB_PREFIX_ . "rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                                WHERE c.id_customer =" . (int) $network['id'] . "
                                HAVING sponsoships > 0");

                        array_push($array_sponsor, $sponsor);
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
                            '{password}' => $passwordDni,
                            '{firstname}' => $customer->firstname,
                            '{lastname}' => $customer->lastname,
                            '{dni}' => $customer->dni,
                            '{birthdate}' => $customer->birthday,
                            '{address}' => 'No Disponible',
                            '{phone}' => $phone_user,
                            '{img_business}' => $image_url,
                            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                            '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                            '{shop_url_personal}' => Context::getContext()->link->getPageLink('identity', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                            '{learn_more_url}' => "http://reglas.fluzfluz.co",
                        );
                        
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
            //Tools::redirect($this->context->link->getPageLink('business', true));
        }
        
        if (Tools::isSubmit('upload-employee')) {
            
            if ( isset($_POST["upload-employee"]) ) {
                
                if ( isset($_FILES["file"])) {
                    
                //if there was an error uploading the file
                if ($_FILES["file"]["error"] > 0) {
                     echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
                }
                else {

                     //if file already exists
                     if (file_exists("csvcustomer/" . $_FILES["file"]["name"])) {
                       $error = "already exists";
                       $this->context->smarty->assign('error', $error);
                       $this->context->smarty->assign('csv', $_FILES["file"]["name"]);
                     }
                     else {
                       //Store file in directory "upload" with the name of "uploaded_file.txt"
                       $storagename = $_FILES["file"]["name"];
                       move_uploaded_file($_FILES["file"]["tmp_name"], "csvcustomer/" . $storagename);
                       //echo "Stored in: " . "upload/" . $_FILES["file"]["name"] . "<br />";
                    }
                }
             } 
                else {
                     echo "No file selected <br />";
                }
            }
            
            $filename = "csvcustomer/" . $storagename;
            $list_customer = $this->csv_to_array($filename);
            $process = false;
            foreach($list_customer as $datacustomer){
                
                $error = "";
                if (empty($datacustomer['First Name']) || empty($datacustomer['Last Name']) || !Validate::isName($datacustomer['Last Name']) || !Validate::isName($datacustomer['Last Name'])) {
                    $error = 'name invalid';
                    unlink($filename);
                } 
                elseif (Tools::isSubmit('upload-employee') && !Validate::isEmail($datacustomer['Email'])) {
                    $error = 'email invalid';
                    unlink($filename);
                } 
                elseif (empty($datacustomer['cedula'])) {
                    $error = 'No se ha ingresado correctamente el campo Cedula';
                    unlink($filename);
                } 
                elseif (RewardsSponsorshipModel::isEmailExists($datacustomer['Email']) || Customer::customerExists($datacustomer['Email'])) {
                        $customerKickOut = Db::getInstance()->getValue("SELECT kick_out FROM " . _DB_PREFIX_ . "customer WHERE email = '" . $datacustomer['Email'] . "'");
                    if ($customerKickOut == 0) {
                        $error = 'email exists';
                        $mails_exists[] = $datacustomer['Email'];
                        $this->context->smarty->assign('email',$datacustomer['Email']);
                        unlink($filename);
                    }
                }
                
            if ($error == "") {
                
                $customer = new Customer();
                $customer->firstname = $datacustomer['First Name'];
                $customer->lastname = $datacustomer['Last Name'];
                //$customer->active = $datacustomer['Active (0/1)'];
                $customer->username = $datacustomer['Username'];
                //$customer->id_gender = $datacustomer['Titles ID (Mr=1 , Ms=2)'];
                $customer->dni = $datacustomer['cedula'];
                $customer->email = $datacustomer['Email'];
                $customer->passwd = Tools::encrypt($datacustomer['cedula']);
                $customer->date_kick_out = date('Y-m-d H:i:s', strtotime('+30 day', strtotime(date("Y-m-d H:i:s"))));
                //$customer->newsletter = $datacustomer['Newsletter (0/1)'];
                $customer->birthday = $datacustomer['Birthday (yyyy-mm-dd)'];
                $customer->id_default_group = $this->context->customer->id_default_group;
                $customer->id_lang = $this->context->customer->id_lang;
                $customer->field_work = $this->context->customer->field_work;
                
                $customer->add();
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
                        $sponsorship->firstname = $datacustomer['First Name'];
                        $sponsorship->lastname = $datacustomer['Last Name'];
                        $sponsorship->email = $datacustomer['Email'];
                        $sponsorship->channel = 1;
                        $send = "";
                        if ($sponsorship->save()) {
                            $vars = array(
                            '{username}' => $customer->username,
                            '{password}' => $passwordDni,
                            '{firstname}' => $customer->firstname,
                            '{lastname}' => $customer->lastname,
                            '{dni}' => $customer->dni,
                            '{birthdate}' => $customer->birthday,
                            '{address}' => 'No Disponible',
                            '{phone}' => $datacustomer['Phone Mobile'],
                            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                            '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                            '{shop_url_personal}' => Context::getContext()->link->getPageLink('identity', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                            '{learn_more_url}' => "http://reglas.fluzfluz.co",
                            );

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
                    else 
                    {
                        $error = 'no sponsor';
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

                        array_push($array_sponsor, $sponsor);
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
                            '{password}' => $passwordDni,
                            '{firstname}' => $customer->firstname,
                            '{lastname}' => $customer->lastname,
                            '{dni}' => $customer->dni,
                            '{birthdate}' => $customer->birthday,
                            '{address}' => 'No Disponible',
                            '{phone}' => $datacustomer['Phone Mobile'],
                            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                            '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                            '{shop_url_personal}' => Context::getContext()->link->getPageLink('identity', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                            '{learn_more_url}' => "http://reglas.fluzfluz.co",
                            );

                            AuthController::sendNotificationSponsor($customer->id);

                            $template = 'welcome_fluzfluz';
                            $prefix_template = '16-welcome_fluzfluz';

                            $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
                            $row_subject = Db::getInstance()->getRow($query_subject);
                            $message_subject = $row_subject['subject_mail'];

                            $allinone_rewards = new allinone_rewards();
                            $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject),$vars, $sponsorship->email, $customer->firstname.' '.$customer->lastname);
                            
                            $invitation_sent = true;
                            }
                        }
                    }
                           //Tools::redirect($this->context->link->getPageLink('confirmtransfercustomer', true));
                }
                else{
                           $this->context->smarty->assign('error', $error);
                }
                $process = true;
            }
                if($process == 'true'){
                  Tools::redirect($this->context->link->getPageLink('confirmtransfercustomer', true));
                }
        }
        
        switch (Tools::getValue('action')) {
            case 'allFLuz':
                $pointUsed = Tools::getValue('ptoUsed');
                $point_used = RewardsModel::getRewardReadyForDisplay($pointUsed, $this->context->currency->id);
                
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
                    $query_t = 'SELECT id_transfers_fluz FROM ' . _DB_PREFIX_ . 'transfers_fluz WHERE id_customer=' . (int) $this->context->customer->id . ' ORDER BY id_transfers_fluz DESC';
                    $row_t = Db::getInstance()->getRow($query_t);
                    $id_transfer = $row_t['id_transfers_fluz'];

                    Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                            . "                          VALUES ('2', " . (int) $network['id_sponsor'] . ", 0,NULL,'0','0'," . $network['amount'] . ",'loyalty','TransferFluzBusiness','" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "', " . (int) $id_transfer . ")");
                }
                break;    
            case 'editFLuz':
                $pto_total = Tools::getValue('ptosTotal');
                $pointsTotal = RewardsModel::getRewardReadyForDisplay($pto_total, $this->context->currency->id);
                
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
                }
                break;
            
            case 'submitcopy':
                $listcopy = Tools::getValue('listcopy');
                $list_var_copy = json_decode($listcopy, true);
                
                foreach($list_var_copy as $datacustomer){ 

                        $error = "";
                        
                        if (empty($datacustomer['First Name']) || empty($datacustomer['Last Name']) || !Validate::isName($datacustomer['Last Name']) || !Validate::isName($datacustomer['Last Name'])) {
                            $error = 'name invalid';
                            $this->context->smarty->assign('error', $error);
                        } 
                        elseif (Tools::isSubmit('upload-employee') && !Validate::isEmail($datacustomer['Email'])) {
                            $error = 'email invalid';
                            $this->context->smarty->assign('error', $error);
                        } 
                        elseif (empty($datacustomer['cedula'])) {
                            $error = 'No se ha ingresado correctamente el campo Cedula';
                            $this->context->smarty->assign('error', $error);
                        } 
                        elseif (RewardsSponsorshipModel::isEmailExists($datacustomer['Email']) || Customer::customerExists($datacustomer['Email'])) {
                                $customerKickOut = Db::getInstance()->getValue("SELECT kick_out FROM " . _DB_PREFIX_ . "customer WHERE email = '" . $datacustomer['Email'] . "'");
                            if ($customerKickOut == 0) {
                                $error = 'email exists';
                                $mails_exists[] = $datacustomer['Email'];
                                $this->context->smarty->assign('email',$datacustomer['Email']);
                            }
                        }

                    if ($error == "") {

                        $customer = new Customer();
                        $customer->firstname = $datacustomer['First Name'];
                        $customer->lastname = $datacustomer['Last Name'];
                        //$customer->active = $datacustomer['Active (0/1)'];
                        $customer->username = $datacustomer['Username'];
                        //$customer->id_gender = $datacustomer['Titles ID (Mr=1 , Ms=2)'];
                        $customer->dni = $datacustomer['cedula'];
                        $customer->email = $datacustomer['Email'];
                        $customer->passwd = Tools::encrypt($datacustomer['cedula']);
                        $customer->date_kick_out = date('Y-m-d H:i:s', strtotime('+30 day', strtotime(date("Y-m-d H:i:s"))));
                        //$customer->newsletter = $datacustomer['Newsletter (0/1)'];
                        //$customer->birthday = $datacustomer['Birthday (yyyy-mm-dd)'];
                        $customer->id_default_group = $this->context->customer->id_default_group;
                        $customer->id_lang = $this->context->customer->id_lang;
                        $customer->field_work = $this->context->customer->field_work;

                        $customer->add();

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
                                $sponsorship->firstname = $datacustomer['First Name'];
                                $sponsorship->lastname = $datacustomer['Last Name'];
                                $sponsorship->email = $datacustomer['Email'];
                                $sponsorship->channel = 1;
                                $send = "";
                                if ($sponsorship->save()) {
                                    $vars = array(
                                        '{email}' => $sponsor['id_customer'],
                                        '{firstname_invited}' => $sponsorship->firstname,
                                        '{inviter_username}' => $sponsor['username'],
                                        '{username}' => $sponsor['username'],
                                        '{lastname}' => $sponsor['lastname'],
                                        '{firstname}' => $sponsor['firstname'],
                                        '{email_friend}' => $sponsorship->email,
                                        '{Expiration}' => $send,
                                        '{link}' => $sponsorship->getSponsorshipMailLink()
                                    );

                                    $template = 'sponsorship-invitation-novoucher';
                                    $allinone_rewards = new allinone_rewards();
                                    //$allinone_rewards->sendMail((int) $this->context->language->id, $template, 'Invitacion de su amigo', $vars, 'daniel.gonzalez@ingeniocontenido.co', $sponsorship->firstname . ' ' . $sponsorship->lastname);
                                    /* Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."rewards_sponsorship_third(id_customer,id_rewards_sponsorship,email_third,date_add)
                                      VALUES (".$this->context->customer->id.",".$sponsorship->id.",'".$sponsorship->email."',NOW())"); */
                                    $invitation_sent = true;
                                }
                            } 
                            else 
                            {
                                $error = 'no sponsor';
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

                                array_push($array_sponsor, $sponsor);
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
                                    '{password}' => $passwordDni,
                                    '{firstname}' => $customer->firstname,
                                    '{lastname}' => $customer->lastname,
                                    '{dni}' => $customer->dni,
                                    '{birthdate}' => $customer->birthday,
                                    '{address}' => 'No Disponible',
                                    '{phone}' => $datacustomer['Phone Mobile'],
                                    '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                                    '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                                    '{shop_url_personal}' => Context::getContext()->link->getPageLink('identity', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                                    '{learn_more_url}' => "http://reglas.fluzfluz.co",
                                    );

                                    $template = 'welcome_fluzfluz_business';
                                    $allinone_rewards = new allinone_rewards();
                                    //$allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject),$vars, 'daniel.gonzalez@ingeniocontenido.co', $customer->firstname.' '.$customer->lastname);

                                    //$allinone_rewards->sendMail((int) $this->context->language->id, $template, 'Invitacion de su amigo', $vars, 'daniel.gonzalez@ingeniocontenido.co', $sponsorship->firstname . ' ' . $sponsorship->lastname);
                                    /* Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."rewards_sponsorship_third(id_customer,id_rewards_sponsorship,email_third,date_add)
                                      VALUES (".$this->context->customer->id.",".$sponsorship->id.",'".$sponsorship->email."',NOW())"); */
                                    $invitation_sent = true;
                                    }
                                }
                            }
                        }
                        else{
                            $this->context->smarty->assign('error', $error);
                        }
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
                    $error = "";

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
                    
                    $customer = new Customer($id_employee);
                    $customer->kick_out = 1;
                    
                    $customer->update();
                break;
            default:
                break;
        }
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
