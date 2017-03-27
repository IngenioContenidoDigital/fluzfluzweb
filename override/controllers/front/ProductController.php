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

require_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsSponsorshipModel.php');

class ProductController extends ProductControllerCore
{
    public function initContent()
    {
        FrontController::initContent();
        
        if (!$this->errors) {
            if (Pack::isPack((int)$this->product->id) && !Pack::isInStock((int)$this->product->id)) {
                $this->product->quantity = 0;
            }

            $this->product->description = $this->transformDescriptionWithImg($this->product->description);
            $sponsorships = RewardsSponsorshipModel::getSponsorshipAscendants($this->context->customer->id);
            $sponsorships2=array_slice($sponsorships, 1, 15);
            $sponsor = count($sponsorships2)+1;
            $this->context->smarty->assign('sponsor', $sponsor);
            //$price = (int)$this->product->price - RewardsProductModel::getCostDifference($this->product->id);
            $price = RewardsProductModel::getProductReward($this->product->id,(int)$this->product->price,1, $this->context->currency->id);
            $productP=round(RewardsModel::getRewardReadyForDisplay($price, $this->context->currency->id)/(count($sponsorships2)+1));
            $this->context->smarty->assign("productP", $productP);
            
            $productP2=RewardsModel::getRewardReadyForDisplay($price, $this->context->currency->id)/16;
            $resultProduct = round($productP2, $precision=0);
            $this->context->smarty->assign("resultProduct", $resultProduct);
            
            // Assign to the template the id of the virtual product. "0" if the product is not downloadable.
            $this->context->smarty->assign('virtual', ProductDownload::getIdFromIdProduct((int)$this->product->id));

            $this->context->smarty->assign('customizationFormTarget', Tools::safeOutput(urldecode($_SERVER['REQUEST_URI'])));

            if (Tools::isSubmit('submitCustomizedDatas')) {
                // If cart has not been saved, we need to do it so that customization fields can have an id_cart
                // We check that the cookie exists first to avoid ghost carts
                if (!$this->context->cart->id && isset($_COOKIE[$this->context->cookie->getName()])) {
                    $this->context->cart->add();
                    $this->context->cookie->id_cart = (int)$this->context->cart->id;
                }
                $this->pictureUpload();
                $this->textRecord();
                $this->formTargetFormat();
            } elseif (Tools::getIsset('deletePicture') && !$this->context->cart->deleteCustomizationToProduct($this->product->id, Tools::getValue('deletePicture'))) {
                $this->errors[] = Tools::displayError('An error occurred while deleting the selected picture.');
            }

            $pictures = array();
            $text_fields = array();
            if ($this->product->customizable) {
                $files = $this->context->cart->getProductCustomization($this->product->id, Product::CUSTOMIZE_FILE, true);
                foreach ($files as $file) {
                    $pictures['pictures_'.$this->product->id.'_'.$file['index']] = $file['value'];
                }

                $texts = $this->context->cart->getProductCustomization($this->product->id, Product::CUSTOMIZE_TEXTFIELD, true);

                foreach ($texts as $text_field) {
                    $text_fields['textFields_'.$this->product->id.'_'.$text_field['index']] = str_replace('<br />', "\n", $text_field['value']);
                }
            }

            $this->context->smarty->assign(array(
                'pictures' => $pictures,
                'textFields' => $text_fields));

            $this->product->customization_required = false;
            $customization_fields = $this->product->customizable ? $this->product->getCustomizationFields($this->context->language->id) : false;
            if (is_array($customization_fields)) {
                foreach ($customization_fields as $customization_field) {
                    if ($this->product->customization_required = $customization_field['required']) {
                        break;
                    }
                }
            }

            // Assign template vars related to the category + execute hooks related to the category
            $this->assignCategory();
            // Assign template vars related to the price and tax
            $this->assignPriceAndTax();

            // Assign template vars related to the images
            $this->assignImages();
            // Assign attribute groups to the template
            $this->assignAttributesGroups();

            // Assign attributes combinations to the template
            $this->assignAttributesCombinations();

            // Pack management
            $pack_items = Pack::isPack($this->product->id) ? Pack::getItemTable($this->product->id, $this->context->language->id, true) : array();
            $this->context->smarty->assign('packItems', $pack_items);
            $this->context->smarty->assign('packs', Pack::getPacksTable($this->product->id, $this->context->language->id, true, 1));

            if (isset($this->category->id) && $this->category->id) {
                $return_link = Tools::safeOutput($this->context->link->getCategoryLink($this->category));
            } else {
                $return_link = 'javascript: history.back();';
            }

            $accessories = $this->product->getAccessories($this->context->language->id);
            if ($this->product->cache_is_pack || count($accessories)) {
                $this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');
            }
            if ($this->product->customizable) {
                $customization_datas = $this->context->cart->getProductCustomization($this->product->id, null, true);
            }
            
            $urlbanner1 = _S3_PATH_."p-banners/".Tools::getValue('id_product')."_0.jpg";
            $existsbanner1 = file_get_contents( $urlbanner1 );
            if( !$existsbanner1 ) {
                $urlbanner1 = "";
            }

            $urlbanner2 = _S3_PATH_."p-banners/".Tools::getValue('id_product')."_1.jpg";
            $existsbanner2 = file_get_contents( $urlbanner2 );
            if( !$existsbanner2 ) {
                $urlbanner2 = "";
            }
            
            $this->context->smarty->assign('imgbanner1', $urlbanner1 );
            $this->context->smarty->assign('imgbanner2', $urlbanner2 );
            
            $count_address = count($this->addressManufacturers());
            
            $this->context->smarty->assign(array(
                'stock_management' => Configuration::get('PS_STOCK_MANAGEMENT'),
                'customizationFields' => $customization_fields,
                'id_customization' => empty($customization_datas) ? null : $customization_datas[0]['id_customization'],
                'accessories' => $accessories,
                'return_link' => $return_link,
                'product' => $this->product,
                'product_manufacturer' => new Manufacturer((int)$this->product->id_manufacturer, $this->context->language->id),
                'address_manufacturer' => $this->addressManufacturers(),
                'count_address' => $count_address,
                'token' => Tools::getToken(false),
                'features' => $this->product->getFrontFeatures($this->context->language->id),
                'attachments' => (($this->product->cache_has_attachments) ? $this->product->getAttachments($this->context->language->id) : array()),
                'allow_oosp' => $this->product->isAvailableWhenOutOfStock((int)$this->product->out_of_stock),
                'last_qties' =>  (int)Configuration::get('PS_LAST_QTIES'),
                'HOOK_EXTRA_LEFT' => Hook::exec('displayLeftColumnProduct'),
                'HOOK_EXTRA_RIGHT' => Hook::exec('displayRightColumnProduct'),
                'HOOK_PRODUCT_OOS' => Hook::exec('actionProductOutOfStock', array('product' => $this->product)),
                'HOOK_PRODUCT_ACTIONS' => Hook::exec('displayProductButtons', array('product' => $this->product)),
                'HOOK_PRODUCT_TAB' =>  Hook::exec('displayProductTab', array('product' => $this->product)),
                'HOOK_PRODUCT_TAB_CONTENT' =>  Hook::exec('displayProductTabContent', array('product' => $this->product)),
                'HOOK_PRODUCT_CONTENT' =>  Hook::exec('displayProductContent', array('product' => $this->product)),
                'display_qties' => (int)Configuration::get('PS_DISPLAY_QTIES'),
                'display_ht' => !Tax::excludeTaxeOption(),
                'jqZoomEnabled' => Configuration::get('PS_DISPLAY_JQZOOM'),
                'ENT_NOQUOTES' => ENT_NOQUOTES,
                'outOfStockAllowed' => (int)Configuration::get('PS_ORDER_OUT_OF_STOCK'),
                'errors' => $this->errors,
                'body_classes' => array(
                    $this->php_self.'-'.$this->product->id,
                    $this->php_self.'-'.$this->product->link_rewrite,
                    'category-'.(isset($this->category) ? $this->category->id : ''),
                    'category-'.(isset($this->category) ? $this->category->getFieldByLang('link_rewrite') : '')
                ),
                'display_discount_price' => Configuration::get('PS_DISPLAY_DISCOUNT_PRICE'),
            ));
        }
        $this->setTemplate(_PS_THEME_DIR_.'product.tpl');
    }
    
