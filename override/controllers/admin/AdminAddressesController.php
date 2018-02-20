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
 * @property Address $object
 */
class AdminAddressesController extends AdminAddressesControllerCore
{
    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Addresses'),
                'icon' => 'icon-envelope-alt'
            ),
            'input' => array(
                array(
                    'type' => 'text_customer',
                    'label' => $this->l('Customer'),
                    'name' => 'id_customer',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Identification Number'),
                    'name' => 'dni',
                    'required' => false,
                    'col' => '4',
                    'hint' => $this->l('DNI / NIF / NIE')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Address alias'),
                    'name' => 'alias',
                    'required' => true,
                    'default_value' => "Mi Direccion",
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Other'),
                    'name' => 'other',
                    'required' => false,
                    'cols' => 15,
                    'rows' => 3,
                    'hint' => $this->l('Forbidden characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'id_order'
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'address_type',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'back'
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        $this->fields_value['address_type'] = (int)Tools::getValue('address_type', 1);

        $id_customer = (int)Tools::getValue('id_customer');
        if (!$id_customer && Validate::isLoadedObject($this->object)) {
            $id_customer = $this->object->id_customer;
        }
        if ($id_customer) {
            $customer = new Customer((int)$id_customer);
            $token_customer = Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)$this->context->employee->id);
        }

        $this->tpl_form_vars = array(
            'customer' => isset($customer) ? $customer : null,
            'tokenCustomer' => isset($token_customer) ? $token_customer : null,
            'back_url' => urldecode(Tools::getValue('back'))
        );

        // Order address fields depending on country format
        $addresses_fields = $this->processAddressFormat();
        // we use  delivery address
        $addresses_fields = $addresses_fields['dlv_all_fields'];

        // get required field
        $required_fields = AddressFormat::getFieldsRequired();

        // Merge with field required
        $addresses_fields = array_unique(array_merge($addresses_fields, $required_fields));

        $temp_fields = array();

        foreach ($addresses_fields as $addr_field_item) {
            if ($addr_field_item == 'company') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Company'),
                    'name' => 'company',
                    'required' => in_array('company', $required_fields),
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                );
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('VAT number'),
                    'col' => '2',
                    'name' => 'vat_number',
                    'required' => in_array('vat_number', $required_fields)
                );
            } elseif ($addr_field_item == 'lastname') {
                if (isset($customer) &&
                    !Tools::isSubmit('submit'.strtoupper($this->table)) &&
                    Validate::isLoadedObject($customer) &&
                    !Validate::isLoadedObject($this->object)) {
                    $default_value = $customer->lastname;
                } else {
                    $default_value = '';
                }

                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Last Name'),
                    'name' => 'lastname',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' 0-9!&amp;lt;&amp;gt;,;?=+()@#"�{}_$%:',
                    'default_value' => $default_value,
                );
            } elseif ($addr_field_item == 'firstname') {
                if (isset($customer) &&
                    !Tools::isSubmit('submit'.strtoupper($this->table)) &&
                    Validate::isLoadedObject($customer) &&
                    !Validate::isLoadedObject($this->object)) {
                    $default_value = $customer->firstname;
                } else {
                    $default_value = '';
                }

                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('First Name'),
                    'name' => 'firstname',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' 0-9!&amp;lt;&amp;gt;,;?=+()@#"�{}_$%:',
                    'default_value' => $default_value,
                );
            } elseif ($addr_field_item == 'address1') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Address'),
                    'name' => 'address1',
                    'col' => '6',
                    'required' => true,
                );
            } elseif ($addr_field_item == 'address2') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Address').' (2)',
                    'name' => 'address2',
                    'col' => '6',
                    'required' => in_array('address2', $required_fields),
                );
            } elseif ($addr_field_item == 'postcode') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Zip/Postal Code'),
                    'name' => 'postcode',
                    'col' => '2',
                    'required' => true,
                );
            } elseif ($addr_field_item == 'city') {
                $temp_fields[] = array(
                    'type' => 'select',
                    'label' => $this->l('City'),
                    'name' => 'city',
                    'required' => true,
                    'col' => '4',
                    'options' => array(
                        'query' => City::getCities(),
                        'id' => 'ciudad',
                        'name' => 'ciudad'
                    )
                );
            } elseif ($addr_field_item == 'country' || $addr_field_item == 'Country:name') {
                $temp_fields[] = array(
                    'type' => 'select',
                    'label' => $this->l('Country'),
                    'name' => 'id_country',
                    'required' => in_array('Country:name', $required_fields) || in_array('country', $required_fields),
                    'col' => '4',
                    'default_value' => (int)$this->context->country->id,
                    'options' => array(
                        'query' => Country::getCountries($this->context->language->id, false, true),
                        'id' => 'id_country',
                        'name' => 'name'
                    )
                );
                /*$temp_fields[] = array(
                    'type' => 'select',
                    'label' => $this->l('State'),
                    'name' => 'id_state',
                    'required' => false,
                    'col' => '4',
                    'options' => array(
                        'query' => array(),
                        'id' => 'id_state',
                        'name' => 'name'
                    )
                );*/
            } elseif ($addr_field_item == 'phone') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Home phone'),
                    'name' => 'phone',
                    'required' => in_array('phone', $required_fields) || Configuration::get('PS_ONE_PHONE_AT_LEAST'),
                    'col' => '4',
                    'hint' => Configuration::get('PS_ONE_PHONE_AT_LEAST') ? sprintf($this->l('You must register at least one phone number.')) : ''
                );
            } elseif ($addr_field_item == 'phone_mobile') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->l('Mobile phone'),
                    'name' => 'phone_mobile',
                    'required' =>  in_array('phone_mobile', $required_fields) || Configuration::get('PS_ONE_PHONE_AT_LEAST'),
                    'col' => '4',
                    'hint' => Configuration::get('PS_ONE_PHONE_AT_LEAST') ? sprintf($this->l('You must register at least one phone number.')) : ''
                );
            }
        }

        // merge address format with the rest of the form
        array_splice($this->fields_form['input'], 3, 0, $temp_fields);

        return AdminController::renderForm();
    }
}

?>