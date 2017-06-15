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
include_once(_PS_MODULE_DIR_.'allinone_rewards/allinone_rewards.php');
include_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsModel.php');

class businessController extends FrontController
{
    public $auth = true;
    public $ssl = true;
      
    
    public function setMedia(){
       parent::setMedia();
       $this->addCSS(_THEME_CSS_DIR_.'business.css');
    }
    
    public function initContent()
    {
        parent::initContent();
        
        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
        array_shift($tree);
        
        $totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
        $pointsAvailable = round(isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0);
        $this->context->smarty->assign('pointsAvailable', $pointsAvailable);
        
        $total_users = (count($tree)-1);
        $this->context->smarty->assign('all_fluz', $total_users);
        
        foreach ($tree as &$network){
            $sql = 'SELECT id_customer, firstname, lastname, username, email, dni FROM '._DB_PREFIX_.'customer 
                    WHERE id_customer='.$network['id'];
            $row_sql = Db::getInstance()->getRow($sql);
            
            $network['id_customer'] = $row_sql['id_customer'];
            $network['firstname'] = $row_sql['firstname'];
            $network['lastname'] = $row_sql['lastname'];
            $network['email'] = $row_sql['email'];
            $network['dni'] = $row_sql['dni'];
            $network['username'] = $row_sql['username'];
        }
        
        $this->context->smarty->assign('network',$tree);
        
        $this->setTemplate(_PS_THEME_DIR_.'business.tpl');
       
    }
    
