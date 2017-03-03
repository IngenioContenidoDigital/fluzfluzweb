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

class Product extends ProductCore
{
   
    public $specificPrice = 0;
    
    public $save_dolar=0;
    
    public $price_shop=0;
  
    public $product_parent;
    
    public $single_use = false;
    
    public $expiration = '0000-00-00';
    
    /** @var string ENUM('peso', 'dolar') front office type currency */
    public $type_currency;
    
    public $codetype = 0;
    
    public static $definition = array(
        'table' => 'product',
        'primary' => 'id_product',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            /* Classic fields */
            'id_shop_default' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_manufacturer' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_supplier' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'reference' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 32),
            'supplier_reference' =>        array('type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 32),
            'location' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 64),
            'width' =>                        array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'height' =>                    array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'depth' =>                        array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'weight' =>                    array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'quantity_discount' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'codetype' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isInt'),
            'ean13' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isEan13', 'size' => 13),
            'upc' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isUpc', 'size' => 12),
            'cache_is_pack' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'cache_has_attachments' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'is_virtual' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'product_parent' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'expiration'=>                  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            /* Shop fields */
            'id_category_default' =>        array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId'),
            'id_tax_rules_group' =>        array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId'),
            'on_sale' =>                    array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
            'online_only' =>                array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
            'single_use' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'ecotax' =>                    array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice'),
            'minimal_quantity' =>            array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedInt'),
            'price' =>                        array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'required' => true),
            'price_shop' =>                 array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'required' => true),
            'wholesale_price' =>            array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice'),
            'unity' =>                        array('type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isString'),
            'unit_price_ratio' =>            array('type' => self::TYPE_FLOAT, 'shop' => true),
            'additional_shipping_cost' =>    array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice'),
            'customizable' =>                array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedInt'),
            'text_fields' =>                array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedInt'),
            'uploadable_files' =>            array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedInt'),
            'active' =>                    array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
            'redirect_type' =>                array('type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isString'),
            'id_product_redirected' =>        array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId'),
            'available_for_order' =>        array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
            'available_date' =>            array('type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDateFormat'),
            'condition' =>                    array('type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isGenericName', 'values' => array('new', 'used', 'refurbished'), 'default' => 'new'),
            'show_price' =>                array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
            'indexed' =>                    array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
            'visibility' =>                array('type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isProductVisibility', 'values' => array('both', 'catalog', 'search', 'none'), 'default' => 'both'),
            'type_currency' =>             array('type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 20),
            'save_dolar' =>             array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'cache_default_attribute' =>    array('type' => self::TYPE_INT, 'shop' => true),
            'advanced_stock_management' =>    array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
            'date_add' =>                    array('type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDate'),
            'date_upd' =>                    array('type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDate'),
            'pack_stock_type' =>            array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedInt'),

            /* Lang fields */
            'meta_description' =>            array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_keywords' =>                array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_title' =>                array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128),
            'link_rewrite' =>    array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isLinkRewrite',
                'required' => true,
                'size' => 128,
                'ws_modifier' => array(
                    'http_method' => WebserviceRequest::HTTP_POST,
                    'modifier' => 'modifierWsLinkRewrite'
                )
            ),
            'name' =>                        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 128),
            'description' =>                array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'description_short' =>            array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'available_now' =>                array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'available_later' =>            array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'IsGenericName', 'size' => 255),
        ),
        'associations' => array(
            'manufacturer' =>                array('type' => self::HAS_ONE),
            'supplier' =>                    array('type' => self::HAS_ONE),
            'default_category' =>            array('type' => self::HAS_ONE, 'field' => 'id_category_default', 'object' => 'Category'),
            'tax_rules_group' =>            array('type' => self::HAS_ONE),
            'categories' =>                    array('type' => self::HAS_MANY, 'field' => 'id_category', 'object' => 'Category', 'association' => 'category_product'),
            'stock_availables' =>            array('type' => self::HAS_MANY, 'field' => 'id_stock_available', 'object' => 'StockAvailable', 'association' => 'stock_availables'),
        ),
    );
    
    public function update($null_values = false)
    {
        $return = parent::update($null_values);
        $this->setGroupReduction();

        // Sync stock Reference, EAN13 and UPC
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && StockAvailable::dependsOnStock($this->id, Context::getContext()->shop->id)) {
            Db::getInstance()->update('stock', array(
                'reference' => pSQL($this->reference),
                'ean13'     => pSQL($this->ean13),
                'codetype' => pSQL($this->codetype),
                'upc'        => pSQL($this->upc)
            ), 'id_product = '.(int)$this->id.' AND id_product_attribute = 0');
        }

        Hook::exec('actionProductSave', array('id_product' => (int)$this->id, 'product' => $this));
        Hook::exec('actionProductUpdate', array('id_product' => (int)$this->id, 'product' => $this));
        if ($this->getType() == Product::PTYPE_VIRTUAL && $this->active && !Configuration::get('PS_VIRTUAL_PROD_FEATURE_ACTIVE')) {
            Configuration::updateGlobalValue('PS_VIRTUAL_PROD_FEATURE_ACTIVE', '1');
        }

        return $return;
    }
    
    public static function getProductProperties($id_lang, $row, Context $context = null)
    {
        if (!$row['id_product']) {
            return false;
        }

        if ($context == null) {
            $context = Context::getContext();
        }

        $id_product_attribute = $row['id_product_attribute'] = (!empty($row['id_product_attribute']) ? (int)$row['id_product_attribute'] : null);

        // Product::getDefaultAttribute is only called if id_product_attribute is missing from the SQL query at the origin of it:
        // consider adding it in order to avoid unnecessary queries
        $row['allow_oosp'] = Product::isAvailableWhenOutOfStock($row['out_of_stock']);
        if (Combination::isFeatureActive() && $id_product_attribute === null
            && ((isset($row['cache_default_attribute']) && ($ipa_default = $row['cache_default_attribute']) !== null)
                || ($ipa_default = Product::getDefaultAttribute($row['id_product'], !$row['allow_oosp'])))) {
            $id_product_attribute = $row['id_product_attribute'] = $ipa_default;
        }
        if (!Combination::isFeatureActive() || !isset($row['id_product_attribute'])) {
            $id_product_attribute = $row['id_product_attribute'] = 0;
        }

        // Tax
        $usetax = Tax::excludeTaxeOption();

        $cache_key = $row['id_product'].'-'.$id_product_attribute.'-'.$id_lang.'-'.(int)$usetax;
        if (isset($row['id_product_pack'])) {
            $cache_key .= '-pack'.$row['id_product_pack'];
        }

        if (isset(self::$producPropertiesCache[$cache_key])) {
            return array_merge($row, self::$producPropertiesCache[$cache_key]);
        }

        // Datas
        $row['category'] = Category::getLinkRewrite((int)$row['id_category_default'], (int)$id_lang);
        $row['link'] = $context->link->getProductLink((int)$row['id_product'], $row['link_rewrite'], $row['category'], $row['ean13']);

        $row['attribute_price'] = 0;
        if ($id_product_attribute) {
            $row['attribute_price'] = (float)Product::getProductAttributePrice($id_product_attribute);
        }

        $row['price_tax_exc'] = Product::getPriceStatic(
            (int)$row['id_product'],
            false,
            $id_product_attribute,
            (self::$_taxCalculationMethod == PS_TAX_EXC ? 2 : 6)
        );

        if (self::$_taxCalculationMethod == PS_TAX_EXC) {
            $row['price_tax_exc'] = Tools::ps_round($row['price_tax_exc'], 2);
            $row['price'] = Product::getPriceStatic(
                (int)$row['id_product'],
                true,
                $id_product_attribute,
                6
            );
            $row['price_without_reduction'] = Product::getPriceStatic(
                (int)$row['id_product'],
                false,
                $id_product_attribute,
                2,
                null,
                false,
                false
            );
        } else {
            $row['price'] = Tools::ps_round(
                Product::getPriceStatic(
                    (int)$row['id_product'],
                    true,
                    $id_product_attribute,
                    6
                ),
                (int)Configuration::get('PS_PRICE_DISPLAY_PRECISION')
            );
            $row['price_without_reduction'] = Product::getPriceStatic(
                (int)$row['id_product'],
                true,
                $id_product_attribute,
                6,
                null,
                false,
                false
            );
        }

        $row['reduction'] = Product::getPriceStatic(
            (int)$row['id_product'],
            (bool)$usetax,
            $id_product_attribute,
            6,
            null,
            true,
            true,
            1,
            true,
            null,
            null,
            null,
            $specific_prices
        );

        $row['specific_prices'] = $specific_prices;

        $row['quantity'] = Product::getQuantity(
            (int)$row['id_product'],
            0,
            isset($row['cache_is_pack']) ? $row['cache_is_pack'] : null
        );

        $row['quantity_all_versions'] = $row['quantity'];

        if ($row['id_product_attribute']) {
            $row['quantity'] = Product::getQuantity(
                (int)$row['id_product'],
                $id_product_attribute,
                isset($row['cache_is_pack']) ? $row['cache_is_pack'] : null
            );
        }
        
        $row['id_image_parent'] = $row['id_image'];
        $row['id_image'] = Product::defineProductImage($row, $id_lang);
        $row['features'] = Product::getFrontFeaturesStatic((int)$id_lang, $row['id_product']);

        $row['attachments'] = array();
        if (!isset($row['cache_has_attachments']) || $row['cache_has_attachments']) {
            $row['attachments'] = Product::getAttachmentsStatic((int)$id_lang, $row['id_product']);
        }

        $row['virtual'] = ((!isset($row['is_virtual']) || $row['is_virtual']) ? 1 : 0);

        // Pack management
        $row['pack'] = (!isset($row['cache_is_pack']) ? Pack::isPack($row['id_product']) : (int)$row['cache_is_pack']);
        $row['packItems'] = $row['pack'] ? Pack::getItemTable($row['id_product'], $id_lang) : array();
        $row['nopackprice'] = $row['pack'] ? Pack::noPackPrice($row['id_product']) : 0;
        if ($row['pack'] && !Pack::isInStock($row['id_product'])) {
            $row['quantity'] = 0;
        }

        $row['customization_required'] = false;
        if (isset($row['customizable']) && $row['customizable'] && Customization::isFeatureActive()) {
            if (count(Product::getRequiredCustomizableFieldsStatic((int)$row['id_product']))) {
                $row['customization_required'] = true;
            }
        }

        $row = Product::getTaxesInformations($row, $context);
        self::$producPropertiesCache[$cache_key] = $row;
        return self::$producPropertiesCache[$cache_key];
    }

}

?>