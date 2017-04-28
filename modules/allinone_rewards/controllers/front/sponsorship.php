<?php
/**
 * All-in-one Rewards Module
 *
 * @category  Prestashop
 * @category  Module
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2015 Yann BONNAILLIE - ByWEB (http://www.prestaplugins.com)
 * @license   Commercial license see license.txt
 * Support by mail  : contact@prestaplugins.com
 * Support on forum : Patanock
 * Support on Skype : Patanock13
 */

class Allinone_rewardsSponsorshipModuleFrontController extends ModuleFrontController
{
	public $content_only = false;
	public $display_header = true;
	public $display_footer = true;
	private $_ajaxCall = false;

	public function init()
	{
		if (!Tools::getValue('checksponsor')) {
			$id_template = (int)MyConf::getIdTemplate('sponsorship', $this->context->customer->id);
			if (!MyConf::get('RSPONSORSHIP_ACTIVE', null, $id_template))
				die('This functionality is not available');

			if (!$this->context->customer->isLogged())
				Tools::redirect('index.php?controller=authentication');
			elseif (!RewardsSponsorshipModel::isCustomerAllowed($this->context->customer))
				Tools::redirect('index');
		}

		if (Tools::getValue('popup') || Tools::getValue('provider')) {
			// allow to not add the javascript at the end causing JS issue (presta 1.6)
			$this->controller_type = 'modulefront';
			$this->content_only = true;
			$this->display_header = false;
			$this->display_footer = false;
			$this->_ajaxCall = true;
		}

		parent::init();
	}

	// allow to not add the javascript at the end causing a loop on the popup when "defer javascript" is activated
	public function display()
	{
		if ($this->_ajaxCall) {
			$html = $this->context->smarty->fetch($this->template);
	        echo trim($html);
	        return true;
		} else
			return parent::display();
	}

	public function setMedia()
	{
		parent::setMedia();
		if (!Tools::getValue('checksponsor')) {
			$this->addJqueryPlugin(array('idTabs'));
		}
	}

        public function generateIdTemporary($email) {
            $idTemporary = '1';
            for ($i = 0; $i < strlen($email); $i++) {
                $idTemporary .= (string) ord($email[$i]);
            }
            return substr($idTemporary, 0, 10);
        }

