<?php

require_once(_PS_MODULE_DIR_.'/allinone_rewards/allinone_rewards.php');

class AdminImportController extends AdminImportControllerCore
{
    public function __construct()
    {
        parent::__construct();

        $this->bootstrap = true;
        $this->entities = array(
            $this->l('Categories'),
            $this->l('Products'),
            $this->l('Combinations'),
            $this->l('Customers'),
            $this->l('Addresses'),
            $this->l('Manufacturers'),
            $this->l('Suppliers'),
            $this->l('Alias'),
            $this->l('Orders'),
        );

        // @since 1.5.0
        /*if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            $this->entities = array_merge(
                $this->entities,
                array(
                    $this->l('Supply Orders'),
                    $this->l('Supply Order Details'),
                )
            );
        }*/

        $this->entities = array_flip($this->entities);

        switch ((int)Tools::getValue('entity')) {
            case $this->entities[$this->l('Combinations')]:
                $this->required_fields = array(
                    'group',
                    'attribute'
                );

                $this->available_fields = array(
                    'no' => array('label' => $this->l('Ignore this column')),
                    'id_product' => array('label' => $this->l('Product ID')),
                    'product_reference' => array('label' => $this->l('Product Reference')),
                    'group' => array(
                        'label' => $this->l('Attribute (Name:Type:Position)').'*'
                    ),
                    'attribute' => array(
                        'label' => $this->l('Value (Value:Position)').'*'
                    ),
                    'supplier_reference' => array('label' => $this->l('Supplier reference')),
                    'reference' => array('label' => $this->l('Reference')),
                    'ean13' => array('label' => $this->l('EAN13')),
                    'upc' => array('label' => $this->l('UPC')),
                    'wholesale_price' => array('label' => $this->l('Wholesale price')),
                    'price' => array('label' => $this->l('Impact on price')),
                    'ecotax' => array('label' => $this->l('Ecotax')),
                    'quantity' => array('label' => $this->l('Quantity')),
                    'minimal_quantity' => array('label' => $this->l('Minimal quantity')),
                    'weight' => array('label' => $this->l('Impact on weight')),
                    'default_on' => array('label' => $this->l('Default (0 = No, 1 = Yes)')),
                    'available_date' => array('label' => $this->l('Combination availability date')),
                    'image_position' => array(
                        'label' => $this->l('Choose among product images by position (1,2,3...)')
                    ),
                    'image_url' => array('label' => $this->l('Image URLs (x,y,z...)')),
                    'delete_existing_images' => array(
                        'label' => $this->l('Delete existing images (0 = No, 1 = Yes).')
                    ),
                    'shop' => array(
                        'label' => $this->l('ID / Name of shop'),
                        'help' => $this->l('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.'),
                    ),
                    'advanced_stock_management' => array(
                        'label' => $this->l('Advanced Stock Management'),
                        'help' => $this->l('Enable Advanced Stock Management on product (0 = No, 1 = Yes)')
                    ),
                    'depends_on_stock' => array(
                        'label' => $this->l('Depends on stock'),
                        'help' => $this->l('0 = Use quantity set in product, 1 = Use quantity from warehouse.')
                    ),
                    'warehouse' => array(
                        'label' => $this->l('Warehouse'),
                        'help' => $this->l('ID of the warehouse to set as storage.')
                    ),
                );

                self::$default_values = array(
                    'reference' => '',
                    'supplier_reference' => '',
                    'ean13' => '',
                    'upc' => '',
                    'wholesale_price' => 0,
                    'price' => 0,
                    'ecotax' => 0,
                    'quantity' => 0,
                    'minimal_quantity' => 1,
                    'weight' => 0,
                    'default_on' => 0,
                    'advanced_stock_management' => 0,
                    'depends_on_stock' => 0,
                    'available_date' => date('Y-m-d')
                );
            break;

            case $this->entities[$this->l('Categories')]:
                $this->available_fields = array(
                    'no' => array('label' => $this->l('Ignore this column')),
                    'id' => array('label' => $this->l('ID')),
                    'active' => array('label' => $this->l('Active (0/1)')),
                    'name' => array('label' => $this->l('Name')),
                    'parent' => array('label' => $this->l('Parent category')),
                    'is_root_category' => array(
                        'label' => $this->l('Root category (0/1)'),
                        'help' => $this->l('A category root is where a category tree can begin. This is used with multistore.')
                        ),
                    'description' => array('label' => $this->l('Description')),
                    'meta_title' => array('label' => $this->l('Meta title')),
                    'meta_keywords' => array('label' => $this->l('Meta keywords')),
                    'meta_description' => array('label' => $this->l('Meta description')),
                    'link_rewrite' => array('label' => $this->l('URL rewritten')),
                    'image' => array('label' => $this->l('Image URL')),
                    'shop' => array(
                        'label' => $this->l('ID / Name of shop'),
                        'help' => $this->l('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.'),
                    ),
                );

                self::$default_values = array(
                    'active' => '1',
                    'parent' => Configuration::get('PS_HOME_CATEGORY'),
                    'link_rewrite' => ''
                );
            break;

            case $this->entities[$this->l('Products')]:
                self::$validators['image'] = array(
                    'AdminImportController',
                    'split'
                );

                $this->available_fields = array(
                    'no' => array('label' => $this->l('Ignore this column')),
                    'id' => array('label' => $this->l('ID')),
                    'active' => array('label' => $this->l('Active (0/1)')),
                    'name' => array('label' => $this->l('Name')),
                    'category' => array('label' => $this->l('Categories (x,y,z...)')),
                    'price_tex' => array('label' => $this->l('Price tax excluded')),
                    'price_tin' => array('label' => $this->l('Price tax included')),
                    'id_tax_rules_group' => array('label' => $this->l('Tax rules ID')),
                    'wholesale_price' => array('label' => $this->l('Wholesale price')),
                    'on_sale' => array('label' => $this->l('On sale (0/1)')),
                    'reduction_price' => array('label' => $this->l('Discount amount')),
                    'reduction_percent' => array('label' => $this->l('Discount percent')),
                    'reduction_from' => array('label' => $this->l('Discount from (yyyy-mm-dd)')),
                    'reduction_to' => array('label' => $this->l('Discount to (yyyy-mm-dd)')),
                    'reference' => array('label' => $this->l('Reference #')),
                    'supplier_reference' => array('label' => $this->l('Supplier reference #')),
                    'supplier' => array('label' => $this->l('Supplier')),
                    'manufacturer' => array('label' => $this->l('Manufacturer')),
                    'ean13' => array('label' => $this->l('EAN13')),
                    'upc' => array('label' => $this->l('UPC')),
                    'ecotax' => array('label' => $this->l('Ecotax')),
                    'width' => array('label' => $this->l('Width')),
                    'height' => array('label' => $this->l('Height')),
                    'depth' => array('label' => $this->l('Depth')),
                    'weight' => array('label' => $this->l('Weight')),
                    'quantity' => array('label' => $this->l('Quantity')),
                    'minimal_quantity' => array('label' => $this->l('Minimal quantity')),
                    'visibility' => array('label' => $this->l('Visibility')),
                    'additional_shipping_cost' => array('label' => $this->l('Additional shipping cost')),
                    'unity' => array('label' => $this->l('Unit for the unit price')),
                    'unit_price' => array('label' => $this->l('Unit price')),
                    'description_short' => array('label' => $this->l('Short description')),
                    'description' => array('label' => $this->l('Description')),
                    'tags' => array('label' => $this->l('Tags (x,y,z...)')),
                    'meta_title' => array('label' => $this->l('Meta title')),
                    'meta_keywords' => array('label' => $this->l('Meta keywords')),
                    'meta_description' => array('label' => $this->l('Meta description')),
                    'link_rewrite' => array('label' => $this->l('URL rewritten')),
                    'available_now' => array('label' => $this->l('Text when in stock')),
                    'available_later' => array('label' => $this->l('Text when backorder allowed')),
                    'available_for_order' => array('label' => $this->l('Available for order (0 = No, 1 = Yes)')),
                    'available_date' => array('label' => $this->l('Product availability date')),
                    'date_add' => array('label' => $this->l('Product creation date')),
                    'show_price' => array('label' => $this->l('Show price (0 = No, 1 = Yes)')),
                    'image' => array('label' => $this->l('Image URLs (x,y,z...)')),
                    'delete_existing_images' => array(
                        'label' => $this->l('Delete existing images (0 = No, 1 = Yes)')
                    ),
                    'features' => array('label' => $this->l('Feature (Name:Value:Position:Customized)')),
                    'online_only' => array('label' => $this->l('Available online only (0 = No, 1 = Yes)')),
                    'condition' => array('label' => $this->l('Condition')),
                    'customizable' => array('label' => $this->l('Customizable (0 = No, 1 = Yes)')),
                    'uploadable_files' => array('label' => $this->l('Uploadable files (0 = No, 1 = Yes)')),
                    'text_fields' => array('label' => $this->l('Text fields (0 = No, 1 = Yes)')),
                    'out_of_stock' => array('label' => $this->l('Action when out of stock')),
                    'shop' => array(
                        'label' => $this->l('ID / Name of shop'),
                        'help' => $this->l('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.'),
                    ),
                    'advanced_stock_management' => array(
                        'label' => $this->l('Advanced Stock Management'),
                        'help' => $this->l('Enable Advanced Stock Management on product (0 = No, 1 = Yes).')
                    ),
                    'depends_on_stock' => array(
                        'label' => $this->l('Depends on stock'),
                        'help' => $this->l('0 = Use quantity set in product, 1 = Use quantity from warehouse.')
                    ),
                    'warehouse' => array(
                        'label' => $this->l('Warehouse'),
                        'help' => $this->l('ID of the warehouse to set as storage.')
                    ),
                );

                self::$default_values = array(
                    'id_category' => array((int)Configuration::get('PS_HOME_CATEGORY')),
                    'id_category_default' => null,
                    'active' => '1',
                    'width' => 0.000000,
                    'height' => 0.000000,
                    'depth' => 0.000000,
                    'weight' => 0.000000,
                    'visibility' => 'both',
                    'additional_shipping_cost' => 0.00,
                    'unit_price' => 0,
                    'quantity' => 0,
                    'minimal_quantity' => 1,
                    'price' => 0,
                    'id_tax_rules_group' => 0,
                    'description_short' => array((int)Configuration::get('PS_LANG_DEFAULT') => ''),
                    'link_rewrite' => array((int)Configuration::get('PS_LANG_DEFAULT') => ''),
                    'online_only' => 0,
                    'condition' => 'new',
                    'available_date' => date('Y-m-d'),
                    'date_add' => date('Y-m-d H:i:s'),
                    'date_upd' => date('Y-m-d H:i:s'),
                    'customizable' => 0,
                    'uploadable_files' => 0,
                    'text_fields' => 0,
                    'advanced_stock_management' => 0,
                    'depends_on_stock' => 0,
                );
            break;

            case $this->entities[$this->l('Customers')]:
                //Overwrite required_fields AS only email is required whereas other entities
                $this->required_fields = array('active', 'id_gender', 'username', 'email', 'type_document', 'dni', 'birthday', 'lastname', 'firstname', 'newsletter', 'optin', 'address1', 'address2', 'city', 'phone', 'phone_mobile');

                $this->available_fields = array(
                    'no' => array('label' => $this->l('Ignore this column')),
                    'active' => array('label' => 'Active (0/1)'),
                    'id_gender' => array('label' => 'Titles ID (Mr=1 , Ms=2)'),
                    'username' => array('label' => 'Username'),
                    'email' => array('label' => 'Email'),
                    'type_document' => array('label' => 'Type Document (CC=0,NIT=1,CE=2)'),
                    'dni' => array('label' => 'DNI'),
                    'birthday' => array('label' => 'Birthday (yyyy-mm-dd)'),
                    'lastname' => array('label' => 'Last Name'),
                    'firstname' => array('label' => 'First Name'),
                    'newsletter' => array('label' => 'Newsletter (0/1)'),
                    'optin' => array('label' => 'Opt-in (0/1)'),
                    'address1' => array('label' => 'Address'),
                    'address2' => array('label' => 'Address 2'),
                    'city' => array('label' => 'City'),
                    'phone' => array('label' => 'Phone'),
                    'phone_mobile' => array('label' => 'Phone Mobile'),
                );

                self::$default_values = array(
                    'id_shop' => Configuration::get('PS_SHOP_DEFAULT'),
                    'group' => 'Cliente,Afiliado',
                    'id_default_group' => '4',
                    'id_country' => '69',
                    'alias' => 'Mi Direccion',
                );
            break;
        
            case $this->entities[$this->l('Orders')]:
                //Overwrite required_fields AS only email is required whereas other entities
                $this->required_fields = array(
                    'id_customer',
                    'id_products',
                    'payment'
                );
                
                $this->available_fields = array(
                    'no' => array('label' => $this->l('Ignore this column')),
                    'id_customer' => array('label' => $this->l('Customer ID')),
                    'id_products' => array('label' => $this->l('Product ID')),
                    'payment' => array('label' => 'payment (pedido gratuito=1 , tarjeta credito=2)')
                );

                self::$default_values = array(
                    'id_shop' => Configuration::get('PS_SHOP_DEFAULT'),
                    'current_state' => '1',
                );
            break;

            case $this->entities[$this->l('Addresses')]:
                //Overwrite required_fields
                $this->required_fields = array(
                    'alias',
                    'lastname',
                    'firstname',
                    'address1',
                    'postcode',
                    'country',
                    'customer_email',
                    'city'
                );

                $this->available_fields = array(
                    'no' => array('label' => $this->l('Ignore this column')),
                    'id' => array('label' => $this->l('ID')),
                    'alias' => array('label' => $this->l('Alias *')),
                    'active' => array('label' => $this->l('Active  (0/1)')),
                    'customer_email' => array('label' => $this->l('Customer email *')),
                    'id_customer' => array('label' => $this->l('Customer ID')),
                    'manufacturer' => array('label' => $this->l('Manufacturer')),
                    'supplier' => array('label' => $this->l('Supplier')),
                    'company' => array('label' => $this->l('Company')),
                    'lastname' => array('label' => $this->l('Last Name *')),
                    'firstname' => array('label' => $this->l('First Name *')),
                    'address1' => array('label' => $this->l('Address 1 *')),
                    'address2' => array('label' => $this->l('Address 2')),
                    'postcode' => array('label' => $this->l('Zip/postal code *')),
                    'city' => array('label' => $this->l('City *')),
                    'country' => array('label' => $this->l('Country *')),
                    'state' => array('label' => $this->l('State')),
                    'other' => array('label' => $this->l('Other')),
                    'phone' => array('label' => $this->l('Phone')),
                    'phone_mobile' => array('label' => $this->l('Mobile Phone')),
                    'vat_number' => array('label' => $this->l('VAT number')),
                    'dni' => array('label' => $this->l('DNI/NIF/NIE')),
                );

                self::$default_values = array(
                    'alias' => 'Alias',
                    'postcode' => 'X'
                );
            break;
            case $this->entities[$this->l('Manufacturers')]:
            case $this->entities[$this->l('Suppliers')]:
                //Overwrite validators AS name is not MultiLangField
                self::$validators = array(
                    'description' => array('AdminImportController', 'createMultiLangField'),
                    'short_description' => array('AdminImportController', 'createMultiLangField'),
                    'meta_title' => array('AdminImportController', 'createMultiLangField'),
                    'meta_keywords' => array('AdminImportController', 'createMultiLangField'),
                    'meta_description' => array('AdminImportController', 'createMultiLangField'),
                );

                $this->available_fields = array(
                    'no' => array('label' => $this->l('Ignore this column')),
                    'id' => array('label' => $this->l('ID')),
                    'active' => array('label' => $this->l('Active (0/1)')),
                    'name' => array('label' => $this->l('Name')),
                    'description' => array('label' => $this->l('Description')),
                    'short_description' => array('label' => $this->l('Short description')),
                    'meta_title' => array('label' => $this->l('Meta title')),
                    'meta_keywords' => array('label' => $this->l('Meta keywords')),
                    'meta_description' => array('label' => $this->l('Meta description')),
                    'image' => array('label' => $this->l('Image URL')),
                    'shop' => array(
                        'label' => $this->l('ID / Name of group shop'),
                        'help' => $this->l('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.'),
                    ),
                );

                self::$default_values = array(
                    'shop' => Shop::getGroupFromShop(Configuration::get('PS_SHOP_DEFAULT')),
                );
            break;
            case $this->entities[$this->l('Alias')]:
                //Overwrite required_fields
                $this->required_fields = array(
                    'alias',
                    'search',
                );
                $this->available_fields = array(
                    'no' => array('label' => $this->l('Ignore this column')),
                    'id' => array('label' => $this->l('ID')),
                    'alias' => array('label' => $this->l('Alias *')),
                    'search' => array('label' => $this->l('Search *')),
                    'active' => array('label' => $this->l('Active')),
                    );
                self::$default_values = array(
                    'active' => '1',
                );
            break;
        }

        // @since 1.5.0
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            switch ((int)Tools::getValue('entity')) {
                case $this->entities[$this->l('Supply Orders')]:
                    // required fields
                    $this->required_fields = array(
                        'id_supplier',
                        'id_warehouse',
                        'reference',
                        'date_delivery_expected',
                    );
                    // available fields
                    $this->available_fields = array(
                        'no' => array('label' => $this->l('Ignore this column')),
                        'id' => array('label' => $this->l('ID')),
                        'id_supplier' => array('label' => $this->l('Supplier ID *')),
                        'id_lang' => array('label' => $this->l('Lang ID')),
                        'id_warehouse' => array('label' => $this->l('Warehouse ID *')),
                        'id_currency' => array('label' => $this->l('Currency ID *')),
                        'reference' => array('label' => $this->l('Supply Order Reference *')),
                        'date_delivery_expected' => array('label' => $this->l('Delivery Date (Y-M-D)*')),
                        'discount_rate' => array('label' => $this->l('Discount Rate')),
                        'is_template' => array('label' => $this->l('Template')),
                    );
                    // default values
                    self::$default_values = array(
                        'id_lang' => (int)Configuration::get('PS_LANG_DEFAULT'),
                        'id_currency' => Currency::getDefaultCurrency()->id,
                        'discount_rate' => '0',
                        'is_template' => '0',
                    );
                break;
                case $this->entities[$this->l('Supply Order Details')]:
                    // required fields
                    $this->required_fields = array(
                        'supply_order_reference',
                        'id_product',
                        'unit_price_te',
                        'quantity_expected',
                    );
                    // available fields
                    $this->available_fields = array(
                        'no' => array('label' => $this->l('Ignore this column')),
                        'supply_order_reference' => array('label' => $this->l('Supply Order Reference *')),
                        'id_product' => array('label' => $this->l('Product ID *')),
                        'id_product_attribute' => array('label' => $this->l('Product Attribute ID')),
                        'unit_price_te' => array('label' => $this->l('Unit Price (tax excl.)*')),
                        'quantity_expected' => array('label' => $this->l('Quantity Expected *')),
                        'discount_rate' => array('label' => $this->l('Discount Rate')),
                        'tax_rate' => array('label' => $this->l('Tax Rate')),
                    );
                    // default values
                    self::$default_values = array(
                        'discount_rate' => '0',
                        'tax_rate' => '0',
                    );
                break;

            }
        }

