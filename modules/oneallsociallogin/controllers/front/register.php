<?php
/**
 * @package   	OneAll Social Login
 * @copyright 	Copyright 2011-2017 http://www.oneall.com
 * @license   	GNU/GPL 2 or later
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307,USA.
 *
 * The "GNU General Public License" (GPL) is available at
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

include_once(_PS_MODULE_DIR_.'/allinone_rewards/allinone_rewards.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsSponsorshipModel.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/controllers/front/sponsorship.php');
include_once(_PS_CLASS_DIR_.'Customer.php');
include_once(_PS_CLASS_DIR_.'Country.php');

class OneAllSocialLoginRegisterModuleFrontController extends ModuleFrontController
{
	public $auth = false;
	public $ssl = true;

	/**
	 * Assign template vars related to page content
	 */
	public function initContent ()
	{
		parent::initContent ();
		global $smarty;
		
		// Restore back value.
		$back = Tools::getValue ('back');
		
		if (!empty ($back))
		{
			$this->context->smarty->assign ('back', Tools::safeOutput ($back));
		}
                
                $this->context->smarty->assign('cities', City::getCities());
		$this->context->smarty->assign('countries', Country::getCountries(1));
		// Did an error occur?
		$have_error = true;
		
		// The cookie is required to proceed.
		if (isset ($this->context->cookie->oasl_data))
		{
			// Extract the data.
			$data = unserialize (base64_decode ($this->context->cookie->oasl_data));
			
			// Check data format.
			if (is_array ($data))
			{
				$have_error = false;
				
				// Submit Button Clicked
				if (Tools::isSubmit ('submit'))
				{
					// Reset Errors.
					$this->errors = array();
					
					// Read fields.
                                        $code_sponsor = trim (Tools::getValue ('oasl_code_sponsor'));
					$email = trim (Tools::getValue ('oasl_email'));
					$firstname = trim (Tools::getValue ('oasl_firstname'));
					$lastname = trim (Tools::getValue ('oasl_lastname'));
					$username = trim (Tools::getValue ('oasl_username'));
					$address = trim (Tools::getValue ('oasl_address'));
					$phone = trim (Tools::getValue ('oasl_phone'));
					$city = trim (Tools::getValue ('oasl_city'));
					$typedni = trim (Tools::getValue ('oasl_typedni'));
					$dni = trim (Tools::getValue ('oasl_dni'));
                                        $id_country = trim (Tools::getValue ('oasl_id_country'));
					$newsletter = 1;
					$code_generate = Allinone_rewardsSponsorshipModuleFrontController::generateIdCodeSponsorship($username);
                                        
                                        // Validate Id Sponsor
                                        $id_sponsor = RewardsSponsorshipCodeModel::getIdSponsorByCode(Tools::getValue ('oasl_code_sponsor'));
                                        if ( $code_sponsor == "" || empty($id_sponsor)) {
                                            $id_sponsor = "";
                                        }
                                        
					// Make sure the firstname is not empty.
					if (strlen ($firstname) == 0 || !Validate::isName($firstname))
					{
                                            $this->errors [] = Tools::displayError ('Por favor ingrese un nombre valido.');
					}
					
					// Make sure the lastname is not empty.
					if (strlen ($lastname) == 0 || !Validate::isName($lastname))
					{
                                            $this->errors [] = Tools::displayError ('Por favor ingrese un apellido valido.');
					}
					
					// Make sure the email address it is not empty.
					if (strlen ($email) == 0 || !Validate::isEmail($email))
					{
                                            $this->errors [] = Tools::displayError ('Por favor ingrese un correo electronico valido.');
					}
					// Make sure the email address is not already taken.
					elseif (oneall_social_login_tools::get_id_customer_for_email_address($email) !== false)
					{
                                            $this->errors [] = Tools::displayError ('El correo electronico se encuentra en uso.');
					}
                                        
                                        // Make sure the username is not empty.
					if (strlen ($username) == 0)
					{
                                            $this->errors [] = Tools::displayError ('Por favor ingrese un nombre de usuario valido.');
					}
                                        
                                        // Validate exist username
                                        if ( Customer::usernameExists($username) ) {
                                            $this->errors[] = Tools::displayError('El nombre de usuario se encuentra en uso.');
                                        }
                    
                                        //validate existe phone mobile
                                        if ( Customer::phoneExists($phone) ) {
                                            $this->errors[] = Tools::displayError('El numero de telefono ya se encuentra en uso.');
                                        }

                                        // Make sure the address is not empty.
					if (strlen ($address) == 0)
					{
                                            $this->errors [] = Tools::displayError ('Por favor ingrese una direccion valida.');
					}
                                        
                                        // Make sure the phone is not empty.
					if (strlen ($phone) == 0)
					{
                                            $this->errors [] = Tools::displayError ('Por favor ingrese un telefono valido.');
					}
                                        
                                        // Make sure the city is not empty.
					if (strlen ($city) == 0)
					{
                                            $this->errors [] = Tools::displayError ('Por favor ingrese una ciudad valida.');
					}
                                        
                                        // Make sure the typedni is not empty.
					if (strlen ($typedni) == 0)
					{
                                            $this->errors [] = Tools::displayError ('Por favor ingrese un tipo de identificacion valida.');
					}
                                        
                                        // Make sure the dni is not empty.
					if ( $typedni == 0 ) {
                                            if ( Validate::isIdentification($dni) || $dni == "" ) {
                                                $this->errors [] = Tools::displayError ('Por favor ingrese una identificacion valida.');
                                            }
                                        } 
                                        else if ( $typedni == 2 ){
                                            if ( Validate::isIdentificationCE($dni) || $dni == "" ) {
                                                $this->errors [] = Tools::displayError ('Por favor ingrese una identificacion valida.');
                                            }
                                        }
                                        
                                        // Validate dni
                                        if ( Customer::dniExists($dni,$email) ) {
                                            $this->errors [] = Tools::displayError ('El numero de identificacion se encuentra en uso.');
                                        }
					
					// We are good to go.
					if (count ($this->errors) == 0)
					{
						// Store the manually entered email fields.
                                                $data ['user_sponsor_id'] = $id_sponsor;
						$data ['user_email'] = strtolower ($email);
						$data ['user_first_name'] = ucwords (strtolower ($firstname));
						$data ['user_last_name'] = ucwords (strtolower ($lastname));
						$data ['user_newsletter'] = ($newsletter == 1 ? 1 : 0);
                                                $data ['user_username'] = $username;
                                                $data ['user_address'] = $address;
                                                $data ['user_phone'] = $phone;
                                                $data ['user_city'] = $city;
                                                $data ['user_typedni'] = $typedni;
                                                $data ['user_dni'] = $dni;
                                                $data['user_code_sponsor'] = $code_sponsor;
						$data['id_country'] = $id_country;
						// Email flags.
						$send_email_to_admin = ((Configuration::get ('OASL_EMAIL_ADMIN_DISABLE') != 1) ? true : false);
						$send_email_to_customer = ((Configuration::get ('OASL_EMAIL_CUSTOMER_DISABLE') != 1) ? true : false);
						
						// Create a new account.                                                
						$id_customer = oneall_social_login_tools::create_customer_from_data ($data, $send_email_to_admin, $send_email_to_customer);
						
                                                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_sponsor, code)
                                                                VALUES ('.$id_customer.', "'.$code_generate.'")');
                                                
                                                
                                                $this->context->smarty->assign('sendSMSconfirm', true);
						// Login the customer.
						/*if (!empty ($id_customer) and oneall_social_login_tools::login_customer ($id_customer))
						{
							// Remove the data
							unset ($this->context->cookie->oasl_data);
							
							// A refresh is required to update the page
							$back = trim (Tools::getValue ('back'));
							$back = (!empty ($back) ? $back : oneall_social_login_tools::get_current_url ());
							Tools::redirect ($back);
						}*/
                                        }
				}
                                
                                elseif (Tools::isSubmit('confirm')) {
                                    $id_customer = Tools::getValue('id_customer');
                                    $codesponsor = Tools::getValue('codesponsor');
                                    $id_sponsor = Tools::getValue('id_sponsor');
                                    $codesms = Tools::getValue('codesms');

                                    if ( Customer::validateCodeSMS($id_customer,$codesms) ) {
                                        $customer = new Customer($id_customer);
                                        $customer->active = 1;
                                        $customer->save();
                                        $this->context->smarty->assign('id_customer', $id_customer);
                                        $this->context->smarty->assign('successfulregistration', true);
                                    } else {
                                        $this->errors[] = "El codigo es incorrecto.";
                                        $this->context->smarty->assign('id_customer', $id_customer);
                                        $this->context->smarty->assign('codesponsor', $codesponsor);
                                        $this->context->smarty->assign('sendSMS', true);
                                    }
                                } elseif (Tools::isSubmit('resendSMS')) {
                                    $id_customer = Tools::getValue('id_customer');
                                    $codesponsor = Tools::getValue('codesponsor');
                                    $id_sponsor = Tools::getValue('id_sponsor');

                                    $sendSMS = false;
                                    while ( !$sendSMS ) {
                                        $sendSMS = Customer::confirmCustomerSMS($id_customer);
                                    }
                                    if ( $sendSMS ) {
                                        $this->context->smarty->assign('id_customer', $id_customer);
                                        $this->context->smarty->assign('codesponsor', $codesponsor);
                                        $this->context->smarty->assign('id_sponsor', $id_sponsor);
                                        $this->context->smarty->assign('sendSMS', true);
                                    }
                                }
				// First call of the page.
				else
				{
					$smarty->assign ('oasl_populate', 1);
					$smarty->assign ('oasl_email', (isset ($data ['user_email']) ? $data ['user_email'] : ''));
					$smarty->assign ('oasl_first_name', (isset ($data ['user_first_name']) ? $data ['user_first_name'] : ''));
					$smarty->assign ('oasl_last_name', (isset ($data ['user_last_name']) ? $data ['user_last_name'] : ''));
					$smarty->assign ('oasl_newsletter', 1);
				}
				
				// Assign template vars.
				$smarty->assign ('identity_provider', $data ['identity_provider']);
				$smarty->assign ('oasl_register', $this->context->link->getModuleLink ('oneallsociallogin', 'register'));
				
				// Show our template.
				$this->setTemplate ('oneallsociallogin_register.tpl');
			}
                }
		
		// We could not extract the data.
		if ($have_error)
		{
			Tools::redirect ();
		}
	}
}