        /**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$id_template = (int)MyConf::getIdTemplate('sponsorship', $this->context->customer->id);
		$popup = Tools::getValue('popup');
                
                $groupCustomer = 'SELECT id_default_group as afiliado, username as name FROM '._DB_PREFIX_.'customer WHERE id_customer = '.$this->context->customer->id;
                $rowcustomer = Db::getInstance()->getRow($groupCustomer);
                $grupo = $rowcustomer['afiliado'];
                $name = $rowcustomer['name'];

                $this->context->smarty->assign('grupo',$grupo);
                $this->context->smarty->assign('sponsor', $name);
                
		if (Tools::getValue('checksponsor')) {
			$sponsorship = trim(Tools::getValue('sponsorship'));
			$customer_email = trim(Tools::getValue('customer_email'));
			$sponsor = new Customer(RewardsSponsorshipModel::decodeSponsorshipLink($sponsorship));
			if (Validate::isLoadedObject($sponsor) && RewardsSponsorshipModel::isCustomerAllowed($sponsor) && $sponsor->email != $customer_email) {
				die('{"result":"1"}');
			} else {
				$sponsor = new Customer();
				if (Validate::isEmail($sponsorship)) {
					$sponsor=$sponsor->getByEmail($sponsorship);
					if (Validate::isLoadedObject($sponsor) && RewardsSponsorshipModel::isCustomerAllowed($sponsor) && $sponsor->email != $customer_email){
						die('{"result":"1"}');
					}
				}
			}
			die('{"result":"0"}');
		} else {
			$error = false;

			// get discount value for sponsored (ready to display)
			$nb_discount = (int)MyConf::get('RSPONSORSHIP_QUANTITY_GC', null, $id_template);
			$discount_gc = $this->module->getDiscountReadyForDisplay((int)MyConf::get('RSPONSORSHIP_DISCOUNT_TYPE_GC', null, $id_template), (int)MyConf::get('RSPONSORSHIP_FREESHIPPING_GC', null, $id_template), (float)MyConf::get('RSPONSORSHIP_VOUCHER_VALUE_GC_'.(int)$this->context->currency->id, null, $id_template), null, MyConf::get('RSPONSORSHIP_REAL_VOUCHER_GC', null, $id_template) ? MyConf::get('RSPONSORSHIP_REAL_DESC_GC', (int)$this->context->language->id, $id_template) : null);
			if (MyConf::get('RSPONSORSHIP_REAL_VOUCHER_GC', null, $id_template)) {
				$cart_rule = new CartRule((int)CartRule::getIdByCode(MyConf::get('RSPONSORSHIP_REAL_CODE_GC', null, $id_template)));
				if (Validate::isLoadedObject($cart_rule))
					$nb_discount = $cart_rule->quantity_per_user;
			}

			$template = 'sponsorship-invitation-novoucher';
			if ((int)MyConf::get('RSPONSORSHIP_DISCOUNT_GC', null, $id_template) == 1)
				$template = 'sponsorship-invitation';

			$activeTab = 'sponsor';

			// Mailing invitation to friend sponsor
			$invitation_sent = false;
			$sms_sent = false;
			$hook_sms = Hook::getIdByName('sendsms2Sponsorship');
			$sms_active = Module::isEnabled('sendsms2') && Configuration::get('SENDSMS2_ISACTIVE_'.$hook_sms);

			$nbInvitation = 0;
			$code = RewardsSponsorshipModel::getSponsorshipCode($this->context->customer, true);

			if (Tools::getValue('friendsEmail') && sizeof($friendsEmail = Tools::getValue('friendsEmail')) >= 1)
			{
				$activeTab = 'sponsor';

				$friendsLastName = Tools::getValue('friendsLastName');
				$friendsFirstName = Tools::getValue('friendsFirstName');
				$mails_exists = array();

                                /*if ( !Customer::customerPurchaseLicense($this->context->customer->email) ) {
                                    $error = 'purchase incomplete';
                                }*/

				// 1ere boucle pour contrôle des erreurs
				foreach ($friendsEmail as $key => $friendEmail)
				{
					$friendEmail = $friendEmail;
					$friendLastName = isset($friendsLastName[$key]) ? $friendsLastName[$key] : '';
					$friendFirstName = isset($friendsFirstName[$key]) ? $friendsFirstName[$key] : '';

					if (empty($friendEmail) && empty($friendLastName) && empty($friendFirstName))
						continue;
					elseif (empty($friendEmail) || !Validate::isEmail($friendEmail))
						$error = 'email invalid';
					elseif (Tools::isSubmit('submitSponsorFriends') && (empty($friendFirstName) || empty($friendLastName) || !Validate::isName($friendLastName) || !Validate::isName($friendFirstName)))
						$error = 'name invalid';
					if ($error)
						break;
				}