        $this->separator = ($separator = Tools::substr(strval(trim(Tools::getValue('separator'))), 0, 1)) ? $separator :  ';';
        $this->multiple_value_separator = ($separator = Tools::substr(strval(trim(Tools::getValue('multiple_value_separator'))), 0, 1)) ? $separator :  ',';
    }
    
    public function postProcess()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = Tools::displayError('This functionality has been disabled.');
            return;
        }

        if (Tools::isSubmit('import')) {
            // Check if the CSV file exist
            if (Tools::getValue('csv')) {
                $shop_is_feature_active = Shop::isFeatureActive();
                // If i am a superadmin, i can truncate table
                if ((($shop_is_feature_active && $this->context->employee->isSuperAdmin()) || !$shop_is_feature_active) && Tools::getValue('truncate')) {
                    $this->truncateTables((int)Tools::getValue('entity'));
                }
                $import_type = false;
                Db::getInstance()->disableCache();
                switch ((int)Tools::getValue('entity')) {
                    case $this->entities[$import_type = $this->l('Categories')]:
                        $this->categoryImport();
                        $this->clearSmartyCache();
                        break;
                    case $this->entities[$import_type = $this->l('Products')]:
                        $this->productImport();
                        $this->clearSmartyCache();
                        break;
                    case $this->entities[$import_type = $this->l('Customers')]:
                        //$this->customerImport();
                        break;
                    case $this->entities[$import_type = $this->l('Orders')]:
                        $this->ordersImport();
                        break;
                    case $this->entities[$import_type = $this->l('Addresses')]:
                        $this->addressImport();
                        break;
                    case $this->entities[$import_type = $this->l('Combinations')]:
                        $this->attributeImport();
                        $this->clearSmartyCache();
                        break;
                    case $this->entities[$import_type = $this->l('Manufacturers')]:
                        $this->manufacturerImport();
                        $this->clearSmartyCache();
                        break;
                    case $this->entities[$import_type = $this->l('Suppliers')]:
                        $this->supplierImport();
                        $this->clearSmartyCache();
                        break;
                    case $this->entities[$import_type = $this->l('Alias')]:
                        $this->aliasImport();
                        break;
                }

                // @since 1.5.0
                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                    switch ((int)Tools::getValue('entity')) {
                        case $this->entities[$import_type = $this->l('Supply Orders')]:
                            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                                $this->supplyOrdersImport();
                            }
                            break;
                        case $this->entities[$import_type = $this->l('Supply Order Details')]:
                            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                                $this->supplyOrdersDetailsImport();
                            }
                            break;
                    }
                }

                if ($import_type !== false) {
                    $log_message = sprintf($this->l('%s import', 'AdminTab', false, false), $import_type);
                    if (Tools::getValue('truncate')) {
                        $log_message .= ' '.$this->l('with truncate', 'AdminTab', false, false);
                    }
                    PrestaShopLogger::addLog($log_message, 1, null, $import_type, null, true, (int)$this->context->employee->id);
                }
            } else {
                $this->errors[] = $this->l('You must upload a file in order to proceed to the next step');
            }
        } elseif ($filename = Tools::getValue('csvfilename')) {
            $filename = urldecode($filename);
            $file = AdminImportController::getPath(basename($filename));
            if (realpath(dirname($file)) != realpath(AdminImportController::getPath())) {
                exit();
            }
            if (!empty($filename)) {
                $b_name = basename($filename);
                if (Tools::getValue('delete') && file_exists($file)) {
                    @unlink($file);
                } elseif (file_exists($file)) {
                    $b_name = explode('.', $b_name);
                    $b_name = strtolower($b_name[count($b_name) - 1]);
                    $mime_types = array('csv' => 'text/csv');

                    if (isset($mime_types[$b_name])) {
                        $mime_type = $mime_types[$b_name];
                    } else {
                        $mime_type = 'application/octet-stream';
                    }

                    if (ob_get_level() && ob_get_length() > 0) {
                        ob_end_clean();
                    }

                    header('Content-Transfer-Encoding: binary');
                    header('Content-Type: '.$mime_type);
                    header('Content-Length: '.filesize($file));
                    header('Content-Disposition: attachment; filename="'.$filename.'"');
                    $fp = fopen($file, 'rb');
                    while (is_resource($fp) && !feof($fp)) {
                        echo fgets($fp, 16384);
                    }
                    exit;
                }
            }
        }
        Db::getInstance()->enableCache();
        return parent::postProcess();
    }
    
    public function customerImport()
    {
        $this->receiveTab();
        $handle = $this->openCsvFile();
        $default_language_id = (int)Configuration::get('PS_LANG_DEFAULT');
        $id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
        if (!Validate::isUnsignedId($id_lang)) {
            $id_lang = $default_language_id;
        }
        AdminImportController::setLocale();

        $shop_is_feature_active = Shop::isFeatureActive();
        $convert = Tools::getValue('convert');
        $force_ids = Tools::getValue('forceIDs');
        
        $number_to_import = 0;
        for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator); $current_line++) {
            $number_to_import++;
        }
        $this->closeCsvFile($handle);

        if ( $number_to_import <= 51 ) {
            $handle = $this->openCsvFile();
            for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator); $current_line++) {
            
                $query = "SELECT
                            c.id_customer,
                            (2 - COUNT(rs.id_sponsorship)) pendingsinvitation
                        FROM "._DB_PREFIX_."customer c
                        LEFT JOIN "._DB_PREFIX_."rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                        LEFT JOIN "._DB_PREFIX_."customer_group cg ON ( c.id_customer = cg.id_customer AND cg.id_group = 4 )
                        WHERE c.active = 1
                        AND c.kick_out = 0
                        AND c.autoaddnetwork = 0
                        GROUP BY c.id_customer
                        HAVING pendingsinvitation = 2
                        ORDER BY c.date_add ASC
                        LIMIT 1";
                $sponsor = Db::getInstance()->executeS($query);
                $sponsor = $sponsor[0];

                if ( !empty($sponsor) && $sponsor['id_customer'] != "" )  {
                    if ($convert) {
                        $line = $this->utf8EncodeArray($line);
                    }
                    $info = AdminImportController::getMaskedRow($line);

                    AdminImportController::setDefaultValues($info);

                    if ($force_ids && isset($info['id']) && (int)$info['id']) {
                        $customer = new Customer((int)$info['id']);
                    } else {
                        if (array_key_exists('id', $info) && (int)$info['id'] && Customer::customerIdExistsStatic((int)$info['id'])) {
                            $customer = new Customer((int)$info['id']);
                        } else {
                            $customer = new Customer();
                        }
                    }
                    
                    $customer->date_kick_out = date ( 'Y-m-d H:i:s' , strtotime ( '+30 day' , strtotime ( date("Y-m-d H:i:s") ) ) );
                    $customer->warning_kick_out = 0;

                    $customer_exist = false;

                    if (array_key_exists('id', $info) && (int)$info['id'] && Customer::customerIdExistsStatic((int)$info['id']) && Validate::isLoadedObject($customer)) {
                        $current_id_customer = (int)$customer->id;
                        $current_id_shop = (int)$customer->id_shop;
                        $current_id_shop_group = (int)$customer->id_shop_group;
                        $customer_exist = true;
                        $customer_groups = $customer->getGroups();
                        $addresses = $customer->getAddresses((int)Configuration::get('PS_LANG_DEFAULT'));
                    }

                    // Group Importation
                    if (isset($info['group']) && !empty($info['group'])) {
                        foreach (explode($this->multiple_value_separator, $info['group']) as $key => $group) {
                            $group = trim($group);
                            if (empty($group)) {
                                continue;
                            }
                            $id_group = false;
                            if (is_numeric($group) && $group) {
                                $my_group = new Group((int)$group);
                                if (Validate::isLoadedObject($my_group)) {
                                    $customer_groups[] = (int)$group;
                                }
                                continue;
                            }
                            $my_group = Group::searchByName($group);
                            if (isset($my_group['id_group']) && $my_group['id_group']) {
                                $id_group = (int)$my_group['id_group'];
                            }
                            if (!$id_group) {
                                $my_group = new Group();
                                $my_group->name = array($id_lang => $group);
                                if ($id_lang != $default_language_id) {
                                    $my_group->name = $my_group->name + array($default_language_id => $group);
                                }
                                $my_group->price_display_method = 1;
                                $my_group->add();
                                if (Validate::isLoadedObject($my_group)) {
                                    $id_group = (int)$my_group->id;
                                }
                            }
                            if ($id_group) {
                                $customer_groups[] = (int)$id_group;
                            }
                        }
                    } elseif (empty($info['group']) && isset($customer->id) && $customer->id) {
                        $customer_groups = array(0 => Configuration::get('PS_CUSTOMER_GROUP'));
                    }

                    AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $customer);

                    $passwd = substr(base64_encode($info['email']), 0, 6);
                    $customer->passwd = $passwd;
                    if ($customer->passwd) {
                        $customer->passwd = Tools::encrypt($customer->passwd);
                    }

                    $id_shop_list = explode($this->multiple_value_separator, $customer->id_shop);
                    $customers_shop = array();
                    $customers_shop['shared'] = array();
                    $default_shop = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));
                    if ($shop_is_feature_active && $id_shop_list) {
                        foreach ($id_shop_list as $id_shop) {
                            if (empty($id_shop)) {
                                continue;
                            }
                            $shop = new Shop((int)$id_shop);
                            $group_shop = $shop->getGroup();
                            if ($group_shop->share_customer) {
                                if (!in_array($group_shop->id, $customers_shop['shared'])) {
                                    $customers_shop['shared'][(int)$id_shop] = $group_shop->id;
                                }
                            } else {
                                $customers_shop[(int)$id_shop] = $group_shop->id;
                            }
                        }
                    } else {
                        $default_shop = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));
                        $default_shop->getGroup();
                        $customers_shop[$default_shop->id] = $default_shop->getGroup()->id;
                    }

                    //set temporally for validate field
                    $customer->id_shop = $default_shop->id;
                    $customer->id_shop_group = $default_shop->getGroup()->id;
                    if (isset($info['id_default_group']) && !empty($info['id_default_group']) && !is_numeric($info['id_default_group'])) {
                        $info['id_default_group'] = trim($info['id_default_group']);
                        $my_group = Group::searchByName($info['id_default_group']);
                        if (isset($my_group['id_group']) && $my_group['id_group']) {
                            $info['id_default_group'] = (int)$my_group['id_group'];
                        }
                    }
                    $my_group = new Group($customer->id_default_group);
                    if (!Validate::isLoadedObject($my_group)) {
                        $customer->id_default_group = (int)Configuration::get('PS_CUSTOMER_GROUP');
                    }
                    $customer_groups[] = (int)$customer->id_default_group;
                    $customer_groups = array_flip(array_flip($customer_groups));
                    $res = false;
                    if (($field_error = $customer->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                        ($lang_field_error = $customer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true) {
                        $res = true;
                        foreach ($customers_shop as $id_shop => $id_group) {
                            $customer->force_id = (bool)$force_ids;
                            if ($id_shop == 'shared') {
                                foreach ($id_group as $key => $id) {
                                    $customer->id_shop = (int)$key;
                                    $customer->id_shop_group = (int)$id;
                                    if ($customer_exist && ((int)$current_id_shop_group == (int)$id || in_array($current_id_shop, ShopGroup::getShopsFromGroup($id)))) {
                                        $customer->id = (int)$current_id_customer;
                                        $res &= $customer->update();
                                    } else {
                                        $res &= $customer->add();
                                        if (isset($addresses)) {
                                            foreach ($addresses as $address) {
                                                $address['id_customer'] = $customer->id;
                                                unset($address['country'], $address['state'], $address['state_iso'], $address['id_address']);
                                                Db::getInstance()->insert('address', $address, false, false);
                                            }
                                        }
                                    }
                                    if ($res && isset($customer_groups)) {
                                        $customer->updateGroup($customer_groups);
                                    }
                                }
                            } else {
                                $customer->id_shop = $id_shop;
                                $customer->id_shop_group = $id_group;
                                if ($customer_exist && (int)$id_shop == (int)$current_id_shop) {
                                    $customer->id = (int)$current_id_customer;
                                    $res &= $customer->update();
                                } else {
                                    $res &= $customer->add();
                                    if (isset($addresses)) {
                                        foreach ($addresses as $address) {
                                            $address['id_customer'] = $customer->id;
                                            unset($address['country'], $address['state'], $address['state_iso'], $address['id_address']);
                                            Db::getInstance()->insert('address', $address, false, false);
                                        }
                                    }
                                }
                                if ($res && isset($customer_groups)) {
                                    $customer->updateGroup($customer_groups);
                                }
                            }
                        }
                    }

                    if (isset($customer_groups)) {
                        unset($customer_groups);
                    }
                    if (isset($current_id_customer)) {
                        unset($current_id_customer);
                    }
                    if (isset($current_id_shop)) {
                        unset($current_id_shop);
                    }
                    if (isset($current_id_shop_group)) {
                        unset($current_id_shop_group);
                    }
                    if (isset($addresses)) {
                        unset($addresses);
                    }

                    if (!$res) {
                        $this->errors[] = sprintf(
                            Tools::displayError('%1$s cannot be saved'),
                            $info['email'],
                            (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null'
                        );
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                            Db::getInstance()->getMsgError();
                    } else {
                        // Assing Sponsor
                        $sponsorship = new RewardsSponsorshipModel();
                        $sponsorship->id_sponsor = $sponsor['id_customer'];
                        $sponsorship->id_customer = $customer->id;
                        $sponsorship->firstname = $customer->firstname;
                        $sponsorship->lastname = $customer->lastname;
                        $sponsorship->channel = 1;
                        $sponsorship->email = $customer->email;
                        $sponsorship->save();

                        // Assing Address
                        $address = new Address();
                        $address->id_country = $info['id_country'];
                        $address->id_customer = $customer->id;
                        $address->alias = $info['alias'];
                        $address->lastname = $customer->lastname;
                        $address->firstname = $customer->firstname;
                        $address->address1 = $info['address1'];
                        $address->address2 = $info['address2'];
                        $address->city = $info['city'];
                        $address->phone = $info['phone'];
                        $address->phone_mobile = $info['phone_mobile'];
                        $address->type_document = $info['type_document'];
                        if ( $info['type_document'] == 1 ) {
                            $dni = explode("-", $customer->dni);
                            $address->dni = $dni[0];
                            $address->checkdigit = $dni[1];
                        } else {
                            $address->dni = $customer->dni;
                            $address->checkdigit = 0;
                        }
                        $address->active = 1;
                        $address->add();
                        
                        $merchants_featured = Manufacturer::getManufacturersFeatured();
                        $merchant = array_slice($merchants_featured, 0, 4);
                        
                        $link = new Link();
                        $table_merchants_featured = '<table cellspacing="10">
                                                        <tr>
                                                            <td>
                                                                <a href="'.$link->getProductLink($merchant[0]['id_product'], $merchant[0]['link_rewrite']).'" title="'.$merchant[0]['name'].'">
                                                                    <div style="background: url('._S3_PATH_.'m/m/'.$merchant[0]['id_manufacturer'].'.jpg) no-repeat; background-size: 100% 100%;">
                                                                        <div style="height: 232px; display: table; text-align: center; min-width: 100%; padding: 10px;">
                                                                            <div style="display: table-cell; vertical-align: middle;">
                                                                                <img src="'._S3_PATH_.'m/'.$merchant[0]['id_manufacturer'].'.jpg" alt="'.$merchant[0]['name'].'" title="'.$merchant[0]['name'].'" style="max-width: 70%;">
                                                                            </div>    
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="'.$link->getProductLink($merchant[1]['id_product'], $merchant[1]['link_rewrite']).'" title="'.$merchant[1]['name'].'">
                                                                    <div style="background: url('._S3_PATH_.'m/m/'.$merchant[1]['id_manufacturer'].'.jpg) no-repeat; background-size: 100% 100%;">
                                                                        <div style="height: 232px; display: table; text-align: center; min-width: 100%; padding: 10px;">
                                                                            <div style="display: table-cell; vertical-align: middle;">
                                                                                <img src="'._S3_PATH_.'m/'.$merchant[1]['id_manufacturer'].'.jpg" alt="'.$merchant[1]['name'].'" title="'.$merchant[1]['name'].'" style="max-width: 70%;">
                                                                            </div>    
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <a href="'.$link->getProductLink($merchant[2]['id_product'], $merchant[2]['link_rewrite']).'" title="'.$merchant[2]['name'].'">
                                                                    <div style="background: url('._S3_PATH_.'m/m/'.$merchant[2]['id_manufacturer'].'.jpg) no-repeat; background-size: 100% 100%;">
                                                                        <div style="height: 232px; display: table; text-align: center; min-width: 100%; padding: 10px;">
                                                                            <div style="display: table-cell; vertical-align: middle;">
                                                                                <img src="'._S3_PATH_.'m/'.$merchant[2]['id_manufacturer'].'.jpg" alt="'.$merchant[2]['name'].'" title="'.$merchant[2]['name'].'" style="max-width: 70%;">
                                                                            </div>    
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="'.$link->getProductLink($merchant[3]['id_product'], $merchant[3]['link_rewrite']).'" title="'.$merchant[3]['name'].'">
                                                                    <div style="background: url('._S3_PATH_.'m/m/'.$merchant[3]['id_manufacturer'].'.jpg) no-repeat; background-size: 100% 100%;">
                                                                        <div style="height: 232px; display: table; text-align: center; min-width: 100%; padding: 10px;">
                                                                            <div style="display: table-cell; vertical-align: middle;">
                                                                                <img src="'._S3_PATH_.'m/'.$merchant[3]['id_manufacturer'].'.jpg" alt="'.$merchant[3]['name'].'" title="'.$merchant[3]['name'].'" style="max-width: 70%;">
                                                                            </div>    
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </table>';

                        // Welcome Email
                        $vars = array(
                            '{username}' => $customer->username,
                            '{password}' => $passwd,
                            '{firstname}' => $customer->firstname,
                            '{lastname}' => $customer->lastname,
                            '{dni}' => $customer->dni,
                            '{birthdate}' => $customer->birthday,
                            '{address}' => $address->address1,
                            '{phone}' => $address->phone_mobile,
                            '{merchants_featured}' => $table_merchants_featured,
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
                        $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject),$vars, $customer->email, $customer->firstname.' '.$customer->lastname);
        
                        /*Mail::Send(
                            Context::getContext()->language->id,
                            'welcome_fluzfluz',
                            'Bienvenido a FluzFluz',
                            $vars,
                            $customer->email,
                            $customer->firstname
                        );*/
                    }
                }
            }
            
            // INSERT LOG IMPORT
            $employee = new Employee((int)Context::getContext()->cookie->id_employee);
            Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."log_import_customers (link_file, employee, status, quantity, date_import)
                                        VALUES ('".$this->context->cookie->csv_selected."', '".$employee->firstname." ".$employee->lastname."', 'Successful', ".$number_to_import.", NOW())");
            
            $this->closeCsvFile($handle);
        } else {
            $this->errors[] = "No es posible importar mas de 50 registros. Por favor validar y reducir la cantidad de registros.";
        }
    }
    
    public function ordersImport()
    {
        $this->receiveTab();
        $handle = $this->openCsvFile();
        $default_language_id = (int)Configuration::get('PS_LANG_DEFAULT');
        $id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
        if (!Validate::isUnsignedId($id_lang)) {
            $id_lang = $default_language_id;
        }
        AdminImportController::setLocale();

        $shop_is_feature_active = Shop::isFeatureActive();
        $convert = Tools::getValue('convert');
        $force_ids = Tools::getValue('forceIDs');
        
        $number_to_import = 0;
        for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator); $current_line++) {
            $number_to_import++;
        }
        $this->closeCsvFile($handle);
        
        if ( $number_to_import <= 31 ) {
        $handle = $this->openCsvFile();
        // main loop, for each supply orders to import
        for ($current_line = 0; $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator); ++$current_line) {
            
            $name = '';
            $quantity_p = '';
            $price_unit = '';
            $price_total = '';
            $price_point = '';

            // if convert requested
            if ($convert) {
                $line = $this->utf8EncodeArray($line);
            }
            $info = AdminImportController::getMaskedRow($line);

            // sets default values if needed
            AdminImportController::setDefaultValues($info);
            
            // if an id is set, instanciates a supply order with this id if possible
            
            if (array_key_exists('id_customer', $info) && (int)$info['id_customer'] && Customer::customerIdExistsStatic((int)$info['id_customer'])) {
                
                $query_secure = 'SELECT secure_key FROM '._DB_PREFIX_.'customer WHERE id_customer='.(int)$info['id_customer'];
                $row = Db::getInstance()->getRow($query_secure);
                $key = $row['secure_key'];
                
                $product_fluz = array();
                $products_normal = array();
                $products = explode(",", $info['id_products']);
                
                foreach ($products as $p){
                    $product_quantity = explode(":", $p);
                    $id_product = $product_quantity[0];
                    $quantity = $product_quantity[1];
                    
                    $query_stock = 'SELECT quantity FROM '._DB_PREFIX_.'stock_available WHERE id_product = '.$id_product;
                    $row_stock = Db::getInstance()->getRow($query_stock);
                    $stock_available = $row_stock['quantity']; 
                    
                    $query_m = 'SELECT reference, id_product FROM ps_product WHERE id_product = '.$id_product;
                    $m_fluz = Db::getInstance()->executeS($query_m);
                    $reference = $m_fluz[0]['reference'];
                    $fluz = substr($reference, 0,5);
                    
                    if($quantity>$stock_available){
                            $this->errors[] = Tools::displayError('Producto ID: '.$id_product.' sin Codigos Disponibles');
                            break;
                    }
                    if($fluz == 'MFLUZ' && $info['payment']==2){
                            $this->errors[] = Tools::displayError('Carga de Bonos Fluz solo en Pedido Gratituito');
                            break;
                    }
                    
                }
                
                if(strlen($this->errors[0])<=0){
                    foreach ($products as $p){
                            $product_quantity = explode(":", $p);
                            $id_product = $product_quantity[0];
                            $quantity = $product_quantity[1];

                            $query_m = 'SELECT reference, id_product FROM ps_product WHERE id_product = '.$id_product;
                            $m_fluz = Db::getInstance()->executeS($query_m);
                            $reference = $m_fluz[0]['reference'];
                            $fluz = substr($reference, 0,5);

                            if($fluz == 'MFLUZ'){
                                $query_fluz = 'SELECT * FROM ps_product WHERE id_product = '.$id_product;
                                $p_fluz = Db::getInstance()->executeS($query_fluz);
                                $p_fluz[0]['quantity']= $quantity;

                                array_push($product_fluz, $p_fluz);
                            }
                            else{
                                $query_n = 'SELECT * FROM ps_product WHERE id_product = '.$id_product;
                                $p_normal = Db::getInstance()->executeS($query_n);
                                $p_normal[0]['quantity']= $quantity;

                                array_push($products_normal, $p_normal);
                            }
                        }
                
                $array_products_fluz = array_map('current',$product_fluz);
                $array_products_normal = array_map('current',$products_normal);
                
                if(!empty($array_products_fluz)){
                    $cart_fluz = new Cart();
                    $cart_fluz->id_customer = (int)$info['id_customer'];
                    $cart_fluz->id_shop_group = 1;
                    $cart_fluz->id_currency = Currency::getDefaultCurrency()->id;
                    $cart_fluz->id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
                    $cart_fluz->secure_key = $key;

                    $cart_fluz->add();
                    
                    foreach ($array_products_fluz as $p){
                        $cart_fluz->updateQty($p['quantity'],$p['id_product']);
                    }
                    
                    $payment = 'Pedido gratuito';
                    $module = 'free_order';
                    $state = 2;
                    $paid = 0;
                    $payment_module = Module::getInstanceByName('bankwire');
                    $payment_module->validateOrder($cart_fluz->id, $state, $paid, $payment);
                    
                    // INSERT LOG IMPORT ORDERS
                    $employee = new Employee((int)Context::getContext()->cookie->id_employee);
                    Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."log_import_orders (id_cart, link_file, employee, status, payment, quantity, date_import)
                                        VALUES (".$cart_fluz->id.",'".$this->context->cookie->csv_selected."', '".$employee->firstname." ".$employee->lastname."', 'Successful','".$payment."', ".$number_to_import.", NOW())");
                    
                }
                if(!empty($array_products_normal)){
                    $cart_normal = new Cart();
                    $cart_normal->id_customer = (int)$info['id_customer'];
                    $cart_normal->id_shop_group = 1;
                    $cart_normal->id_currency = Currency::getDefaultCurrency()->id;
                    $cart_normal->id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
                    $cart_normal->secure_key = $key;

                    $cart_normal->add();
                    
                    foreach ($array_products_normal as &$p){
                        $cart_normal->updateQty($p['quantity'],$p['id_product']);
                    }
                    
                    if($info['payment']==1){
                        $payment = 'Pedido gratuito';
                        $module = 'free_order';
                        $state = 2;
                        $paid = 0;
                        $payment_module = Module::getInstanceByName('bankwire');
                        $payment_module->validateOrder($cart_normal->id, $state, $paid, $payment);
                    }
                    else{
                        
                        $sponsorships = RewardsSponsorshipModel::getSponsorshipAscendants((int)$info['id_customer']);
                        $sponsorships2=array_slice($sponsorships, 1, 15);
                        
                        foreach ($cart_normal->getProducts() as &$product_cart){
                            $name .=  "<label>".$product_cart['name']."</label><br>";
                            $quantity_p .=  "<label>".$product_cart['cart_quantity']."</label><br>";
                            $price_unit .=  "<label>".$product_cart['price']."</label><br>";
                            $price_total .=  "<label>".$product_cart['total']."</label><br>";
                            
                            $query_value = 'SELECT (rp.`value`/100) as value FROM '._DB_PREFIX_.'rewards_product rp WHERE id_product = '.$product_cart['id_product'];
                            $row_v = Db::getInstance()->getRow($query_value);
                            $value_porc = $row_v['value'];
                            
                            $reward = round(RewardsModel::getRewardReadyForDisplay($product_cart['total'], $this->context->currency->id)/(count($sponsorships2)+1));
                            $r_point = floor($reward*$value_porc);
                            $price_point .=  "<label>".$r_point."</label><br>";
                        }
                        
                        $customer = new Customer($info['id_customer']);
                        $mailVars = array(
                            '{order_link}' => Context::getContext()->link->getPageLink('order', false, (int)$cart_normal->id_lang, 'step=3&recover_cart='.(int)$cart_normal->id.'&token_cart='.md5(_COOKIE_KEY_.'recover_cart_'.(int)$cart_normal->id)),
                            '{username}' => $customer->username,
                            '{quantity}' => $quantity_p,
                            '{name_product}' => $name,
                            '{points}' => $price_point,
                            '{price_unit}' => $price_unit,
                            '{price_total}' => $price_total,
                            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                            '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                        );
                        $template = 'backoffice_order';
                        $prefix_template = '16-backoffice_order';
                        
                        $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
                        $row_subject = Db::getInstance()->getRow($query_subject);
                        $message_subject = $row_subject['subject_mail'];
                        
                        /*Mail::Send((int)$cart_normal->id_lang, 'backoffice_order', Mail::l('Pedido Recomendado', (int)$cart_normal->id_lang), $mailVars, $customer->email,
                        $customer->firstname.' '.$customer->lastname, null, null, null, null, _PS_MAIL_DIR_, true, $cart_normal->id_shop);
                        */
                        $allinone_rewards = new allinone_rewards();
                        $allinone_rewards->sendMail((int)$cart_normal->id_lang, $template, $allinone_rewards->getL($message_subject), $mailVars, $customer->email, $customer->firstname.' '.$customer->lastname);
        
                    }
                        // INSERT LOG IMPORT ORDERS
                        $employee = new Employee((int)Context::getContext()->cookie->id_employee);
                        Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."log_import_orders (id_cart, link_file, employee, status, payment, quantity, date_import)
                                        VALUES (".$cart_normal->id.",'".$this->context->cookie->csv_selected."', '".$employee->firstname." ".$employee->lastname."', 'Successful','Tarjeta_credito', ".$number_to_import.", NOW())");
                }
                }
            }
        }
            
            // closes
            $this->closeCsvFile($handle);
        }
        else {
            $this->errors[] = "No es posible importar mas de 30 registros. Por favor validar y reducir la cantidad de registros.";
        }
        
    }

    protected function truncateTables($case)
    {
        /*switch ((int)$case) {
            case $this->entities[$this->l('Categories')]:
                Db::getInstance()->execute('
					DELETE FROM `'._DB_PREFIX_.'category`
					WHERE id_category NOT IN ('.(int)Configuration::get('PS_HOME_CATEGORY').
                    ', '.(int)Configuration::get('PS_ROOT_CATEGORY').')');
                Db::getInstance()->execute('
					DELETE FROM `'._DB_PREFIX_.'category_lang`
					WHERE id_category NOT IN ('.(int)Configuration::get('PS_HOME_CATEGORY').
                    ', '.(int)Configuration::get('PS_ROOT_CATEGORY').')');
                Db::getInstance()->execute('
					DELETE FROM `'._DB_PREFIX_.'category_shop`
					WHERE `id_category` NOT IN ('.(int)Configuration::get('PS_HOME_CATEGORY').
                    ', '.(int)Configuration::get('PS_ROOT_CATEGORY').')');
                Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'category` AUTO_INCREMENT = 3');
                foreach (scandir(_PS_CAT_IMG_DIR_) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_CAT_IMG_DIR_.$d);
                    }
                }
                break;
            case $this->entities[$this->l('Products')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'feature_product`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'category_product`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_tag`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'image`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'image_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'image_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'specific_price`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'specific_price_priority`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_carrier`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'cart_product`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'compare_product`');
                if (count(Db::getInstance()->executeS('SHOW TABLES LIKE \''._DB_PREFIX_.'favorite_product\' '))) { //check if table exist
                    Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'favorite_product`');
                }
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attachment`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_country_tax`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_download`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_group_reduction_cache`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_sale`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_supplier`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'scene_products`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'warehouse_product_location`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'stock`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'stock_available`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'stock_mvt`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customization`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customization_field`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supply_order_detail`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_impact`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_combination`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_image`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'pack`');
                Image::deleteAllImages(_PS_PROD_IMG_DIR_);
                if (!file_exists(_PS_PROD_IMG_DIR_)) {
                    mkdir(_PS_PROD_IMG_DIR_);
                }
                break;
            case $this->entities[$this->l('Combinations')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_impact`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_group`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_group_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_group_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_combination`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_image`');
                Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'stock_available` WHERE id_product_attribute != 0');
                break;
            case $this->entities[$this->l('Customers')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customer`');
                break;
            case $this->entities[$this->l('Addresses')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'address`');
                break;
            case $this->entities[$this->l('Manufacturers')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'manufacturer`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'manufacturer_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'manufacturer_shop`');
                foreach (scandir(_PS_MANU_IMG_DIR_) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_MANU_IMG_DIR_.$d);
                    }
                }
                break;
            case $this->entities[$this->l('Suppliers')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supplier`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supplier_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supplier_shop`');
                foreach (scandir(_PS_SUPP_IMG_DIR_) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_SUPP_IMG_DIR_.$d);
                    }
                }
                break;
            case $this->entities[$this->l('Alias')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'alias`');
                break;
        }
        Image::clearTmpDir();
        */
        return true;
    }
}
