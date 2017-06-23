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
        
        $totals = RewardsModel::getAllTotalsByCustomer((int) $this->context->customer->id);
        $pointsAvailable = round(isset($totals[RewardsStateModel::getValidationId()]) ? (float) $totals[RewardsStateModel::getValidationId()] : 0);
        $this->context->smarty->assign('pointsAvailable', $pointsAvailable);

        $total_users = (count($tree) - 1);
        $this->context->smarty->assign('all_fluz', $total_users);

        foreach ($tree as &$network) {
            $sql = 'SELECT id_customer, firstname, lastname, username, email, dni FROM ' . _DB_PREFIX_ . 'customer 
                    WHERE id_customer =' . $network['id'];
            $row_sql = Db::getInstance()->getRow($sql);

            $network['id_customer'] = $row_sql['id_customer'];
            $network['firstname'] = $row_sql['firstname'];
            $network['lastname'] = $row_sql['lastname'];
            $network['email'] = $row_sql['email'];
            $network['dni'] = $row_sql['dni'];
            $network['username'] = $row_sql['username'];
        }

        $employee_b = Db::getInstance()->executeS('SELECT id_customer, firstname, lastname, email, dni, username FROM ps_customer WHERE field_work = "' . $this->context->customer->field_work . '" AND id_customer !=' . $this->context->customer->id);
        $net_business = array_merge($tree, $employee_b);

        foreach ($net_business as $val) {
            $list_business[$val['id_customer']] = $val;
        }
        $list_business = array_values($list_business);

        $this->context->smarty->assign('network', $list_business);
        $this->setTemplate(_PS_THEME_DIR_ . 'business.tpl');
    }

    public function postProcess() {

        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
        array_shift($tree);
        
        if (Tools::isSubmit('add-employee')) {

            $error = "";
            $FirstNameEmployee = Tools::getValue('firstname');
            $LastNameEmployee = Tools::getValue('lastname');
            $EmailEmployee = Tools::getValue('email');
            $passwordDni = Tools::getValue('dni');
            $point_used_add = Tools::getValue('ptosusedhiddenadde');


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
                        . "                          VALUES ('2', " . (int) $this->context->customer->id . ", 0,NULL,'0','0'," . -1 * $point_used_add . ",'loyalty', 'TransferFluz','" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "', " . (int) $id_transfer . ")");

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
                            $allinone_rewards->sendMail((int) $this->context->language->id, $template, 'Invitacion de su amigo', $vars, 'daniel.gonzalez@ingeniocontenido.co', $sponsorship->firstname . ' ' . $sponsorship->lastname);
                            /* Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."rewards_sponsorship_third(id_customer,id_rewards_sponsorship,email_third,date_add)
                              VALUES (".$this->context->customer->id.",".$sponsorship->id.",'".$sponsorship->email."',NOW())"); */
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
                                '{email}' => $sponsor['id_customer'],
                                '{firstname_invited}' => $sponsorship->firstname,
                                '{inviter_username}' => $sponsor_a['username'],
                                '{username}' => $sponsor_a['username'],
                                '{lastname}' => $sponsor_a['lastname'],
                                '{firstname}' => $sponsor_a['firstname'],
                                '{email_friend}' => $sponsorship->email,
                                '{Expiration}' => $send,
                                '{link}' => $sponsorship->getSponsorshipMailLink()
                            );

                            $template = 'sponsorship-invitation-novoucher';
                            $allinone_rewards = new allinone_rewards();
                            $allinone_rewards->sendMail((int) $this->context->language->id, $template, 'Invitacion de su amigo', $vars, 'daniel.gonzalez@ingeniocontenido.co', $sponsorship->firstname . ' ' . $sponsorship->lastname);
                            /* Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."rewards_sponsorship_third(id_customer,id_rewards_sponsorship,email_third,date_add)
                              VALUES (".$this->context->customer->id.",".$sponsorship->id.",'".$sponsorship->email."',NOW())"); */
                            $invitation_sent = true;
                        }
                    }
                }
                Tools::redirect($this->context->link->getPageLink('business', true));
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
                elseif (empty($datacustomer['DNI'])) {
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
                $customer->dni = $datacustomer['DNI'];
                $customer->email = $datacustomer['Email'];
                $customer->passwd = Tools::encrypt($datacustomer['DNI']);
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
                            $allinone_rewards->sendMail((int) $this->context->language->id, $template, 'Invitacion de su amigo', $vars, 'daniel.gonzalez@ingeniocontenido.co', $sponsorship->firstname . ' ' . $sponsorship->lastname);
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
                                '{email}' => $sponsor['id_customer'],
                                '{firstname_invited}' => $sponsorship->firstname,
                                '{inviter_username}' => $sponsor_a['username'],
                                '{username}' => $sponsor_a['username'],
                                '{lastname}' => $sponsor_a['lastname'],
                                '{firstname}' => $sponsor_a['firstname'],
                                '{email_friend}' => $sponsorship->email,
                                '{Expiration}' => $send,
                                '{link}' => $sponsorship->getSponsorshipMailLink()
                            );

                            $template = 'sponsorship-invitation-novoucher';
                            $allinone_rewards = new allinone_rewards();
                            $allinone_rewards->sendMail((int) $this->context->language->id, $template, 'Invitacion de su amigo', $vars, 'daniel.gonzalez@ingeniocontenido.co', $sponsorship->firstname . ' ' . $sponsorship->lastname);
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
            //Tools::redirect($this->context->link->getPageLink('business', true));
        }
        
        switch (Tools::getValue('action')) {
            case 'allFLuz':

                $point_used = Tools::getValue('ptoUsed');
                $points_distribute = Tools::getValue('ptoDistribute');
                Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "transfers_fluz (id_customer, id_sponsor_received, date_add)
                                            VALUES (" . (int) $this->context->customer->id . ", 0,'" . date("Y-m-d H:i:s") . "')");

                $query_t = 'SELECT id_transfers_fluz FROM ' . _DB_PREFIX_ . 'transfers_fluz WHERE id_customer=' . (int) $this->context->customer->id . ' ORDER BY id_transfers_fluz DESC';
                $row_t = Db::getInstance()->getRow($query_t);
                $id_transfer = $row_t['id_transfers_fluz'];

                Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                        . "                          VALUES ('2', " . (int) $this->context->customer->id . ", 0,NULL,'0','0'," . -1 * $point_used . ",'loyalty', 'TransferFluz','" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "', " . (int) $id_transfer . ")");

                $employee_b = Db::getInstance()->executeS('SELECT id_customer as id, firstname, lastname, email, dni, username FROM ps_customer WHERE field_work = "' . $this->context->customer->field_work . '" AND id_customer !=' . $this->context->customer->id);
                $net_business = array_merge($tree, $employee_b);

                foreach ($net_business as $val) {
                    $list_business[$val['id']] = $val;
                }
                $list_business = array_values($list_business);

                foreach ($list_business as $network) {
                    $query_t = 'SELECT id_transfers_fluz FROM ' . _DB_PREFIX_ . 'transfers_fluz WHERE id_customer=' . (int) $this->context->customer->id . ' ORDER BY id_transfers_fluz DESC';
                    $row_t = Db::getInstance()->getRow($query_t);
                    $id_transfer = $row_t['id_transfers_fluz'];

                    Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                            . "                          VALUES ('2', " . (int) $network['id'] . ", 0,NULL,'0','0'," . $points_distribute . ",'loyalty','TransferFluzBusiness','" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "', " . (int) $id_transfer . ")");
                }
                break;

            case 'editFLuz':
                $pointsTotal = Tools::getValue('ptosTotal');

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
                
                case 'copycustomer':
                    
                $listcopy = Tools::getValue('listcopy');
                $list_var = json_decode($listcopy, true);
                $array_list = explode("\n", $list_var);
                
                print_r($array_list);
                die();
                    
                break;

            default:
                break;
        }
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