				if (!$error) {
					// 2ème boucle pour envoie des invitations
					foreach ($friendsEmail as $key => $friendEmail)
					{
						$friendEmail = $friendEmail;
						$friendLastName = isset($friendsLastName[$key]) ? $friendsLastName[$key] : '';
						$friendFirstName = isset($friendsFirstName[$key]) ? $friendsFirstName[$key] : '';

						if (empty($friendEmail) && empty($friendLastName) && empty($friendFirstName))
							continue;

						if (RewardsSponsorshipModel::isEmailExists($friendEmail) || Customer::customerExists($friendEmail)) {
                                                        $customerKickOut = Db::getInstance()->getValue("SELECT kick_out FROM "._DB_PREFIX_."customer WHERE email = '".$friendEmail."'");
                                                        if ( $customerKickOut == 0 ) {
                                                            $error = 'email exists';
                                                            $mails_exists[] = $friendEmail;
                                                            continue;
                                                        }
						}

						$sponsorship = new RewardsSponsorshipModel();
						$sponsorship->id_sponsor = (int)$this->context->customer->id;
						$sponsorship->id_customer = $this->generateIdTemporary($friendEmail);
						$sponsorship->firstname = $friendFirstName;
						$sponsorship->lastname = $friendLastName;
						$sponsorship->channel = 1;
						$sponsorship->email = $friendEmail;
                                                $send = "";
						if ($sponsorship->save()) {
							$vars = array(
								'{message}' => Tools::nl2br(Tools::getValue('message')),
								'{email}' => $this->context->customer->email,
                                                                '{firstname_invited}'=> $sponsorship->firstname,
                                                                '{inviter_username}' => $this->context->customer->username,
                                                                '{username}' => $this->context->customer->username,
								'{lastname}' => $this->context->customer->lastname,
								'{firstname}' => $this->context->customer->firstname,
								'{email_friend}' => $friendEmail,
								'{link}' => $sponsorship->getSponsorshipMailLink(),
                                                                '{Expiration}'=> $send,
								'{nb_discount}' => $nb_discount,
								'{discount}' => $discount_gc);
							$this->module->sendMail((int)$this->context->language->id, $template, $this->module->getL('invitation'), $vars, $friendEmail, $friendFirstName.' '.$friendLastName);
							$invitation_sent = true;
							$nbInvitation++;
							$activeTab = 'pending';
						}
					}
				}
				if ($nbInvitation > 0)
					$_POST = array();
			} else if ($sms_active && Tools::isSubmit('submitSponsorSMS')) {
				$phone = Tools::getValue('phone');
				if (empty($phone) || !Validate::isPhoneNumber($phone))
					$error = 'bad phone';
				else {
					$qry = '
						SELECT count(*)
						FROM `'._DB_PREFIX_.'sendsms_recipient`
						JOIN `'._DB_PREFIX_.'sendsms_campaign` AS sc USING(id_sendsms_campaign)
						WHERE `phone`=\''.pSQL($phone).'\'
						AND `event`=\'sendsms2Sponsorship\'
						AND sc.status=3
						AND TO_DAYS(NOW()) - TO_DAYS(sc.date_send) <= 10';
					$result = Db::getInstance()->getValue($qry);
					if ((int)$result > 0)
						$error = 'sms already sent';
					else {
						// envoi du SMS
						$vars = array('phone' => $phone, 'customer' => $this->context->customer, 'code' => $code);
						if (!Hook::exec('sendsms2Sponsorship', $vars))
							$error = 'sms impossible';
						else
							$sms_sent = true;
					}
				}
			}

			if (!$popup) {
				// Mailing revive
				$revive_sent = false;
				$nbRevive = 0;
				if (Tools::isSubmit('revive'))
				{
					$activeTab = 'pending';
					if (Tools::getValue('friendChecked') && sizeof($friendsChecked = Tools::getValue('friendChecked')) >= 1)
					{
						foreach ($friendsChecked as $key => $friendChecked)
						{       
                                                        $send = "";
							$sponsorship = new RewardsSponsorshipModel((int)$key);
							$vars = array(
								'{email}' => $this->context->customer->email,
                                                                '{firstname_invited}'=> $sponsorship->firstname,
                                                                '{username}' => $this->context->customer->username,
								'{lastname}' => $this->context->customer->lastname,
								'{firstname}' => $this->context->customer->firstname,
								'{email_friend}' => $sponsorship->email,
								'{link}' => $sponsorship->getSponsorshipMailLink(),
                                                                '{Expiration}'=> $send,
								'{nb_discount}' => $nb_discount,
                                                                
								'{discount}' => $discount_gc
							);
							$sponsorship->save();
							$this->module->sendMail((int)$this->context->language->id, $template, $this->module->getL('invitation'), $vars, $sponsorship->email, $sponsorship->firstname.' '.$sponsorship->lastname);
							$revive_sent = true;
							$nbRevive++;
						}
					}
					else
						$error = 'no revive checked';
				}

				$stats = $this->context->customer->getStats();

				$orderQuantityS = (int)MyConf::get('RSPONSORSHIP_ORDER_QUANTITY_S', null, $id_template);

				$canSendInvitations = false;
				if ((int)($stats['nb_orders']) >= $orderQuantityS)
					$canSendInvitations = true;
			}
                        
                        if (!$popup) {
				// Mailing reviveCancel
				
				if (Tools::isSubmit('reviveCancel'))
				{
					$activeTab = 'pending';
					if (Tools::getValue('friendChecked') && sizeof($friendsChecked = Tools::getValue('friendChecked')) >= 1)
					{
                                            foreach ($friendsChecked as $key => $friendChecked)
                                            {
                                                $sponsorship = new RewardsSponsorshipModel((int)$key);
                                                $query = 'DELETE FROM '._DB_PREFIX_.'rewards_sponsorship WHERE email = "'.$sponsorship->email.'"';
                                                Db::getInstance()->execute($query);
                                                
                                                $vars = array(
								'{email}' => $this->context->customer->email,
                                                                '{username}' => $this->context->customer->username,
								'{lastname}' => $sponsorship->lastname,
								'{firstname}' => $sponsorship->firstname,
								'{email_friend}' => $sponsorship->email,
								'{link}' => $sponsorship->getSponsorshipMailLink(),
								'{nb_discount}' => $nb_discount,
								'{discount}' => $discount_gc
							);
                                                Mail::Send(
                                                    (int)$this->context->language->id,
                                                    'invitationCancel',
                                                    'Invitacion Cancelada',
                                                    $vars,
                                                    $sponsorship->email,
                                                    $sponsorship->firstname.' '.$sponsorship->lastname
                                                );
                                                Tools::redirect($this->context->link->getPageLink('cancelinvitation', true, (int)$this->context->language->id));
                                            }
					}
					else
						$error = 'no revive checked';
				}
			}

			// lien de parrainage
			$link_sponsorship = RewardsSponsorshipModel::getSponsorshipLink($this->context->customer);
			$link_sponsorship_fb = $link_sponsorship . '&c=3';
			$link_sponsorship_twitter = $link_sponsorship . '&c=4';
			$link_sponsorship_google = $link_sponsorship . '&c=5';

			// Smarty display
			$smarty_values = array(
				'text' => !$popup ? MyConf::get('RSPONSORSHIP_ACCOUNT_TXT', $this->context->language->id, $id_template) : (Tools::getValue('scheduled') == 1 ? MyConf::get('RSPONSORSHIP_POPUP_TXT', $this->context->language->id, $id_template) : MyConf::get('RSPONSORSHIP_ORDER_TXT', $this->context->language->id, $id_template)),
				'link_sponsorship' => $link_sponsorship,
				'link_sponsorship_fb' => urlencode($link_sponsorship_fb),
				'link_sponsorship_twitter' => urlencode($link_sponsorship_twitter),
				'link_sponsorship_google' => urlencode($link_sponsorship_google),
				'email' => $this->context->customer->email,
				'code' => $code,
				'nbFriends' => (int)MyConf::get('RSPONSORSHIP_NB_FRIENDS', null, $id_template),
				'message' => Tools::getValue('message'),
				'friendsLastName' => Tools::getValue('friendsLastName'),
				'friendsFirstName' => Tools::getValue('friendsFirstName'),
				'friendsEmail' => Tools::getValue('friendsEmail'),
				'error' => $error,
				'invitation_sent' => $invitation_sent,
				'sms_sent' => $sms_sent,
				'nbInvitation' => $nbInvitation,
				'mails_exists' => (isset($mails_exists) ? $mails_exists : array()),
				'rewards_path' => $this->module->getPathUri(),
				'sms' => $sms_active
			);
			$this->context->smarty->assign($smarty_values);

			// si affichage normal, dans le compte du client
			if (!$popup) {
				$statistics = RewardsSponsorshipModel::getStatistics(true);
				$reward_order_allowed = (int)MyConf::get('RSPONSORSHIP_REWARD_ORDER', null, $id_template) || ($statistics['direct_rewards_orders1']+$statistics['direct_rewards_orders2']+$statistics['direct_rewards_orders3']+$statistics['direct_rewards_orders4']+$statistics['direct_rewards_orders5']+$statistics['indirect_rewards']) > 0;
				$reward_registration_allowed = (int)MyConf::get('RSPONSORSHIP_REWARD_REGISTRATION', null, $id_template) || ($statistics['direct_rewards_registrations1']+$statistics['direct_rewards_registrations2']+$statistics['direct_rewards_registrations3']+$statistics['direct_rewards_registrations4']+$statistics['direct_rewards_registrations5']) > 0;

				$params_s = explode(',', MyConf::get('RSPONSORSHIP_REWARD_TYPE_S', null, $id_template));
				$multilevel = count($params_s) > 1 || MyConf::get('RSPONSORSHIP_UNLIMITED_LEVELS', null, $id_template) || (float)$statistics['indirect_rewards'] > 0;
				$smarty_values = array(
					'activeTab' => $activeTab,
					'orderQuantityS' => $orderQuantityS,
					'canSendInvitations' => $canSendInvitations,
					'pendingFriends' => RewardsSponsorshipModel::getSponsorFriends((int)$this->context->customer->id, 'pending'),
					'revive_sent' => $revive_sent,
					'nbRevive' => $nbRevive,
					'subscribeFriends' => RewardsSponsorshipModel::getSponsorFriends((int)$this->context->customer->id, 'subscribed'),
					'statistics' => $statistics,
					'reward_order_allowed' => $reward_order_allowed,
					'reward_registration_allowed' => $reward_registration_allowed,
					'multilevel' => $multilevel,
                                        'autoaddnetwork' => $this->context->customer->autoaddnetwork,
                                        'id_customer' => $this->context->customer->id
				);
				$this->context->smarty->assign($smarty_values);
			}
			// si popup
			else {
				$smarty_values = array(
					'canSendInvitations' => true,
					'popup' => true,
					'afterSubmit' => Tools::getValue('conditionsValided')
				);
				$this->context->smarty->assign($smarty_values);
			}
		}

                if ( Tools::isSubmit('submitSponsorFriendsThird') ) {
                    $error = "";
                    $invitation_sent = false;
                    $friendFirstNameThird = Tools::getValue('friendsFirstNameThird');
                    $friendLastNameThird = Tools::getValue('friendsLastNameThird');
                    $friendEmailThird = Tools::getValue('friendsEmailThird');

                    if (empty($friendFirstNameThird) || empty($friendLastNameThird) || !Validate::isName($friendFirstNameThird) || !Validate::isName($friendLastNameThird)) {
                        $error = 'name invalid';
                    } elseif (Tools::isSubmit('submitSponsorFriendsThird') && !Validate::isEmail($friendEmailThird) ) {
                        $error = 'email invalid';
                    } elseif (RewardsSponsorshipModel::isEmailExists($friendEmailThird) || Customer::customerExists($friendEmailThird)) {
                        $customerKickOut = Db::getInstance()->getValue("SELECT kick_out FROM "._DB_PREFIX_."customer WHERE email = '".$friendEmailThird."'");
                        if ( $customerKickOut == 0 ) {
                            $error = 'email exists';
                            $mails_exists[] = $friendEmailThird;
                        }
                    }
                    
                    if ( $error == "" ) {
                        $sponsor = array();
                        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
                        foreach ( $tree AS $sponsorshipMember ) {
                            if ( $this->context->customer->id != $sponsorshipMember['id'] && empty($sponsor) ) {
                                $sponsorPossible = Db::getInstance()->getRow("SELECT c.id_customer, c.username, c.firstname, c.lastname, c.email, (2-COUNT(rs.id_sponsorship) ) sponsoships
                                                                                FROM "._DB_PREFIX_."customer c
                                                                                LEFT JOIN "._DB_PREFIX_."rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                                                                                WHERE c.id_customer = ".$sponsorshipMember['id']);
                                if ( $sponsorPossible['sponsoships'] > 0 ) {
                                    $sponsor = $sponsorPossible;
                                }
                            }
                        }

                        if ( !empty($sponsor) ) {
                            $nb_discount = (int)MyConf::get('RSPONSORSHIP_QUANTITY_GC', null, $id_template);
                            $discount_gc = $this->module->getDiscountReadyForDisplay((int)MyConf::get('RSPONSORSHIP_DISCOUNT_TYPE_GC', null, $id_template), (int)MyConf::get('RSPONSORSHIP_FREESHIPPING_GC', null, $id_template), (float)MyConf::get('RSPONSORSHIP_VOUCHER_VALUE_GC_'.(int)$this->context->currency->id, null, $id_template), null, MyConf::get('RSPONSORSHIP_REAL_VOUCHER_GC', null, $id_template) ? MyConf::get('RSPONSORSHIP_REAL_DESC_GC', (int)$this->context->language->id, $id_template) : null);
                            $sponsorship = new RewardsSponsorshipModel();
                            $sponsorship->id_sponsor = $sponsor['id_customer'];
                            $sponsorship->id_customer = $this->generateIdTemporary($friendEmailThird);
                            $sponsorship->firstname = $friendFirstNameThird;
                            $sponsorship->lastname = $friendLastNameThird;
                            $sponsorship->email = $friendEmailThird;
                            $sponsorship->channel = 1;
                            $send = "";
                            if ($sponsorship->save()) {
                                $vars = array(
                                        '{message}' => Tools::nl2br(Tools::getValue('message')),
                                        '{email}' => $sponsor['id_customer'],
                                        '{firstname_invited}'=> $sponsorship->firstname,
                                        '{inviter_username}' => $sponsor['username'],
                                        '{username}' => $sponsor['username'],
                                        '{lastname}' => $sponsor['lastname'],
                                        '{firstname}' => $sponsor['firstname'],
                                        '{email_friend}' => $sponsorship->email,
                                        '{link}' => $sponsorship->getSponsorshipMailLink(),
                                        '{Expiration}'=> $send,
                                        '{nb_discount}' => $nb_discount,
                                        '{discount}' => $discount_gc);
                                $this->module->sendMail((int)$this->context->language->id, $template, $this->module->getL('invitation'), $vars, $sponsorship->email, $sponsorship->firstname.' '.$sponsorship->lastname);
                                Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."rewards_sponsorship_third(id_customer,id_rewards_sponsorship,email_third,date_add)
                                                            VALUES (".$this->context->customer->id.",".$sponsorship->id.",'".$sponsorship->email."',NOW())");
                                $invitation_sent = true;
                            }
                        } else {
                            $error = 'no sponsor';
                        }
                    }
                }
                
                $sponsorshipThird = Db::getInstance()->getRow("SELECT rs.id_sponsorship, rs.firstname, rs.lastname, rs.email, c.id_customer
                                                                FROM "._DB_PREFIX_."rewards_sponsorship_third rst
                                                                INNER JOIN "._DB_PREFIX_."rewards_sponsorship rs ON ( rst.id_rewards_sponsorship = rs.id_sponsorship )
                                                                LEFT JOIN "._DB_PREFIX_."customer c ON ( rs.id_customer = c.id_customer )
                                                                WHERE rst.id_customer = ".$this->context->customer->id);

                $smarty_values = array(
                    'error' => $error,
                    'invitation_sent' => $invitation_sent,
                    'sponsorshipThird' => $sponsorshipThird,
                );
                $this->context->smarty->assign($smarty_values);
                
		$this->setTemplate('sponsorship.tpl');
	}
}