    public function postProcess() {
        
        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
        array_shift($tree);
        
        if(Tools::isSubmit('add-employee')){
            
            $error = "";    
            $FirstNameEmployee = Tools::getValue('firstname');
            $LastNameEmployee = Tools::getValue('lastname');
            $EmailEmployee = Tools::getValue('email');
            $passwordDni = Tools::getValue('dni');
            $BusinessEmployee = $this->context->customer->field_work;
            
            if (empty($FirstNameEmployee) || empty($LastNameEmployee) || !Validate::isName($FirstNameEmployee) || !Validate::isName($LastNameEmployee)) {
                $error = 'name invalid';
            } elseif (Tools::isSubmit('submitSponsorFriendsThird') && !Validate::isEmail($EmailEmployee) ) {
                $error = 'email invalid';
            } elseif (RewardsSponsorshipModel::isEmailExists($EmailEmployee) || Customer::customerExists($EmailEmployee)) {
                $customerKickOut = Db::getInstance()->getValue("SELECT kick_out FROM "._DB_PREFIX_."customer WHERE email = '".$EmailEmployee."'");
                if ( $customerKickOut == 0 ) {
                    $error = 'email exists';
                    $mails_exists[] = $EmailEmployee;
                }
            }
            
            if ( $error == "" ) {
                
                $customer = new Customer();
                $customer->firstname = $FirstNameEmployee;
                $customer->lastname = $LastNameEmployee;
                $customer->email = $EmailEmployee;
                $customer->field_work = $BusinessEmployee;
                $customer->passwd = $passwordDni;
                $customer->dni = $passwordDni;
                $customer->username = 'prueba';
                $customer->id_default_group = $this->context->customer->id_default_group;
                $customer->id_lang = $this->context->customer->id_lang;
                
                $customer->add();
                $customer->save();
                
                $count_array = count($tree);
                
                if ($count_array < 2){
                        $sponsor = Db::getInstance()->getRow("SELECT c.id_customer, c.username, c.firstname, c.lastname, c.email, (2-COUNT(rs.id_sponsorship) ) sponsoships
                                FROM "._DB_PREFIX_."customer c
                                LEFT JOIN "._DB_PREFIX_."rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                                WHERE c.id_customer =".$this->context->customer->id);
                }
                else {
                        $sponsor = Db::getInstance()->getRow("SELECT c.id_customer, c.username, c.firstname, c.lastname, c.email, (2-COUNT(rs.id_sponsorship) ) sponsoships
                                FROM "._DB_PREFIX_."customer c
                                LEFT JOIN "._DB_PREFIX_."rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                                WHERE c.id_customer = 10");
                }
                
                if ( !empty($sponsor) ) {
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
                            '{firstname_invited}'=> $sponsorship->firstname,
                            '{inviter_username}' => $sponsor['username'],
                            '{username}' => $sponsor['username'],
                            '{lastname}' => $sponsor['lastname'],
                            '{firstname}' => $sponsor['firstname'],
                            '{email_friend}' => $sponsorship->email,
                            '{Expiration}'=> $send,
                            '{link}' => $sponsorship->getSponsorshipMailLink()
                        );
                        
                        $template = 'sponsorship-invitation-novoucher';
                        $allinone_rewards = new allinone_rewards();
                        $allinone_rewards->sendMail((int)$this->context->language->id, $template, 'Invitacion de su amigo', $vars, 'daniel.gonzalez@ingeniocontenido.co', $sponsorship->firstname.' '.$sponsorship->lastname);
                        /*Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."rewards_sponsorship_third(id_customer,id_rewards_sponsorship,email_third,date_add)
                                                    VALUES (".$this->context->customer->id.",".$sponsorship->id.",'".$sponsorship->email."',NOW())");*/
                        $invitation_sent = true;
                    }
                } else {
                    $error = 'no sponsor';
                }
            }
        }
        
        switch ( Tools::getValue('action') ) {
            case 'allFLuz':
                
                $point_used = Tools::getValue('ptoUsed');
                $points_distribute = Tools::getValue('ptoDistribute');
                Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."transfers_fluz (id_customer, id_sponsor_received, date_add)
                                            VALUES (".(int)$this->context->customer->id.", 0,'".date("Y-m-d H:i:s")."')");
                
                $query_t = 'SELECT id_transfers_fluz FROM '._DB_PREFIX_.'transfers_fluz WHERE id_customer='.(int)$this->context->customer->id.' ORDER BY id_transfers_fluz DESC';
                $row_t = Db::getInstance()->getRow($query_t);
                $id_transfer = $row_t['id_transfers_fluz'];
                
                Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                                    . "                          VALUES ('2', ".(int)$this->context->customer->id.", 0,NULL,'0','0',".-1*$point_used.",'loyalty', 'TransferFluz','".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', ".(int)$id_transfer.")");
                
                foreach ($tree as $network)
                {
                    $query_t = 'SELECT id_transfers_fluz FROM '._DB_PREFIX_.'transfers_fluz WHERE id_customer='.(int)$this->context->customer->id.' ORDER BY id_transfers_fluz DESC';
                    $row_t = Db::getInstance()->getRow($query_t);
                    $id_transfer = $row_t['id_transfers_fluz'];
                    
                    Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                                    . "                          VALUES ('2', ".(int)$network['id'].", 0,NULL,'0','0',".$points_distribute.",'loyalty','TransferFluzBusiness','".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', ".(int)$id_transfer.")");
                }
                break;
                
                case 'editFLuz':
                
                $pointsTotal = Tools::getValue('ptosTotal');    
                
                Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."transfers_fluz (id_customer, id_sponsor_received, date_add)
                                            VALUES (".(int)$this->context->customer->id.", 0,'".date("Y-m-d H:i:s")."')");
                
                $query_t = 'SELECT id_transfers_fluz FROM '._DB_PREFIX_.'transfers_fluz WHERE id_customer='.(int)$this->context->customer->id.' ORDER BY id_transfers_fluz DESC';
                $row_t = Db::getInstance()->getRow($query_t);
                $id_transfer = $row_t['id_transfers_fluz'];
                
                Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                                    . "                          VALUES ('2', ".(int)$this->context->customer->id.", 0,NULL,'0','0',".-1*$pointsTotal.",'loyalty', 'TransferFluzBusiness','".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', ".(int)$id_transfer.")");
                    
                $point_used = Tools::getValue('listEdit');
                $list_var = json_decode($point_used, true);
                
                
                foreach ($list_var as $network)
                {
                    $query_t = 'SELECT id_transfers_fluz FROM '._DB_PREFIX_.'transfers_fluz WHERE id_customer='.(int)$this->context->customer->id.' ORDER BY id_transfers_fluz DESC';
                    $row_t = Db::getInstance()->getRow($query_t);
                    $id_transfer = $row_t['id_transfers_fluz'];
                    
                    Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."rewards (id_reward_state, id_customer, id_order, id_cart, id_cart_rule, id_payment, credits, plugin, reason, date_add, date_upd, id_transfer_fluz)"
                                    . "                          VALUES ('2', ".(int)$network['id_sponsor'].", 0,NULL,'0','0',".$network['amount'].",'loyalty','TransferFluzBusiness','".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', ".(int)$id_transfer.")");
                }
                break;
                
            default:
                break;
        }
    }
}
?>