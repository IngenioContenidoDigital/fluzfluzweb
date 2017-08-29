<?php
/*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @property Product $object
 */
class AdminProductsController extends AdminProductsControllerCore
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'product';
        $this->className = 'Product';
        $this->lang = true;
        $this->explicitSelect = true;
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
        if (!Tools::getValue('id_product')) {
            $this->multishop_context_group = false;
        }
        
        AdminController::__construct();
        $this->imageType = 'jpg';
        $this->_defaultOrderBy = 'position';
        $this->max_file_size = (int)(Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE') * 1000000);
        $this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        $this->allow_export = true;
        // @since 1.5 : translations for tabs
        $this->available_tabs_lang = array(
            'Informations' => $this->l('Information'),
            'Pack' => $this->l('Pack'),
            //'VirtualProduct' => $this->l('Virtual Product'),
            'Prices' => $this->l('Prices'),
            'Seo' => $this->l('SEO'),
            'Images' => $this->l('Images'),
            'Associations' => $this->l('Associations'),
            'Shipping' => $this->l('Shipping'),
            'Combinations' => $this->l('Combinations'),
            'Features' => $this->l('Features'),
            //'Customization' => $this->l('Customization'),
            //'Attachments' => $this->l('Attachments'),
            'Quantities' => $this->l('Quantities'),
            'Suppliers' => $this->l('Suppliers'),
            'Warehouses' => $this->l('Warehouses'),
        );
        $this->available_tabs = array('Quantities' => 6, 'Warehouses' => 14);
        if ($this->context->shop->getContext() != Shop::CONTEXT_GROUP) {
            $this->available_tabs = array_merge($this->available_tabs, array(
                'Informations' => 0,
                'Pack' => 7,
                //'VirtualProduct' => 8,
                'Prices' => 1,
                'Seo' => 2,
                'Associations' => 3,
                'Images' => 9,
                'Shipping' => 4,
                'Combinations' => 5,
                'Features' => 10,
                //'Customization' => 11,
                //'Attachments' => 12,
                'Suppliers' => 13,
            ));
        }
        // Sort the tabs that need to be preloaded by their priority number
        asort($this->available_tabs, SORT_NUMERIC);
        /* Adding tab if modules are hooked */
        $modules_list = Hook::getHookModuleExecList('displayAdminProductsExtra');
        if (is_array($modules_list) && count($modules_list) > 0) {
            foreach ($modules_list as $m) {
                $this->available_tabs['Module'.ucfirst($m['module'])] = 23;
                $this->available_tabs_lang['Module'.ucfirst($m['module'])] = Module::getModuleName($m['module']);
            }
        }
        
        if (Tools::getValue('reset_filter_category')) {
            $this->context->cookie->id_category_products_filter = false;
        }
        if (Shop::isFeatureActive() && $this->context->cookie->id_category_products_filter) {
            $category = new Category((int)$this->context->cookie->id_category_products_filter);
            if (!$category->inShop()) {
                $this->context->cookie->id_category_products_filter = false;
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminProducts'));
            }
        }
        /* Join categories table */
        if ($id_category = (int)Tools::getValue('productFilter_cl!name')) {
            $this->_category = new Category((int)$id_category);
            $_POST['productFilter_cl!name'] = $this->_category->name[$this->context->language->id];
        } else {
            if ($id_category = (int)Tools::getValue('id_category')) {
                $this->id_current_category = $id_category;
                $this->context->cookie->id_category_products_filter = $id_category;
            } elseif ($id_category = $this->context->cookie->id_category_products_filter) {
                $this->id_current_category = $id_category;
            }
            if ($this->id_current_category) {
                $this->_category = new Category((int)$this->id_current_category);
            } else {
                $this->_category = new Category();
            }
        }
        $join_category = false;
        if (Validate::isLoadedObject($this->_category) && empty($this->_filter)) {
            $join_category = true;
        }
        $this->_join .= '
		LEFT JOIN `'._DB_PREFIX_.'stock_available` sav ON (sav.`id_product` = a.`id_product` AND sav.`id_product_attribute` = 0
		'.StockAvailable::addSqlShopRestriction(null, null, 'sav').') ';
        $alias = 'sa';
        $alias_image = 'image_shop';
        $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP? (int)$this->context->shop->id : 'a.id_shop_default';
        $this->_join .= ' JOIN `'._DB_PREFIX_.'product_shop` sa ON (a.`id_product` = sa.`id_product` AND sa.id_shop = '.$id_shop.')
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON ('.$alias.'.`id_category_default` = cl.`id_category` AND b.`id_lang` = cl.`id_lang` AND cl.id_shop = '.$id_shop.')
				LEFT JOIN `'._DB_PREFIX_.'shop` shop ON (shop.id_shop = '.$id_shop.')
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.`id_product` = a.`id_product` AND image_shop.`cover` = 1 AND image_shop.id_shop = '.$id_shop.')
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_image` = image_shop.`id_image`)
				LEFT JOIN `'._DB_PREFIX_.'product_download` pd ON (pd.`id_product` = a.`id_product`)';
        $this->_select .= 'shop.`name` AS `shopname`, a.`id_shop_default`, ';
        $this->_select .= $alias_image.'.`id_image` AS `id_image`, cl.`name` AS `name_category`, '.$alias.'.`price`, 0 AS `price_final`, a.`is_virtual`, pd.`nb_downloadable`, sav.`quantity` AS `sav_quantity`, '.$alias.'.`active`, IF(sav.`quantity`<=0, 1, 0) AS `badge_danger`';
        if ($join_category) {
            $this->_join .= ' INNER JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = a.`id_product` AND cp.`id_category` = '.(int)$this->_category->id.') ';
            $this->_select .= ' , cp.`position`, ';
        }
        $this->_use_found_rows = false;
        $this->_group = '';
        $this->fields_list = array();
        $this->fields_list['id_product'] = array(
            'title' => $this->l('ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'int'
        );
        $this->fields_list['image'] = array(
            'title' => $this->l('Image'),
            'align' => 'center',
            'image' => 'p',
            'orderby' => false,
            'filter' => false,
            'search' => false
        );
        $this->fields_list['name'] = array(
            'title' => $this->l('Name'),
            'filter_key' => 'b!name'
        );
        $this->fields_list['reference'] = array(
            'title' => $this->l('Reference'),
            'align' => 'left',
        );
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->fields_list['shopname'] = array(
                'title' => $this->l('Default shop'),
                'filter_key' => 'shop!name',
            );
        } else {
            $this->fields_list['name_category'] = array(
                'title' => $this->l('Category'),
                'filter_key' => 'cl!name',
            );
        }
        $this->fields_list['price'] = array(
            'title' => $this->l('Base price'),
            'type' => 'price',
            'align' => 'text-right',
            'filter_key' => 'a!price'
        );
        $this->fields_list['price_final'] = array(
            'title' => $this->l('Final price'),
            'type' => 'price',
            'align' => 'text-right',
            'havingFilter' => true,
            'orderby' => false,
            'search' => false
        );
        if (Configuration::get('PS_STOCK_MANAGEMENT')) {
            $this->fields_list['sav_quantity'] = array(
                'title' => $this->l('Quantity'),
                'type' => 'int',
                'align' => 'text-right',
                'filter_key' => 'sav!quantity',
                'orderby' => true,
                'badge_danger' => true,
                //'hint' => $this->l('This is the quantity available in the current shop/group.'),
            );
        }
        
        $this->fields_list['product_parent'] = array(
            'title' => $this->l('Producto Padre'),
            'active' => 'status',
            'align' => 'text-center',
            'type' => 'bool',
            'class' => 'fixed-width-sm',
            'orderby' => false
        );
        $this->fields_list['active'] = array(
            'title' => $this->l('Status'),
            'active' => 'status',
            'filter_key' => $alias.'!active',
            'align' => 'text-center',
            'type' => 'bool',
            'class' => 'fixed-width-sm',
            'orderby' => false
        );
        if ($join_category && (int)$this->id_current_category) {
            $this->fields_list['position'] = array(
                'title' => $this->l('Position'),
                'filter_key' => 'cp!position',
                'align' => 'center',
                'position' => 'position'
            );
        }
    }
    
    public function initFormAttributes($product)
    {
        $data = $this->createTemplate($this->tpl_form);
        if (!Combination::isFeatureActive()) {
            $this->displayWarning($this->l('This feature has been disabled. ').
                ' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
        } elseif (Validate::isLoadedObject($product)) {
            if ($this->product_exists_in_shop)
        {
            // removed virtual product restriction
            $attribute_js = array();
            $attributes = Attribute::getAttributes($this->context->language->id, true);
            foreach ($attributes as $k => $attribute)
                $attribute_js[$attribute['id_attribute_group']][$attribute['id_attribute']] = $attribute['name'];
            $currency = $this->context->currency;
            $data->assign('attributeJs', $attribute_js);
            $data->assign('attributes_groups', AttributeGroup::getAttributesGroups($this->context->language->id));
            $data->assign('currency', $currency);
            $images = Image::getImages($this->context->language->id, $product->id);
            $data->assign('tax_exclude_option', Tax::excludeTaxeOption());
            $data->assign('ps_weight_unit', Configuration::get('PS_WEIGHT_UNIT'));
            $data->assign('ps_use_ecotax', Configuration::get('PS_USE_ECOTAX'));
            $data->assign('field_value_unity', $this->getFieldValue($product, 'unity'));
            $data->assign('reasons', $reasons = StockMvtReason::getStockMvtReasons($this->context->language->id));
            $data->assign('ps_stock_mvt_reason_default', $ps_stock_mvt_reason_default = Configuration::get('PS_STOCK_MVT_REASON_DEFAULT'));
            $data->assign('minimal_quantity', $this->getFieldValue($product, 'minimal_quantity') ? $this->getFieldValue($product, 'minimal_quantity') : 1);
            $data->assign('available_date', ($this->getFieldValue($product, 'available_date') != 0) ? stripslashes(htmlentities($this->getFieldValue($product, 'available_date'), $this->context->language->id)) : '0000-00-00');
            $i = 0;
            $type = ImageType::getByNameNType('%', 'products', 'height');
            if (isset($type['name']))
                $data->assign('imageType', $type['name']);
            else
                $data->assign('imageType', 'small_default');
            $data->assign('imageWidth', (isset($image_type['width']) ? (int)($image_type['width']) : 64) + 25);
            foreach ($images as $k => $image)
            {
                $images[$k]['obj'] = new Image($image['id_image']);
                ++$i;
            }
            $data->assign('images', $images);
            $data->assign($this->tpl_form_vars);
            $data->assign(array(
                'list' => $this->renderListAttributes($product, $currency),
                'product' => $product,
                'id_category' => $product->getDefaultCategory(),
                'token_generator' => Tools::getAdminTokenLite('AdminAttributeGenerator'),
                'combination_exists' => (Shop::isFeatureActive() && (Shop::getContextShopGroup()->share_stock) && count(AttributeGroup::getAttributesGroups($this->context->language->id)) > 0 && $product->hasAttributes())
            ));
        }
        else
            $this->displayWarning($this->l('You must save the product in this shop before adding combinations.'));    
        } else {
            $data->assign('product', $product);
            $this->displayWarning($this->l('You must save this product before adding combinations.'));
        }
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }
    
    public function processDuplicate()
    {
        if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {
            $id_product_old = $product->id;
            if (empty($product->price) && Shop::getContext() == Shop::CONTEXT_GROUP) {
                $shops = ShopGroup::getShopsFromGroup(Shop::getContextShopGroupID());
                foreach ($shops as $shop) {
                    if ($product->isAssociatedToShop($shop['id_shop'])) {
                        $product_price = new Product($id_product_old, false, null, $shop['id_shop']);
                        $product->price = $product_price->price;
                    }
                }
            }
            unset($product->id);
            unset($product->id_product);
            $product->indexed = 0;
            $product->active = 0;
            $product->product_parent=0;
            if ($product->add()
            && Category::duplicateProductCategories($id_product_old, $product->id)
            && Product::duplicateSuppliers($id_product_old, $product->id)
            && ($combination_images = Product::duplicateAttributes($id_product_old, $product->id)) !== false
            && GroupReduction::duplicateReduction($id_product_old, $product->id)
            && Product::duplicateAccessories($id_product_old, $product->id)
            && Product::duplicateFeatures($id_product_old, $product->id)
            && Product::duplicateSpecificPrices($id_product_old, $product->id)
            && Pack::duplicate($id_product_old, $product->id)
            && Product::duplicateCustomizationFields($id_product_old, $product->id)
            && Product::duplicateTags($id_product_old, $product->id)
            && Product::duplicateDownload($id_product_old, $product->id)) {
                if ($product->hasAttributes()) {
                    Product::updateDefaultAttribute($product->id);
                }
                if (!Tools::getValue('noimage') && !Image::duplicateProductImages($id_product_old, $product->id, $combination_images)) {
                    $this->errors[] = Tools::displayError('An error occurred while copying images.');
                } else {
                    Hook::exec('actionProductAdd', array('id_product' => (int)$product->id, 'product' => $product));
                    if (in_array($product->visibility, array('both', 'search')) && Configuration::get('PS_SEARCH_INDEXATION')) {
                        Search::indexation(false, $product->id);
                    }
                    $this->redirect_after = self::$currentIndex.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&conf=19&token='.$this->token;
                }
            } else {
                $this->errors[] = Tools::displayError('An error occurred while creating an object.');
            }
        }
    }
    
    public function postProcess()
    {
        if (!$this->redirect_after) {
            AdminController::postProcess();
        }
        if ($this->display == 'edit' || $this->display == 'add') {
            $this->addJqueryUI(array(
                'ui.core',
                'ui.widget'
            ));
            $this->addjQueryPlugin(array(
                'autocomplete',
                'tablednd',
                'thickbox',
                'ajaxfileupload',
                'date',
                'tagify',
                'select2',
                'validate'
            ));
            $this->addJS(array(
                _PS_JS_DIR_.'admin/products.js',
                _PS_JS_DIR_.'admin/attributes.js',
                _PS_JS_DIR_.'admin/price.js',
                _PS_JS_DIR_.'tiny_mce/tiny_mce.js',
                _PS_JS_DIR_.'admin/tinymce.inc.js',
                _PS_JS_DIR_.'admin/dnd.js',
                _PS_JS_DIR_.'jquery/ui/jquery.ui.progressbar.min.js',
                _PS_JS_DIR_.'vendor/spin.js',
                _PS_JS_DIR_.'vendor/ladda.js'
            ));
            $this->addJS(_PS_JS_DIR_.'jquery/plugins/select2/select2_locale_'.$this->context->language->iso_code.'.js');
            $this->addJS(_PS_JS_DIR_.'jquery/plugins/validate/localization/messages_'.$this->context->language->iso_code.'.js');
            $this->addCSS(array(
                _PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css'
            ));
        }
        
        if ( isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == "exportreport" ) {
            $sql = "SELECT
                        p.id_product id,
                        pl.name nombre_producto,
                        p.reference referencia,
                        p.product_parent,
                        p.price precio,
                        p.price_shop precio_tienda,
                        ps.product_supplier_price_te costo,
                        rp.value porcentaje_red,
                        m.name fabricante,
                        s.name proveedor,
                        s.id_select_terms termino_pago,
                        IF(p.active=1,'Activo','Inactivo') estado,
                        (SELECT COUNT(pc1.id_product)
                        FROM "._DB_PREFIX_."product_code pc1
                        WHERE pc1.id_product = p.id_product
                        AND pc1.id_order = 0) unidades_disponibles,
                        (SELECT COUNT(pc2.id_product)
                        FROM "._DB_PREFIX_."product_code pc2
                        WHERE pc2.id_product = p.id_product
                        AND pc2.id_order <> 0) unidades_vendidas
                    FROM "._DB_PREFIX_."product p
                    LEFT JOIN "._DB_PREFIX_."product_lang pl ON ( p.id_product = pl.id_product AND pl.id_lang = ".$this->context->language->id.")
                    LEFT JOIN "._DB_PREFIX_."manufacturer m ON ( p.id_manufacturer = m.id_manufacturer )
                    LEFT JOIN "._DB_PREFIX_."product_supplier ps ON ( p.id_product = ps.id_product )
                    LEFT JOIN "._DB_PREFIX_."supplier s ON ( ps.id_supplier = s.id_supplier )
                    LEFT JOIN "._DB_PREFIX_."rewards_product rp ON ( p.id_product = rp.id_product )
                    GROUP BY p.id_product";
            $products = Db::getInstance()->executeS($sql);
            
            $report = "<html>
                        <head>
                            <meta http-equiv=?Content-Type? content=?text/html; charset=utf-8? />
                        </head>
                            <body>
                                <table>
                                    <tr>
                                        <th>id</th>
                                        <th>nombre_producto</th> 
                                        <th>referencia</th>
                                        <th>precio</th>
                                        <th>precio_tienda</th>
                                        <th>costo</th>
                                        <th>precio_red</th>
                                        <th>porcentaje_red</th>
                                        <th>fabricante</th>
                                        <th>proveedor</th>
                                        <th>termino_pago</th>
                                        <th>categorias</th>
                                        <th>imagen</th>
                                        <th>Producto Padre</th>
                                        <th>estado</th>
                                        <th>unidades_disponibles</th>
                                        <th>unidades_vendidas</th>
                                    </tr>";
            foreach ( $products as $product ) {
                $productCategories = Product::getProductCategoriesFull($product['id']);                
                $categories = "";
                foreach ( $productCategories as $productCategory ) {
                    $categories .= $productCategory['name'].",";
                }
                $imageurl = "";
                $image = Image::getCover($product['id']);
                $productdetail = new Product($product['id'], false, Context::getContext()->language->id);
                $link = new Link;
                $imageurl = $link->getImageLink($productdetail->link_rewrite, $image['id_image'], 'home_default');
                $report .= "<tr>
                                <td>".$product['id']."</td>
                                <td>".$product['nombre_producto']."</td> 
                                <td>".$product['referencia']."</td>
                                <td>".$product['precio']."</td>
                                <td>".$product['precio_tienda']."</td>
                                <td>".$product['costo']."</td>
                                <td>".( $product['precio'] * $product['porcentaje_red'] / 100 )."</td>
                                <td>".$product['porcentaje_red']."</td>
                                <td>".$product['fabricante']."</td>
                                <td>".$product['proveedor']."</td>
                                <td>".$product['termino_pago']."</td>
                                <td>".substr($categories, 0, -1)."</td>
                                <td>".$imageurl."</td>
                                <td>".$product['product_parent']."</td>
                                <td>".$product['estado']."</td>
                                <td>".$product['unidades_disponibles']."</td>
                                <td>".$product['unidades_vendidas']."</td>
                            </tr>";
            }
            $report .= "         </table>
                            </body>
                        </html>";
            header("Content-Type: application/vnd.ms-excel");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("content-disposition: attachment;filename=report_products.xls");
            die($report);
        }
        
        if ( isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == "exportreportdescriptions" ) {
            $sql = "SELECT
                        p.id_product,
                        pl.name,
                        p.product_parent,
                        p.reference,
                        m.name merchant,
                        IF(p.active = 1,'Activo','Inactivo') status,
                        pl.description product_description,
                        pl.description_short product_description_short,
                        ml.description merchant_description,
                        ml.short_description merchant_description_short
                    FROM "._DB_PREFIX_."product p
                    LEFT JOIN "._DB_PREFIX_."product_lang pl ON ( p.id_product = pl.id_product AND pl.id_lang = ".$this->context->language->id." )
                    LEFT JOIN "._DB_PREFIX_."manufacturer m ON ( p.id_manufacturer = m.id_manufacturer )
                    LEFT JOIN "._DB_PREFIX_."manufacturer_lang ml ON ( p.id_manufacturer = ml.id_manufacturer AND ml.id_lang = ".$this->context->language->id." )";
            $products = Db::getInstance()->executeS($sql);
            
            $report = "<html>
                        <head>
                            <meta http-equiv=?Content-Type? content=?text/html; charset=utf-8? />
                        </head>
                            <body>
                                <table>
                                    <tr>
                                        <th>producto</th>
                                        <th>nombre</th> 
                                        <th>referencia</th>
                                        <th>fabricante</th>
                                        <th>producto padre</th>
                                        <th>estado</th>
                                        <th>descripcion larga producto</th>
                                        <th>descripcion corta producto</th>
                                        <th>descripcion larga fabricante</th>
                                        <th>descripcion corta fabricante</th>
                                    </tr>";
            foreach ( $products as $product ) {
                $report .= "<tr>
                                <td>".$product['id_product']."</td>
                                <td>".$product['name']."</td>
                                <td>".$product['reference']."</td>
                                <td>".$product['merchant']."</td>
                                <td>".$product['product_parent']."</td>
                                <td>".$product['status']."</td>
                                <td>".$product['product_description']."</td>
                                <td>".$product['product_description_short']."</td>
                                <td>".$product['merchant_description']."</td>
                                <td>".$product['merchant_description_short']."</td>
                            </tr>";
            }
            $report .= "         </table>
                            </body>
                        </html>";
            header("Content-Type: application/vnd.ms-excel");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("content-disposition: attachment;filename=report_products_descriptions.xls");
            die($report);
        }
        if ( Tools::isSubmit('deleteImgBannerProduct_0') ) {
            unlink(_PS_IMG_DIR_."p-banners/".Tools::getValue('id_product')."_0.jpg");
            $this->confirmations[] = "Banner borrado correctamente.";
        }
        
        if ( Tools::isSubmit('deleteImgBannerProduct_1') ) {
            unlink(_PS_IMG_DIR_."p-banners/".Tools::getValue('id_product')."_1.jpg");
            $this->confirmations[] = "Banner borrado correctamente.";
        }
        if ( Tools::isSubmit('submitImgBannerProduct') ) {
            if ( isset($_FILES['img_0']) && $_FILES['img_0']['size'] != 0 ) {
                $target_path = _PS_IMG_DIR_."p-banners/".Tools::getValue('id_product')."_0.jpg";
                if ( move_uploaded_file($_FILES['img_0']['tmp_name'], $target_path) ) {
                    // Sube las imágenes al AWS S3
                    $awsObj = new Aws();
                    if (!($awsObj->setObjectImage($target_path,basename( Tools::getValue('id_product')."_0.jpg"),'p-banners/'))) {
                         $this->errors[] = Tools::displayError('No fue posible cargar el banner.');
                    } else {
                        unlink(_PS_IMG_DIR_."p-banners/".Tools::getValue('id_product')."_0.jpg");
                    }
                }
            }
            if ( isset($_FILES['img_1']) && $_FILES['img_1']['size'] != 0 ) {
                $target_path = _PS_IMG_DIR_."p-banners/".Tools::getValue('id_product')."_1.jpg";
                if ( move_uploaded_file($_FILES['img_1']['tmp_name'], $target_path) ) {
                    // Sube las imágenes al AWS S3
                    $awsObj = new Aws();
                    if (!($awsObj->setObjectImage($target_path,basename( Tools::getValue('id_product')."_1.jpg"),'p-banners/'))) {
                         $this->errors[] = Tools::displayError('No fue posible cargar el banner.');
                    } else {
                        unlink(_PS_IMG_DIR_."p-banners/".Tools::getValue('id_product')."_1.jpg");
                    }
                }
            }
            $this->confirmations[] = "Banners cargados correctamente.";
        }
    }
    
    public function renderKpis()
    {
        $time = time();
        $kpis = array();
        /* The data generation is located in AdminStatsControllerCore */
        if (Configuration::get('PS_STOCK_MANAGEMENT')) {
            $helper = new HelperKpi();
            $helper->id = 'box-products-stock';
            $helper->icon = 'icon-archive';
            $helper->color = 'color3';
            $helper->title = $this->l('Out of stock items', null, null, false);
            if (ConfigurationKPI::get('PERCENT_PRODUCT_OUT_OF_STOCK') !== false) {
                $helper->value = ConfigurationKPI::get('PERCENT_PRODUCT_OUT_OF_STOCK');
            }
            $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=percent_product_out_of_stock';
            $helper->tooltip = $this->l('X% of your products for sale are out of stock.', null, null, false);
            $helper->refresh = (bool)(ConfigurationKPI::get('PERCENT_PRODUCT_OUT_OF_STOCK_EXPIRE') < $time);
            $helper->href = Context::getContext()->link->getAdminLink('AdminProducts').'&productFilter_sav!quantity=0&productFilter_active=1&submitFilterproduct=1';
            $kpis[] = $helper->generate();
        }
        /*$helper = new HelperKpi();
        $helper->id = 'box-avg-gross-margin';
        $helper->icon = 'icon-tags';
        $helper->color = 'color2';
        $helper->title = $this->l('Average Gross Margin %', null, null, false);
        if (ConfigurationKPI::get('PRODUCT_AVG_GROSS_MARGIN') !== false) {
            $helper->value = ConfigurationKPI::get('PRODUCT_AVG_GROSS_MARGIN');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=product_avg_gross_margin';
        $helper->tooltip = $this->l('Gross margin expressed in percentage assesses how cost-effectively you sell your goods. Out of $100, you will retain $X to cover profit and expenses.', null, null, false);
        $helper->refresh = (bool)(ConfigurationKPI::get('PRODUCT_AVG_GROSS_MARGIN_EXPIRE') < $time);
        $kpis[] = $helper->generate();
        $helper = new HelperKpi();
        $helper->id = 'box-8020-sales-catalog';
        $helper->icon = 'icon-beaker';
        $helper->color = 'color3';
        $helper->title = $this->l('Purchased references', null, null, false);
        $helper->subtitle = $this->l('30 days', null, null, false);
        if (ConfigurationKPI::get('8020_SALES_CATALOG') !== false) {
            $helper->value = ConfigurationKPI::get('8020_SALES_CATALOG');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=8020_sales_catalog';
        $helper->tooltip = $this->l('X% of your references have been purchased for the past 30 days', null, null, false);
        $helper->refresh = (bool)(ConfigurationKPI::get('8020_SALES_CATALOG_EXPIRE') < $time);
        if (Module::isInstalled('statsbestproducts')) {
            $helper->href = Context::getContext()->link->getAdminLink('AdminStats').'&module=statsbestproducts&datepickerFrom='.date('Y-m-d', strtotime('-30 days')).'&datepickerTo='.date('Y-m-d');
        }
        $kpis[] = $helper->generate();*/
        $helper = new HelperKpi();
        $helper->id = 'box-disabled-products';
        $helper->icon = 'icon-off';
        $helper->color = 'color4';
        $helper->href = $this->context->link->getAdminLink('AdminProducts');
        $helper->title = $this->l('Disabled Products', null, null, false);
        if (ConfigurationKPI::get('DISABLED_PRODUCTS') !== false) {
            $helper->value = ConfigurationKPI::get('DISABLED_PRODUCTS');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=disabled_products';
        $helper->refresh = (bool)(ConfigurationKPI::get('DISABLED_PRODUCTS_EXPIRE') < $time);
        $helper->tooltip = $this->l('X% of your products are disabled and not visible to your customers', null, null, false);
        $helper->href = Context::getContext()->link->getAdminLink('AdminProducts').'&productFilter_active=0&submitFilterproduct=1';
        $kpis[] = $helper->generate();
        
        $helper = new HelperKpi();
        $helper->id = 'box-report_products';
        $helper->icon = 'icon-download';
        $helper->color = 'color1';
        $helper->title = $this->l('Reporte Productos', null, null, false);
        $helper->subtitle = $this->l('Descargar', null, null, false);
        $helper->href = $this->context->link->getAdminLink('AdminProducts').'&action=exportreport';
        $kpis[] = $helper->generate();
        
        $helper = new HelperKpi();
        $helper->id = 'box-report_products_descriptions';
        $helper->icon = 'icon-download';
        $helper->color = 'color2';
        $helper->title = $this->l('Reporte Descripciones Productos', null, null, false);
        $helper->subtitle = $this->l('Descargar', null, null, false);
        $helper->href = $this->context->link->getAdminLink('AdminProducts').'&action=exportreportdescriptions';
        $kpis[] = $helper->generate();
        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;
        return $helper->generate();
    }
    
    public function renderForm()
    {
        // This nice code (irony) is here to store the product name, because the row after will erase product name in multishop context
        $this->product_name = $this->object->name[$this->context->language->id];
        if (!method_exists($this, 'initForm'.$this->tab_display)) {
            return;
        }
        $product = $this->object;
        // Product for multishop
        $this->context->smarty->assign('bullet_common_field', '');
        if (Shop::isFeatureActive() && $this->display == 'edit') {
            if (Shop::getContext() != Shop::CONTEXT_SHOP) {
                $this->context->smarty->assign(array(
                    'display_multishop_checkboxes' => true,
                    'multishop_check' => Tools::getValue('multishop_check'),
                ));
            }
            if (Shop::getContext() != Shop::CONTEXT_ALL) {
                $this->context->smarty->assign('bullet_common_field', '<i class="icon-circle text-orange"></i>');
                $this->context->smarty->assign('display_common_field', true);
            }
        }
        $this->tpl_form_vars['tabs_preloaded'] = $this->available_tabs;
        $this->tpl_form_vars['product_type'] = (int)Tools::getValue('type_product', $product->getType());
        $this->tpl_form_vars['product_parent'] = $product->product_parent;
        $this->tpl_form_vars['single_use'] = $product->single_use;
        
        $this->getLanguages();
        $this->tpl_form_vars['id_lang_default'] = Configuration::get('PS_LANG_DEFAULT');
        $this->tpl_form_vars['currentIndex'] = self::$currentIndex;
        $this->tpl_form_vars['display_multishop_checkboxes'] = (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP && $this->display == 'edit');
        $this->fields_form = array('');
        $this->tpl_form_vars['token'] = $this->token;
        $this->tpl_form_vars['combinationImagesJs'] = $this->getCombinationImagesJs();
        $this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
        $this->tpl_form_vars['post_data'] = Tools::jsonEncode($_POST);
        $this->tpl_form_vars['save_error'] = !empty($this->errors);
        $this->tpl_form_vars['mod_evasive'] = Tools::apacheModExists('evasive');
        $this->tpl_form_vars['mod_security'] = Tools::apacheModExists('security');
        $this->tpl_form_vars['ps_force_friendly_product'] = Configuration::get('PS_FORCE_FRIENDLY_PRODUCT');
        // autoload rich text editor (tiny mce)
        $this->tpl_form_vars['tinymce'] = true;
        $iso = $this->context->language->iso_code;
        $this->tpl_form_vars['iso'] = file_exists(_PS_CORE_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en';
        $this->tpl_form_vars['path_css'] = _THEME_CSS_DIR_;
        $this->tpl_form_vars['ad'] = __PS_BASE_URI__.basename(_PS_ADMIN_DIR_);
        if (Validate::isLoadedObject(($this->object))) {
            $id_product = (int)$this->object->id;
        } else {
            $id_product = (int)Tools::getvalue('id_product');
        }
        $page = (int)Tools::getValue('page');
        $this->tpl_form_vars['form_action'] = $this->context->link->getAdminLink('AdminProducts').'&'.($id_product ? 'updateproduct&id_product='.(int)$id_product : 'addproduct').($page > 1 ? '&page='.(int)$page : '');
        $this->tpl_form_vars['id_product'] = $id_product;
        // Transform configuration option 'upload_max_filesize' in octets
        $upload_max_filesize = Tools::getOctets(ini_get('upload_max_filesize'));
        // Transform configuration option 'upload_max_filesize' in MegaOctets
        $upload_max_filesize = ($upload_max_filesize / 1024) / 1024;
        $this->tpl_form_vars['upload_max_filesize'] = $upload_max_filesize;
        $this->tpl_form_vars['country_display_tax_label'] = $this->context->country->display_tax_label;
        $this->tpl_form_vars['has_combinations'] = $this->object->hasAttributes();
        $this->product_exists_in_shop = true;
        if ($this->display == 'edit' && Validate::isLoadedObject($product) && Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP && !$product->isAssociatedToShop($this->context->shop->id)) {
            $this->product_exists_in_shop = false;
            if ($this->tab_display == 'Informations') {
                $this->displayWarning($this->l('Warning: The product does not exist in this shop'));
            }
            $default_product = new Product();
            $definition = ObjectModel::getDefinition($product);
            foreach ($definition['fields'] as $field_name => $field) {
                if (isset($field['shop']) && $field['shop']) {
                    $product->$field_name = ObjectModel::formatValue($default_product->$field_name, $field['type']);
                }
            }
        }
        // let's calculate this once for all
        if (!Validate::isLoadedObject($this->object) && Tools::getValue('id_product')) {
            $this->errors[] = 'Unable to load object';
        } else {
            $this->_displayDraftWarning($this->object->active);
            // if there was an error while saving, we don't want to lose posted data
            if (!empty($this->errors)) {
                $this->copyFromPost($this->object, $this->table);
            }
            $this->initPack($this->object);
            $this->{'initForm'.$this->tab_display}($this->object);
            $this->tpl_form_vars['product'] = $this->object;
            if ($this->ajax) {
                if (!isset($this->tpl_form_vars['custom_form'])) {
                    throw new PrestaShopException('custom_form empty for action '.$this->tab_display);
                } else {
                    return $this->tpl_form_vars['custom_form'];
                }
            }
        }
        $parent = AdminController::renderForm();
        $this->addJqueryPlugin(array('autocomplete', 'fancybox', 'typewatch'));
        return $parent;
    }
    
    public function initFormInformations($product)
    {
        if (!$this->default_form_language) {
            $this->getLanguages();
        }
        $data = $this->createTemplate($this->tpl_form);
        $currency = $this->context->currency;
        $data->assign(array(
            'languages' => $this->_languages,
            'default_form_language' => $this->default_form_language,
            'currency' => $currency
        ));
        $this->object = $product;
        //$this->display = 'edit';
        $data->assign('product_name_redirected', Product::getProductName((int)$product->id_product_redirected, null, (int)$this->context->language->id));
        /*
        * Form for adding a virtual product like software, mp3, etc...
        */
        $product_download = new ProductDownload();
        if ($id_product_download = $product_download->getIdFromIdProduct($this->getFieldValue($product, 'id'))) {
            $product_download = new ProductDownload($id_product_download);
        }
        $product->{'productDownload'} = $product_download;
        $product_props = array();
        // global informations
        array_push($product_props, 'reference', 'ean13', 'upc',
        'available_for_order', 'show_price', 'online_only',
        'id_manufacturer'
        );
        // specific / detailled information
        array_push($product_props,
        // physical product
        'width', 'height', 'weight', 'active',
        // virtual product
        'is_virtual', 'cache_default_attribute',
        // customization
        'uploadable_files', 'text_fields'
        );
        // prices
        array_push($product_props,
            'price', 'wholesale_price', 'id_tax_rules_group', 'unit_price_ratio', 'on_sale',
            'unity', 'minimum_quantity', 'additional_shipping_cost',
            'available_now', 'available_later', 'available_date'
        );
        if (Configuration::get('PS_USE_ECOTAX')) {
            array_push($product_props, 'ecotax');
        }
        foreach ($product_props as $prop) {
            $product->$prop = $this->getFieldValue($product, $prop);
        }
        $product->name['class'] = 'updateCurrentText';
        if (!$product->id || Configuration::get('PS_FORCE_FRIENDLY_PRODUCT')) {
            $product->name['class'] .= ' copy2friendlyUrl';
        }
        $images = Image::getImages($this->context->language->id, $product->id);
        if (is_array($images)) {
            foreach ($images as $k => $image) {
                $images[$k]['src'] = $this->context->link->getImageLink($product->link_rewrite[$this->context->language->id], $product->id.'-'.$image['id_image'], ImageType::getFormatedName('small'));
            }
            $data->assign('images', $images);
        }
        $data->assign('imagesTypes', ImageType::getImagesTypes('products'));
        $product->tags = Tag::getProductTags($product->id);
        $data->assign('product_type', (int)Tools::getValue('type_product', $product->getType()));
        $data->assign('is_in_pack', (int)Pack::isPacked($product->id));
        $data->assign('product_parent', (int)Tools::getValue('product_parent'));
        $data->assign('single_use', (int)Tools::getValue('single_use'));
        $check_product_association_ajax = false;
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL) {
            $check_product_association_ajax = true;
        }
        // TinyMCE
        $iso_tiny_mce = $this->context->language->iso_code;
        $iso_tiny_mce = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso_tiny_mce.'.js') ? $iso_tiny_mce : 'en');
        $data->assign(array(
            'ad' => dirname($_SERVER['PHP_SELF']),
            'iso_tiny_mce' => $iso_tiny_mce,
            'check_product_association_ajax' => $check_product_association_ajax,
            'id_lang' => $this->context->language->id,
            'product' => $product,
            'token' => $this->token,
            'currency' => $currency,
            'link' => $this->context->link,
            'PS_PRODUCT_SHORT_DESC_LIMIT' => Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') ? Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') : 400
        ));
        $data->assign($this->tpl_form_vars);
        $this->tpl_form_vars['product'] = $product;
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }
    
    
}

?>