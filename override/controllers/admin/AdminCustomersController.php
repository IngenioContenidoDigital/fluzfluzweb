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

/**
 * @property Customer $object
 */
class AdminCustomersController extends AdminCustomersControllerCore
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->required_database = true;
        $this->required_fields = array('newsletter','optin');
        $this->table = 'customer';
        $this->className = 'Customer';
        $this->lang = false;
        $this->deleted = true;
        $this->explicitSelect = true;

        $this->allow_export = true;

        $this->addRowAction('edit');
        $this->addRowAction('view');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->context = Context::getContext();

        $this->default_form_language = $this->context->language->id;

        $titles_array = array();
        $genders = Gender::getGenders($this->context->language->id);
        foreach ($genders as $gender) {
            /** @var Gender $gender */
            $titles_array[$gender->id_gender] = $gender->name;
        }

        $this->_join = ' LEFT JOIN '._DB_PREFIX_.'gender_lang gl ON (a.id_gender = gl.id_gender AND gl.id_lang = '.(int)$this->context->language->id.')';
        $this->_use_found_rows = false;
        $this->fields_list = array(
            'id_customer' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'title' => array(
                'title' => $this->l('Social title'),
                'filter_key' => 'a!id_gender',
                'type' => 'select',
                'list' => $titles_array,
                'filter_type' => 'int',
                'order_key' => 'gl!name'
            ),
            'username' => array(
                'title' => $this->l('username')
            ),
            'firstname' => array(
                'title' => $this->l('First name')
            ),
            'lastname' => array(
                'title' => $this->l('Last name')
            ),
            'email' => array(
                'title' => $this->l('Email address')
            ),
            'days_inactive' => array(
                'title' => $this->l('Dias Inactividad')
            ),
        );

        if (Configuration::get('PS_B2B_ENABLE')) {
            $this->fields_list = array_merge($this->fields_list, array(
                'company' => array(
                    'title' => $this->l('Company')
                ),
            ));
        }

        $this->fields_list = array_merge($this->fields_list, array(
            'total_spent' => array(
                'title' => $this->l('Sales'),
                'type' => 'price',
                'search' => false,
                'havingFilter' => true,
                'align' => 'text-right',
                'badge_success' => true
            ),
            /*'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!active'
            ),*/
            'newsletter' => array(
                'title' => $this->l('Newsletter'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printNewsIcon',
                'orderby' => false
            ),
            /*'optin' => array(
                'title' => $this->l('Opt-in'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printOptinIcon',
                'orderby' => false
            ),*/
            'date_add' => array(
                'title' => $this->l('Registration'),
                'type' => 'date',
                'align' => 'text-right'
            ),
            'date_kick_out' => array(
                'title' => $this->l('Fecha Expulsion'),
                'type' => 'datetime',
                'search' => false,
                'havingFilter' => true
            ),
            'connect' => array(
                'title' => $this->l('Last visit'),
                'type' => 'datetime',
                'search' => false,
                'havingFilter' => true
            ),
            'last_purchase' => array(
                'title' => $this->l('Ultima Compra'),
                'type' => 'datetime',
                'search' => false,
                'havingFilter' => true
            )
        ));

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_CUSTOMER;

        AdminController::__construct();

        $this->_select = '
        a.date_add, gl.name as title, (
            SELECT SUM(total_paid_real / conversion_rate)
            FROM '._DB_PREFIX_.'orders o
            WHERE o.id_customer = a.id_customer
            '.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
            AND o.valid = 1
        ) as total_spent, (
            SELECT c.date_add FROM '._DB_PREFIX_.'guest g
            LEFT JOIN '._DB_PREFIX_.'connections c ON c.id_guest = g.id_guest
            WHERE g.id_customer = a.id_customer
            ORDER BY c.date_add DESC
            LIMIT 1
        ) as connect, (
            SELECT rsk.date_kick_out
            FROM '._DB_PREFIX_.'rewards_sponsorship_kick_out rsk
            WHERE rsk.id_customer = a.id_customer
            GROUP BY rsk.id_customer
        ) as date_kick_out, (
            SELECT MAX(o.date_add)
            FROM '._DB_PREFIX_.'orders o
            WHERE o.id_customer = a.id_customer
        ) as last_purchase';

        // Check if we can add a customer
        if (Shop::isFeatureActive() && (Shop::getContext() == Shop::CONTEXT_ALL || Shop::getContext() == Shop::CONTEXT_GROUP)) {
            $this->can_add_customer = false;
        }

        self::$meaning_status = array(
            'open' => $this->l('Open'),
            'closed' => $this->l('Closed'),
            'pending1' => $this->l('Pending 1'),
            'pending2' => $this->l('Pending 2')
        );
    }
    
    public function renderForm()
    {
        /** @var Customer $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $genders = Gender::getGenders();
        $list_genders = array();
        foreach ($genders as $key => $gender) {
            /** @var Gender $gender */
            $list_genders[$key]['id'] = 'gender_'.$gender->id;
            $list_genders[$key]['value'] = $gender->id;
            $list_genders[$key]['label'] = $gender->name;
        }

        $years = Tools::dateYears();
        $months = Tools::dateMonths();
        $days = Tools::dateDays();
        
        $field_active = array(
                            'type' => 'switch',
                            'label' => $this->l('Enabled'),
                            'name' => 'active',
                            'required' => false,
                            'class' => 't',
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled')
                                )
                            ),
                            'hint' => $this->l('Enable or disable customer login.')
                        );

        $field_kick_out = array(
                            'type' => 'switch',
                            'label' => $this->l('Expulsar'),
                            'name' => 'kick_out',
                            'required' => false,
                            'class' => 't',
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled')
                                )
                            ),
                            'hint' => $this->l('Expulsar al cliente de la red.')
                        );
        if ( $this->context->employee->id_profile != 1 ) {
            if ( $obj->active == 0 ) {
                $field_active = array();
            }
            if ( $obj->kick_out == 1 ) {
                $field_kick_out = array();
            }
        }

        $groups = Group::getGroups($this->default_form_language, true);
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Customer'),
                'icon' => 'icon-user'
            ),
            'input' => array(
                array(
                    'type' => 'radio',
                    'label' => $this->l('Social title'),
                    'name' => 'id_gender',
                    'required' => false,
                    'class' => 't',
                    'values' => $list_genders
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('username'),
                    'name' => 'username',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('First name'),
                    'name' => 'firstname',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Last name'),
                    'name' => 'lastname',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Identificacion'),
                    'name' => 'dni',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('DNI / NIF / NIE')
                ),
                array(
                    'type' => 'text',
                    'prefix' => '<i class="icon-envelope-o"></i>',
                    'label' => $this->l('Email address'),
                    'name' => 'email',
                    'col' => '4',
                    'required' => true,
                    'autocomplete' => false
                ),
                array(
                    'type' => 'password',
                    'label' => $this->l('Password'),
                    'name' => 'passwd',
                    'required' => ($obj->id ? false : true),
                    'col' => '4',
                    'hint' => ($obj->id ? $this->l('Leave this field blank if there\'s no change.') :
                        sprintf($this->l('Password should be at least %s characters long.'), Validate::PASSWORD_LENGTH))
                ),
                array(
                    'type' => 'birthday',
                    'label' => $this->l('Birthday'),
                    'name' => 'birthday',
                    'options' => array(
                        'days' => $days,
                        'months' => $months,
                        'years' => $years
                    )
                ),
                $field_active,
                array(
                    'type' => 'switch',
                    'label' => $this->l('Newsletter'),
                    'name' => 'newsletter',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'newsletter_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'newsletter_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'disabled' =>  (bool)!Configuration::get('PS_CUSTOMER_NWSL'),
                    'hint' => $this->l('This customer will receive your newsletter via email.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Opt-in'),
                    'name' => 'optin',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'optin_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'optin_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'disabled' =>  (bool)!Configuration::get('PS_CUSTOMER_OPTIN'),
                    'hint' => $this->l('This customer will receive your ads via email.')
                ),
                $field_kick_out,
            )
        );

        // if we add a customer via fancybox (ajax), it's a customer and he doesn't need to be added to the visitor and guest groups
        if (Tools::isSubmit('addcustomer') && Tools::isSubmit('submitFormAjax')) {
            $visitor_group = Configuration::get('PS_UNIDENTIFIED_GROUP');
            $guest_group = Configuration::get('PS_GUEST_GROUP');
            foreach ($groups as $key => $g) {
                if (in_array($g['id_group'], array($visitor_group, $guest_group))) {
                    unset($groups[$key]);
                }
            }
        }

        $this->fields_form['input'] = array_merge(
            $this->fields_form['input'],
            array(
                array(
                    'type' => 'group',
                    'label' => $this->l('Group access'),
                    'name' => 'groupBox',
                    'values' => $groups,
                    'required' => true,
                    'col' => '6',
                    'hint' => $this->l('Select all the groups that you would like to apply to this customer.')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Default customer group'),
                    'name' => 'id_default_group',
                    'options' => array(
                        'query' => $groups,
                        'id' => 'id_group',
                        'name' => 'name'
                    ),
                    'col' => '4',
                    'hint' => array(
                        $this->l('This group will be the user\'s default group.'),
                        $this->l('Only the discount for the selected group will be applied to this customer.')
                    )
                )
            )
        );

        // if customer is a guest customer, password hasn't to be there
        if ($obj->id && ($obj->is_guest && $obj->id_default_group == Configuration::get('PS_GUEST_GROUP'))) {
            foreach ($this->fields_form['input'] as $k => $field) {
                if ($field['type'] == 'password') {
                    array_splice($this->fields_form['input'], $k, 1);
                }
            }
        }

        if (Configuration::get('PS_B2B_ENABLE')) {
            $risks = Risk::getRisks();

            $list_risks = array();
            foreach ($risks as $key => $risk) {
                /** @var Risk $risk */
                $list_risks[$key]['id_risk'] = (int)$risk->id;
                $list_risks[$key]['name'] = $risk->name;
            }

            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Company'),
                'name' => 'company'
            );
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->l('SIRET'),
                'name' => 'siret'
            );
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->l('APE'),
                'name' => 'ape'
            );
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Website'),
                'name' => 'website'
            );
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Allowed outstanding amount'),
                'name' => 'outstanding_allow_amount',
                'hint' => $this->l('Valid characters:').' 0-9',
                'suffix' => $this->context->currency->sign
            );
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Maximum number of payment days'),
                'name' => 'max_payment_days',
                'hint' => $this->l('Valid characters:').' 0-9'
            );
            $this->fields_form['input'][] = array(
                'type' => 'select',
                'label' => $this->l('Risk rating'),
                'name' => 'id_risk',
                'required' => false,
                'class' => 't',
                'options' => array(
                    'query' => $list_risks,
                    'id' => 'id_risk',
                    'name' => 'name'
                ),
            );
        }

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        $birthday = explode('-', $this->getFieldValue($obj, 'birthday'));

        $this->fields_value = array(
            'years' => $this->getFieldValue($obj, 'birthday') ? $birthday[0] : 0,
            'months' => $this->getFieldValue($obj, 'birthday') ? $birthday[1] : 0,
            'days' => $this->getFieldValue($obj, 'birthday') ? $birthday[2] : 0,
        );

        // Added values of object Group
        if (!Validate::isUnsignedId($obj->id)) {
            $customer_groups = array();
        } else {
            $customer_groups = $obj->getGroups();
        }
        $customer_groups_ids = array();
        if (is_array($customer_groups)) {
            foreach ($customer_groups as $customer_group) {
                $customer_groups_ids[] = $customer_group;
            }
        }

        // if empty $carrier_groups_ids : object creation : we set the default groups
        if (empty($customer_groups_ids)) {
            $preselected = array(Configuration::get('PS_UNIDENTIFIED_GROUP'), Configuration::get('PS_GUEST_GROUP'), Configuration::get('PS_CUSTOMER_GROUP'));
            $customer_groups_ids = array_merge($customer_groups_ids, $preselected);
        }

        foreach ($groups as $group) {
            $this->fields_value['groupBox_'.$group['id_group']] =
                Tools::getValue('groupBox_'.$group['id_group'], in_array($group['id_group'], $customer_groups_ids));
        }

        return AdminController::renderForm();
    }
    
    public function processUpdate()
    {
        if (Validate::isLoadedObject($this->object)) {
            $customer_email = strval(Tools::getValue('email'));

            // check if e-mail already used
            if ($customer_email != $this->object->email) {
                $customer = new Customer();
                if (Validate::isEmail($customer_email)) {
                    $customer->getByEmail($customer_email);
                    $customer->updateEmailSponsorship($this->object->id, $customer_email);
                }
                if (($customer->id) && ($customer->id != (int)$this->object->id)) {
                    $this->errors[] = Tools::displayError('An account already exists for this email address:').' '.$customer_email;
                }
            }

            return AdminController::processUpdate();
        } else {
            $this->errors[] = Tools::displayError('An error occurred while loading the object.').'
				<b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
        }
    }
    
    public function beforeAdd($customer)
    {
        $customer->id_shop = $this->context->shop->id;
        $customer->date_kick_out = date ( 'Y-m-d H:i:s' , strtotime ( '+30 day' , strtotime ( date("Y-m-d H:i:s") ) ) );
        $customer->warning_kick_out = 0;
    }
    
     public function processSave()
    {
        // Check that default group is selected
        if (!is_array(Tools::getValue('groupBox')) || !in_array(Tools::getValue('id_default_group'), Tools::getValue('groupBox'))) {
            $this->errors[] = Tools::displayError('A default customer group must be selected in group box.');
        }
        
        if ( empty(Tools::getValue('id_customer')) || Tools::getValue('id_customer') == "" || Tools::getValue('id_customer') == 0 ) {
            // Validate exist username
            if ( Customer::usernameExists( Tools::getValue("username") ) ) {
                $this->errors[] = Tools::displayError('El nombre de usuario ya se encuentra en uso.');
            }
            // Validate dni
            if ( Customer::dniExists( Tools::getValue("dni"),Tools::getValue('email') ) ) {
                $this->errors[] = Tools::displayError('El numero de identificacion ya se encuentra en uso.');
            }
        }
        // Check the requires fields which are settings in the BO
        $customer = new Customer();
        $this->errors = array_merge($this->errors, $customer->validateFieldsRequiredDatabase());
        $save = parent::processSave();
        require_once(_PS_ROOT_DIR_.'/kickoutcustomers.php');
        return $save;
    }
}

?>