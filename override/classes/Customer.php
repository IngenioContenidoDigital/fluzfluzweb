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

class Customer extends CustomerCore
{
    /** @var string Current username */
    public $username;
    
    /** @var string DNI number */
    public $dni;

    /** @var string kick_out number */
    public $kick_out;

    /** @var string manual_inactivation number */
    public $manual_inactivation;

    /** @var string days_inactive number */
    public $days_inactive;

    /** @var string autoaddnetwork number */
    public $autoaddnetwork;
    
    public $date_kick_out;

    public $warning_kick_out;
    
    public $civil_status;
    public $occupation_status;
    public $field_work;
    public $pet;
    public $pet_name;
    public $spouse_name;
    public $children;
    public $phone_provider;
    public $vault_code;
    public $phone;
    public $app_confirm;
    
    public static $definition = array(
        'table' => 'customer',
        'primary' => 'id_customer',
        'fields' => array(
            'secure_key' =>                array('type' => self::TYPE_STRING, 'validate' => 'isMd5', 'copy_post' => false),
            'username' =>                    array('type' => self::TYPE_STRING, 'required' => true, 'size' => 32),
            'lastname' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
            'firstname' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
            'email' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 128),
            'passwd' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isPasswd', 'required' => true, 'size' => 32),
            'last_passwd_gen' =>            array('type' => self::TYPE_STRING, 'copy_post' => false),
            'id_gender' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'birthday' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isBirthDate'),
            'newsletter' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'newsletter_date_add' =>        array('type' => self::TYPE_DATE,'copy_post' => false),
            'ip_registration_newsletter' =>    array('type' => self::TYPE_STRING, 'copy_post' => false),
            'optin' =>                        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'autoaddnetwork' =>               array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'website' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isUrl'),
            'company' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'siret' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isSiret'),
            'ape' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isApe'),
            'outstanding_allow_amount' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'copy_post' => false),
            'show_public_prices' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'id_risk' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'max_payment_days' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'active' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'deleted' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'note' =>                        array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 65000, 'copy_post' => false),
            'is_guest' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'id_shop' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'id_shop_group' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'id_default_group' =>            array('type' => self::TYPE_INT, 'copy_post' => false),
            'id_lang' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'date_add' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'dni' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isDniLite', 'size' => 16),
            'kick_out' =>                   array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'manual_inactivation' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'days_inactive' =>              array('type' => self::TYPE_INT),
            'date_kick_out' =>              array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'warning_kick_out' =>              array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'civil_status' =>               array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 32),
            'occupation_status' =>               array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 32),
            'field_work' =>               array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 32),
            'pet' =>               array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 32),
            'pet_name' =>               array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 32),
            'spouse_name' =>               array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 50),
            'children' =>               array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 5),
            'phone_provider' =>               array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 32),
            'vault_code' =>               array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 4),
            'phone' =>               array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'size' => 15),
            'app_confirm' =>               array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'size' => 6),
        ),  
    );
    
    public function add($autodate = true, $null_values = true)
    {
        $this->id_shop = ($this->id_shop) ? $this->id_shop : Context::getContext()->shop->id;
        $this->id_shop_group = ($this->id_shop_group) ? $this->id_shop_group : Context::getContext()->shop->id_shop_group;
        $this->id_lang = ($this->id_lang) ? $this->id_lang : Context::getContext()->language->id;
        $this->birthday = (empty($this->years) ? $this->birthday : (int)$this->years.'-'.(int)$this->months.'-'.(int)$this->days);
        $this->secure_key = md5(uniqid(rand(), true));
        $this->last_passwd_gen = date('Y-m-d H:i:s', strtotime('-'.Configuration::get('PS_PASSWD_TIME_FRONT').'minutes'));

        if ($this->newsletter && !Validate::isDate($this->newsletter_date_add)) {
            $this->newsletter_date_add = date('Y-m-d H:i:s');
        }

        if ($this->id_default_group == Configuration::get('PS_CUSTOMER_GROUP')) {
            if ($this->is_guest) {
                $this->id_default_group = (int)Configuration::get('PS_GUEST_GROUP');
            } else {
                //$this->id_default_group = (int)Configuration::get('PS_CUSTOMER_GROUP');
                $this->id_default_group = 4;
            }
        }

        /* Can't create a guest customer, if this feature is disabled */
        if ($this->is_guest && !Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
            return false;
        }
        $success = parent::add($autodate, $null_values);
        $this->updateGroup($this->groupBox);
        return $success;
    }
    
    public function update($nullValues = false)
    {
        $this->birthday = (empty($this->years) ? $this->birthday : (int)$this->years.'-'.(int)$this->months.'-'.(int)$this->days);
        $this->manual_inactivation = (!$this->active) ? 1 : 0;
        
        if ( Tools::getValue('civil_status') == "" ) {
            $this->civil_status = null;
        }
        if ( Tools::getValue('occupation_status') == "" ) {
            $this->occupation_status = null;
        }
        if ( Tools::getValue('field_work') == "" ) {
            $this->field_work = null;
        }
        if ( Tools::getValue('pet') == "" ) {
            $this->pet = null;
        }
        if ( Tools::getValue('pet_name') == "" ) {
            $this->pet_name = null;
        }
        if ( Tools::getValue('spouse_name') == "" ) {
            $this->spouse_name = null;
        }
        if ( Tools::getValue('children') == "" ) {
            $this->children = 0;
        }
        if ( Tools::getValue('phone_provider') == "" ) {
            $this->phone_provider = null;
        }
        if ( Tools::getValue('vault_code') == "" ) {
            $this->vault_code = null;
        }
        if ( Tools::getValue('phone') == "" ) {
            $this->phone = null;
        }
        if ( Tools::getValue('app_confirm') == "" ) {
            $this->app_confirm = null;
        }

        if ($this->newsletter && !Validate::isDate($this->newsletter_date_add)) {
            $this->newsletter_date_add = date('Y-m-d H:i:s');
        }
        if (isset(Context::getContext()->controller) && Context::getContext()->controller->controller_type == 'admin') {
            $this->updateGroup($this->groupBox);
        }

        if ($this->deleted) {
            $addresses = $this->getAddresses((int)Configuration::get('PS_LANG_DEFAULT'));
            foreach ($addresses as $address) {
                $obj = new Address((int)$address['id_address']);
                $obj->delete();
            }
        }

        return ObjectModel::update(true);
    }
    
    public function getAddresses($id_lang)
    {
        $share_order = (bool)Context::getContext()->shop->getGroup()->share_order;
        $cache_id = 'Customer::getAddresses'.(int)$this->id.'-'.(int)$id_lang.'-'.$share_order;
        if (!Cache::isStored($cache_id)) {
            $sql = 'SELECT DISTINCT a.*, cl.`name` AS country, s.name AS state, s.iso_code AS state_iso
					FROM `'._DB_PREFIX_.'address` a
					LEFT JOIN `'._DB_PREFIX_.'country` c ON (a.`id_country` = c.`id_country`)
					LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country`)
					LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = a.`id_state`)
					'.($share_order ? '' : Shop::addSqlAssociation('country', 'c')).'
					WHERE `id_customer` = '.(int)$this->id.' AND a.`deleted` = 0';

            if ( (int)$id_lang != 0 ) {
                $sql .= ' AND `id_lang` = '.(int)$id_lang;
            }

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }
    
    public function transformToCustomer($id_lang, $password = null)
    {
        if (!$this->isGuest()) {
            return false;
        }
        if (empty($password)) {
            $password = Tools::passwdGen(8, 'RANDOM');
        }
        if (!Validate::isPasswd($password)) {
            return false;
        }

        $this->is_guest = 0;
        $this->passwd = Tools::encrypt($password);
        $this->cleanGroups();
        $this->addGroups(array(Configuration::get('PS_CUSTOMER_GROUP'))); // add default customer group
        if ($this->update()) {
            $vars = array(
                '{username}' => $this->username,
                '{firstname}' => $this->firstname,
                '{lastname}' => $this->lastname,
                '{email}' => $this->email,
                '{passwd}' => $password
            );

            Mail::Send(
                (int)$id_lang,
                'guest_to_customer',
                Mail::l('Your guest account has been transformed into a customer account', (int)$id_lang),
                $vars,
                $this->email,
                $this->firstname.' '.$this->lastname,
                null,
                null,
                null,
                null,
                _PS_MAIL_DIR_,
                false,
                (int)$this->id_shop
            );
            return true;
        }
        return false;
    }
    
    public static function customerPurchaseLicense($email) {
        $membresias = Db::getInstance()->getValue("SELECT COUNT(*) membresias
                                                    FROM "._DB_PREFIX_."customer c
                                                    LEFT JOIN "._DB_PREFIX_."orders o ON ( c.id_customer = o.id_customer )
                                                    LEFT JOIN "._DB_PREFIX_."order_detail od ON ( o.id_order = od.id_order )
                                                    LEFT JOIN "._DB_PREFIX_."order_history oh ON ( o.id_order = oh.id_order )
                                                    WHERE c.email = '".$email."'
                                                    AND od.product_reference LIKE 'MFLUZ%'
                                                    AND oh.id_order_state = 2");

        /*$expulsiones = Db::getInstance()->getValue("SELECT COUNT(*) expulsiones
                                                    FROM "._DB_PREFIX_."rewards_sponsorship_kick_out
                                                    WHERE email = '".$email."'");*/
        
        if ( $membresias > 0 /*&& $membresias > $expulsiones*/ ) {
            return true;
        } else {
            return false;
        }
        
    }
    
    public static function updateEmailSponsorship($id_customer, $email) {
        return Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'rewards_sponsorship
                                            SET email = "'.$email.'"
                                            WHERE id_customer = '.(int)$id_customer);
    }
    
    public static function usernameExists($username) {
        $users = Db::getInstance()->getValue("SELECT COUNT(*)
                                                FROM "._DB_PREFIX_."customer
                                                WHERE username = '".$username."'
                                                AND active = 1");  
        if ( $users > 0 ) {
            return true;
        } else {
            return false;
        }
    }

    public static function dniExists($dni,$email) {
        $users = Db::getInstance()->getValue("SELECT COUNT(*)
                                                FROM "._DB_PREFIX_."customer
                                                WHERE dni = ".$dni."
                                                AND email != '".$email."'
                                                AND active = 1");  
        if ( $users > 0 ) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function getCard($customer) {
        $card = DB::getInstance()->getRow( "SELECT c.id_card, c.id_customer, c.num_creditCard, c.last_digits, c.nameOwner, c.name_creditCard, c.date_expiration, cc.secure_key
                                            FROM "._DB_PREFIX_."cards c
                                            INNER JOIN "._DB_PREFIX_."customer cc ON ( c.id_customer = cc.id_customer )
                                            WHERE c.id_customer = ".$customer );
        $card['num_creditCard'] = Encrypt::decrypt($card['secure_key'] , $card['num_creditCard']);
        
        return $card;
    }
    
    public static function addCard($customer, $secure_key, $num_card, $nameOwner, $type_card, $date_expiration) {

        $card = Encrypt::encrypt($secure_key , $num_card);
        $lastdigits = substr($num_card, -4);

        $existcard = Db::getInstance()->getValue("SELECT COUNT(*) cards
                                                    FROM "._DB_PREFIX_."cards
                                                    WHERE id_customer = ".$customer);
        if ( $existcard > 0 ) {
            $addCard = Db::getInstance()->execute("UPDATE "._DB_PREFIX_."cards
                                                        SET nameOwner = '".$nameOwner."', name_creditCard = '".$type_card."', num_creditCard = '".$card."', last_digits = '".$lastdigits."', date_expiration = '".$date_expiration."'
                                                        WHERE id_customer = ".$customer);
        } else {
            $addCard = Db::getInstance()->Execute( 'INSERT INTO '._DB_PREFIX_.'cards(id_customer, nameOwner, name_creditCard, num_creditCard, last_digits, date_expiration)
                                                        VALUES ('.$customer.', "'.$nameOwner.'", "'.$type_card.'", "'.$card.'", "'.$lastdigits.'", "'.$date_expiration.'")' );
        }
        
        return $addCard;
    }
    
    public static function percentProfileComplete($id_customer) {
        $fields_complete = 0;
        $fields_information = 19;

        $customer = new Customer($id_customer); 
        $address = $customer->getAddresses();
        $address = $address[0];

        /* 1 */ if ( file_exists(_PS_IMG_DIR_."profile-images/".$customer->id.".png") ) { $fields_complete++; }
        /* 2 */ if ( $customer->id_gender != "" ) { $fields_complete++; }
        /* 3 */ if ( $customer->firstname != "" ) { $fields_complete++; }
        /* 4 */ if ( $customer->lastname != "" ) { $fields_complete++; }
        /* 5 */ if ( $customer->email != "" ) { $fields_complete++; }
        /* 6 */ if ( $customer->dni != "" ) { $fields_complete++; }
        /* 7 */ if ( $customer->birthday != "" ) { $fields_complete++; }
        /* 8 */ if ( $customer->civil_status != "" ) { $fields_complete++; }
        /* 9 */ if ( $customer->occupation_status != "" ) { $fields_complete++; }
        /* 10 */ if ( $customer->field_work != "" ) { $fields_complete++; }
        /* 11 */ if ( $customer->pet != "" ) { $fields_complete++; }
        /* 12 */ if ( $customer->pet_name != "" ) { $fields_complete++; }
        /* 13 */ if ( $customer->spouse_name != "" ) { $fields_complete++; }
        /* 14 */ if ( $customer->children != "" ) { $fields_complete++; }
        /* 15 */ if ( $customer->phone_provider != "" ) { $fields_complete++; }
        /* 16 */ if ( $address['phone'] != "" ) { $fields_complete++; }
        /* 17 */ if ( $address['address1'] != "" ) { $fields_complete++; }
        /* 18 */ if ( $address['address2'] != "" ) { $fields_complete++; }
        /* 19 */ if ( $address['city'] != "" ) { $fields_complete++; }

        return round( ($fields_complete*100)/$fields_information );
    }

    public function mylogout()
    {
        $id_cart = Context::getContext()->cookie->id_cart;
        if(!empty($id_cart)){
            $sql = 'DELETE FROM '._DB_PREFIX_.'cart WHERE id_cart = '.$id_cart;
            Db::getInstance()->execute($sql);
        }
        
        Hook::exec('actionCustomerLogoutBefore', array('customer' => $this));

        if (isset(Context::getContext()->cookie)) {
            Context::getContext()->cookie->mylogout();
        }

        $this->logged = 0;

        Hook::exec('actionCustomerLogoutAfter', array('customer' => $this));
    }
}

?>
