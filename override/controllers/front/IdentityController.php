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

class IdentityController extends IdentityControllerCore
{
    public function postProcess()
    {
        $origin_newsletter = (bool)$this->customer->newsletter;

        if (Tools::isSubmit('submitIdentity')) {

            $email = trim(Tools::getValue('email'));

            if (Tools::getValue('months') != '' && Tools::getValue('days') != '' && Tools::getValue('years') != '') {
                $this->customer->birthday = (int)Tools::getValue('years').'-'.(int)Tools::getValue('months').'-'.(int)Tools::getValue('days');
            } elseif (Tools::getValue('months') == '' && Tools::getValue('days') == '' && Tools::getValue('years') == '') {
                $this->customer->birthday = null;
            } else {
                $this->errors[] = Tools::displayError('Invalid date of birth.');
            }

            if ( $_FILES['profileimg']['tmp_name'] != "" ) {
                $typeimg = explode("/", $_FILES['profileimg']['type']);
                if ( $typeimg[0] != "image" || ($typeimg[1] != "jpeg" && $typeimg[1] != "jpg" ) ) {
                    $this->errors[] = Tools::displayError('El archivo cargado no se encuentra en un formato correcto (JPEG, JPG).');
                }
            }

            if (Tools::getIsset('old_passwd')) {
                $old_passwd = trim(Tools::getValue('old_passwd'));
            }

            if (!Validate::isEmail($email)) {
                $this->errors[] = Tools::displayError('This email address is not valid');
            } elseif ($this->customer->email != $email && Customer::customerExists($email, true)) {
                $this->errors[] = Tools::displayError('An account using this email address has already been registered.');
            } elseif (!Tools::getIsset('old_passwd') || (Tools::encrypt($old_passwd) != $this->context->cookie->passwd)) {
                $this->errors[] = Tools::displayError('The password you entered is incorrect.');
            } elseif (Tools::getValue('passwd') != Tools::getValue('confirmation')) {
                $this->errors[] = Tools::displayError('The password and confirmation do not match.');
            } else {
                $prev_id_default_group = $this->customer->id_default_group;

                // Merge all errors of this file and of the Object Model
                $this->errors = array_merge($this->errors, $this->customer->validateController());
            }

            if (!count($this->errors)) {
                $this->customer->id_default_group = (int)$prev_id_default_group;
                $this->customer->firstname = Tools::ucwords($this->customer->firstname);

                if (Configuration::get('PS_B2B_ENABLE')) {
                    $this->customer->website = Tools::getValue('website'); // force update of website, even if box is empty, this allows user to remove the website
                    $this->customer->company = Tools::getValue('company');
                }

                if (!Tools::getIsset('newsletter')) {
                    $this->customer->newsletter = 0;
                } elseif (!$origin_newsletter && Tools::getIsset('newsletter')) {
                    if ($module_newsletter = Module::getInstanceByName('blocknewsletter')) {
                        /** @var Blocknewsletter $module_newsletter */
                        if ($module_newsletter->active) {
                            $module_newsletter->confirmSubscription($this->customer->email);
                        }
                    }
                }

                if (!Tools::getIsset('optin')) {
                    $this->customer->optin = 0;
                }

                if (Tools::getValue('passwd')) {
                    $this->context->cookie->passwd = $this->customer->passwd;
                }

                $this->customer->autoaddnetwork = Tools::getValue('autoaddnetwork');

                $this->customer->dni = Tools::getValue('government');

                $address = $this->customer->getAddresses();
                $address = new Address($address[0]['id_address']);
                $address->dni = Tools::getValue('government');
                $address->phone = Tools::getValue('phone');

                if ( $_FILES['profileimg']['tmp_name'] != "" ) {
                    // subir imagen al servidor
                    $target_path = _PS_IMG_DIR_ . "profile-images/" . basename( $this->customer->id.".".$typeimg[1] );
                    if ( !move_uploaded_file($_FILES['profileimg']['tmp_name'], $target_path) ) {
                        $this->errors[] = Tools::displayError('No fue posible cargar la imagen de perfil.');
                    }

                    // convertir imagen a PNG
                    $patch_grabar = _PS_IMG_DIR_ . "profile-images/" . basename( $this->customer->id.".png" );
                    $imagen = imagecreatefromjpeg($target_path);
                    $pngquality = floor(($quality - 10) / 10);
                    imagepng($imagen, $patch_grabar, $pngquality);

                    // borrar imagen original
                    unlink( $target_path );

                    // cambiar tamaño imagen y recortarla en circulo
                    include_once(_PS_ROOT_DIR_.'/classes/Thumb.php');
                    $mythumb = new thumb();
                    $mythumb->loadImage($patch_grabar);
                    $mythumb->crop(400, 400, 'center');
                    $mythumb->save($patch_grabar);
                }

                if ( $this->customer->update() && $address->update() ) {
                    
                    Db::getInstance()->execute("UPDATE "._DB_PREFIX_."address
                                                SET address1 = '".Tools::getValue('address1')."', address2 = '".Tools::getValue('address2')."', city = '".Tools::getValue('city')."', dni = '".Tools::getValue('government')."'
                                                WHERE id_customer = ".$this->customer->id);
                    
                    $this->context->cookie->customer_lastname = $this->customer->lastname;
                    $this->context->cookie->customer_firstname = $this->customer->firstname;
                    $this->context->smarty->assign('confirmation', 1);
                } else {
                    $this->errors[] = Tools::displayError('The information cannot be updated.');
                }
            }
        } elseif (Tools::isSubmit('submitCard')) {
            if ( Tools::getValue('numbercard') == "" ) { $this->errors[] = Tools::displayError('This number card can not be empty.'); }
            if ( Tools::getValue('monthsCard') == "" ) { $this->errors[] = Tools::displayError('This months card expiration can not be empty.'); }
            if ( Tools::getValue('yearsCard') == "" ) { $this->errors[] = Tools::displayError('This years card expiration can not be empty.'); }
            if ( Tools::getValue('holdernamecard') == "" ) { $this->errors[] = Tools::displayError('This cardholder name can not be empty.'); }
            if (!count($this->errors)) {

                $addCard = Customer::addCard($this->customer->id, $this->customer->secure_key, Tools::getValue('numbercard'), Tools::getValue('holdernamecard'), Tools::getValue('typecard'), Tools::getValue('monthsCard')."/".Tools::getValue('yearsCard'));

                if ( $addCard ) {
                    $this->context->smarty->assign('confirmationcard', 1);
                } else {
                    $this->errors[] = Tools::displayError('The information cannot be updated.');
                }
            }
        } elseif (Tools::isSubmit('submitDeactivate')) {
            $this->customer->active = false;
            $this->customer->update();
            $this->customer->logout();
            Tools::redirect('index.php');
        } else {
            $_POST = array_map('stripslashes', $this->customer->getFields());
        }

        return $this->customer;
    }
    
    public function initContent()
    {
        FrontController::initContent();

        if ($this->customer->birthday) {
            $birthday = explode('-', $this->customer->birthday);
        } else {
            $birthday = array('-', '-', '-');
        }

        /* Generate years, months and days */
        $this->context->smarty->assign(array(
                'years' => Tools::dateYears(),
                'sl_year' => $birthday[0],
                'months' => Tools::dateMonths(),
                'sl_month' => $birthday[1],
                'days' => Tools::dateDays(),
                'sl_day' => $birthday[2],
                'errors' => $this->errors,
                'genders' => Gender::getGenders(),
                'operators' => $this->getOperators(),
            ));

        // Call a hook to display more information
        $this->context->smarty->assign(array(
            'HOOK_CUSTOMER_IDENTITY_FORM' => Hook::exec('displayCustomerIdentityForm'),
        ));

        $newsletter = Configuration::get('PS_CUSTOMER_NWSL') || (Module::isInstalled('blocknewsletter') && Module::getInstanceByName('blocknewsletter')->active);
        $this->context->smarty->assign('newsletter', $newsletter);
        $this->context->smarty->assign('optin', (bool)Configuration::get('PS_CUSTOMER_OPTIN'));

        $this->context->smarty->assign('field_required', $this->context->customer->validateFieldsRequiredDatabase());
        
        $address = $this->customer->getAddresses();
        $this->context->smarty->assign('customerGovernment', $address[0]['dni']);
        $this->context->smarty->assign('customerPhone', $address[0]['phone']);
        $this->context->smarty->assign('customer', $this->context->customer);

        $card = Customer::getCard($this->customer->id);
        $this->context->smarty->assign('card', $card);

        $dateExplode = explode("/",$card['date_expiration']);
        $year = date('Y-m-j');
        $year_select = '<select id="yearsCard" name="yearsCard" class="form-control inputformcard enabled" disabled>
                            <option value="">-</option>';
        for ( $i=0; $i<=15; $i++ ) {
            $str_year = strtotime ( '+'.$i.' year' , strtotime ( $year ) );
            $new_year = date( 'Y' , $str_year);
            if ( $dateExplode[1] == $new_year ) {
                $year_select .= '<option value="'.$new_year.'" selected="selected">'.$new_year.'</option>';
            } else {
                $year_select .= '<option value="'.$new_year.'">'.$new_year.'</option>';
            }
        }
        $year_select .= '</select>';
        $this->context->smarty->assign('year_select',$year_select);
        
        $telconumbers = DB::getInstance()->executeS( "SELECT phone_mobile, default_number
                                                        FROM "._DB_PREFIX_."address
                                                        WHERE id_customer = ".$this->customer->id );
        $this->context->smarty->assign('telconumbers', $telconumbers);
        
        $address = DB::getInstance()->executeS( "SELECT a.id_country, a.address1, a.address2, a.city, c.name country
                                                FROM "._DB_PREFIX_."address a
                                                INNER JOIN "._DB_PREFIX_."country_lang c ON a.id_country = c.id_country
                                                WHERE a.id_customer = ".$this->customer->id."
                                                LIMIT 1" );
        $this->context->smarty->assign('address', $address[0]);
        
        $countries = Country::getCountries($this->context->language->id, false, true);
        $this->context->smarty->assign('countries', $countries);

        $this->context->smarty->assign('cities', City::getCities());
        
        $imgprofile = "";
        if ( file_exists(_PS_IMG_DIR_."profile-images/".$this->context->customer->id.".png") ) {
            $imgprofile = "/img/profile-images/".$this->context->customer->id.".png";
        }
        $this->context->smarty->assign('imgprofile',$imgprofile);
        
        $this->setTemplate(_PS_THEME_DIR_.'identity.tpl');
    }
    
    public function getOperators() {
        $query = "SELECT name
                  FROM ps_webservice_external_telco_operator";
        return Db::getInstance()->executeS($query);
    }
}

?>