    public function  addressManufacturers(){
        
        $query = 'SELECT address1, city, firstname FROM ps_address where id_manufacturer = '.$this->product->id_manufacturer.' AND deleted = 0';
        $address = Db::getInstance()->executeS($query);
        
        return $address;
    }
    
    protected function assignImages()
    {
        $images = $this->product->getImages((int)$this->context->cookie->id_lang);
        $product_images = array();
        if (isset($images[0])) {
            $this->context->smarty->assign('mainImage', $images[0]);
        }
        foreach ($images as $k => $image) {
            if ($image['cover']) {
                $this->context->smarty->assign('mainImage', $image);
                $cover = $image;
                $cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($this->product->id.'-'.$image['id_image']) : $image['id_image']);
                $cover['id_image_only'] = (int)$image['id_image'];
            }
            $product_images[(int)$image['id_image']] = $image;
        }
        if (!isset($cover)) {
            if (isset($images[0])) {
                $cover = $images[0];
                $cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($this->product->id.'-'.$images[0]['id_image']) : $images[0]['id_image']);
                $cover['id_image_only'] = (int)$images[0]['id_image'];
            } else {
                $cover = array(
                    'id_image' => $this->context->language->iso_code.'-default',
                    'legend' => 'No picture',
                    'title' => 'No picture'
                );
            }
        }
        $size = Image::getSize(ImageType::getFormatedName('large'));
        $this->context->smarty->assign(array(
            's3'=> _S3_PATH_,
            'have_image' => (isset($cover['id_image']) && (int)$cover['id_image'])? array((int)$cover['id_image']) : Product::getCover((int)Tools::getValue('id_product')),
            'cover' => $cover,
            'imgWidth' => (int)$size['width'],
            'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
            'largeSize' => Image::getSize(ImageType::getFormatedName('large')),
            'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
            'cartSize' => Image::getSize(ImageType::getFormatedName('cart')),
            'col_img_dir' => _PS_COL_IMG_DIR_));
        if (count($product_images)) {
            $this->context->smarty->assign('images', $product_images);
        }
    }
    
    protected function assignAttributesGroups()
    {
        $colors = array();
        $groups = array();
        // @todo (RM) should only get groups and not all declination ?
        $attributes_groups = $this->product->getAttributesGroups($this->context->language->id);
        if (is_array($attributes_groups) && $attributes_groups) {
            $combination_images = $this->product->getCombinationImages($this->context->language->id);
            $combination_prices_set = array();
            foreach ($attributes_groups as $k => $row) {
                // Color management
                if (isset($row['is_color_group']) && $row['is_color_group'] && (isset($row['attribute_color']) && $row['attribute_color']) || (file_exists(_PS_COL_IMG_DIR_.$row['id_attribute'].'.jpg'))) {
                    $colors[$row['id_attribute']]['value'] = $row['attribute_color'];
                    $colors[$row['id_attribute']]['name'] = $row['attribute_name'];
                    if (!isset($colors[$row['id_attribute']]['attributes_quantity'])) {
                        $colors[$row['id_attribute']]['attributes_quantity'] = 0;
                    }
                    $colors[$row['id_attribute']]['attributes_quantity'] += (int)$row['quantity'];
                }
                if (!isset($groups[$row['id_attribute_group']])) {
                    $groups[$row['id_attribute_group']] = array(
                        'group_name' => $row['group_name'],
                        'name' => $row['public_group_name'],
                        'id_product' => $row['id_product_attribute'],
                        'group_type' => $row['group_type'],
                        'default' => -1,
                    );
                }
                
                $groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = $row['attribute_name'];
                if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1) {
                    $groups[$row['id_attribute_group']]['default'] = (int)$row['id_attribute'];
                }
                if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']])) {
                    $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
                }
                $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int)$row['quantity'];
                $combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
                $combinations[$row['id_product_attribute']]['attributes'][] = (int)$row['id_attribute'];
                $combinations[$row['id_product_attribute']]['price'] = (float)Tools::convertPriceFull($row['price'], null, Context::getContext()->currency);
                // Call getPriceStatic in order to set $combination_specific_price
                if (!isset($combination_prices_set[(int)$row['id_product_attribute']])) {
                    Product::getPriceStatic((int)$this->product->id, false, $row['id_product_attribute'], 6, null, false, true, 1, false, null, null, null, $combination_specific_price);
                    $combination_prices_set[(int)$row['id_product_attribute']] = true;
                    $combinations[$row['id_product_attribute']]['specific_price'] = $combination_specific_price;
                }
                $combinations[$row['id_product_attribute']]['ecotax'] = (float)$row['ecotax'];
                $combinations[$row['id_product_attribute']]['weight'] = (float)$row['weight'];
                $combinations[$row['id_product_attribute']]['quantity'] = (int)$row['quantity'];
                $combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
                $combinations[$row['id_product_attribute']]['unit_impact'] = Tools::convertPriceFull($row['unit_price_impact'], null, Context::getContext()->currency);
                $combinations[$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];
                
                if ($row['available_date'] != '0000-00-00' && Validate::isDate($row['available_date'])) {
                    $combinations[$row['id_product_attribute']]['available_date'] = $row['available_date'];
                    $combinations[$row['id_product_attribute']]['date_formatted'] = Tools::displayDate($row['available_date']);
                } else {
                    $combinations[$row['id_product_attribute']]['available_date'] = $combinations[$row['id_product_attribute']]['date_formatted'] = '';
                }
                if (!isset($combination_images[$row['id_product_attribute']][0]['id_image'])) {
                    $combinations[$row['id_product_attribute']]['id_image'] = -1;
                } else {
                    $combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int)$combination_images[$row['id_product_attribute']][0]['id_image'];
                    if ($row['default_on']) {
                        if (isset($this->context->smarty->tpl_vars['cover']->value)) {
                            $current_cover = $this->context->smarty->tpl_vars['cover']->value;
                        }
                        if (is_array($combination_images[$row['id_product_attribute']])) {
                            foreach ($combination_images[$row['id_product_attribute']] as $tmp) {
                                if ($tmp['id_image'] == $current_cover['id_image']) {
                                    $combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int)$tmp['id_image'];
                                    break;
                                }
                            }
                        }
                        if ($id_image > 0) {
                            if (isset($this->context->smarty->tpl_vars['images']->value)) {
                                $product_images = $this->context->smarty->tpl_vars['images']->value;
                            }
                            if (isset($product_images) && is_array($product_images) && isset($product_images[$id_image])) {
                                $product_images[$id_image]['cover'] = 1;
                                $this->context->smarty->assign('mainImage', $product_images[$id_image]);
                                if (count($product_images)) {
                                    $this->context->smarty->assign('images', $product_images);
                                }
                            }
                            if (isset($this->context->smarty->tpl_vars['cover']->value)) {
                                $cover = $this->context->smarty->tpl_vars['cover']->value;
                            }
                            if (isset($cover) && is_array($cover) && isset($product_images) && is_array($product_images)) {
                                $product_images[$cover['id_image']]['cover'] = 0;
                                if (isset($product_images[$id_image])) {
                                    $cover = $product_images[$id_image];
                                }
                                $cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($this->product->id.'-'.$id_image) : (int)$id_image);
                                $cover['id_image_only'] = (int)$id_image;
                                $this->context->smarty->assign('cover', $cover);
                            }
                        }
                    }
                }
            }
            // wash attributes list (if some attributes are unavailables and if allowed to wash it)
            if (!Product::isAvailableWhenOutOfStock($this->product->out_of_stock) && Configuration::get('PS_DISP_UNAVAILABLE_ATTR') == 0) {
                foreach ($groups as &$group) {
                    foreach ($group['attributes_quantity'] as $key => &$quantity) {
                        if ($quantity <= 0) {
                            unset($group['attributes'][$key]);
                        }
                    }
                }
                foreach ($colors as $key => $color) {
                    if ($color['attributes_quantity'] <= 0) {
                        unset($colors[$key]);
                    }
                }
            }
            
            foreach ($combinations as $id_product_attribute => $comb) {
                $attribute_list = '';
                foreach ($comb['attributes'] as $id_attribute) {
                    $attribute_list .= '\''.(int)$id_attribute.'\',';
                }
                $attribute_list = rtrim($attribute_list, ',');
                $combinations[$id_product_attribute]['list'] = $attribute_list;
            }
            
            $listproducts = array();
            
            foreach ($combinations as $prueba) {
                
                $query = 'SELECT p.price_shop, p.price, p.save_dolar, p.id_product, p.online_only as online, 
                          p.single_use, p.type_currency, p.expiration, p.id_manufacturer, pl.link_rewrite, 
                          pl.name, pa.id_product_attribute, ac.id_attribute,
                          (rp.value/100) as value FROM '._DB_PREFIX_.'product p
                          LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (p.id_product = pl.id_product)
                          LEFT JOIN '._DB_PREFIX_.'rewards_product rp ON (p.id_product = rp.id_product)
                          LEFT JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = p.id_product)        
                          LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (p.reference = pa.reference)        
                          LEFT JOIN '._DB_PREFIX_.'product_attribute_combination ac ON (pa.id_product_attribute=ac.id_product_attribute)        
                          WHERE p.reference = "'.$prueba['reference'].'" 
                          AND ps.active = 1    
                          AND pl.id_lang = '.$this->context->language->id;
                
                $for = Db::getInstance()->executeS($query);
                
                if(!empty($for)){
                    array_push($listproducts, $for[0]);
                }
            }
            
            usort($listproducts, function($a, $b) {
                return $a['price'] - $b['price'];
            });
            
            $this->context->smarty->assign(array(
                'groups' => $groups,
                'colors' => (count($colors)) ? $colors : false,
                'combinations' => $combinations,
                'combinationsList' => $listproducts,
                'combinationImages' => $combination_images
            ));
            
        }
    }
    
}

?>