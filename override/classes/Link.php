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

class Link extends LinkCore
{
    public function getImageLink($name, $ids, $type = null)
    {
        $not_default = false;

        // Check if module is installed, enabled, customer is logged in and watermark logged option is on
        if (Configuration::get('WATERMARK_LOGGED') && (Module::isInstalled('watermark') && Module::isEnabled('watermark')) && isset(Context::getContext()->customer->id)) {
            $type .= '-'.Configuration::get('WATERMARK_HASH');
        }

        // legacy mode or default image
        $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').'-'.(int)Context::getContext()->shop->id_theme.'.jpg')) ? '-'.Context::getContext()->shop->id_theme : '');
        if ((Configuration::get('PS_LEGACY_IMAGES')
            && (file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').$theme.'.jpg')))
            || ($not_default = strpos($ids, 'default') !== false)) {
            if ($this->allow == 1 && !$not_default) {
                $uri_path = __PS_BASE_URI__.$ids.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
            } else {
                $uri_path = _THEME_PROD_DIR_.$ids.($type ? '-'.$type : '').$theme.'.jpg';
            }
        } else {
            // if ids if of the form id_product-id_image, we want to extract the id_image part
            $split_ids = explode('-', $ids);
            $id_image = (isset($split_ids[1]) ? $split_ids[1] : $split_ids[0]);
            $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').'-'.(int)Context::getContext()->shop->id_theme.'.jpg')) ? '-'.Context::getContext()->shop->id_theme : '');
            if ($this->allow == 1) {
                $uri_path = __PS_BASE_URI__.$id_image.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
            } else {
                $uri_path = _THEME_PROD_DIR_.Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').$theme.'.jpg';
            }
        }
        //if (Configuration::get("PS_SHOP_DOMAIN")!='fluzfluz.co') $dev='dev/';
        //return $this->protocol_content.Tools::getMediaServer($uri_path).$uri_path;
        return _S3_PATH_.'p/'.$id_image.'-'.$type.'.jpg';
        
    }
    
    public function getCatImageLink($name, $id_category, $type = null)
    {
        if ($this->allow == 1 && $type) {
            $uri_path = __PS_BASE_URI__.'c/'.$id_category.'-'.$type.'/'.$name.'.jpg';
        } else {
            $uri_path = _THEME_CAT_DIR_.$id_category.($type ? '-'.$type : '').'.jpg';
        }
        //return $this->protocol_content.Tools::getMediaServer($uri_path).$uri_path;
        //if (Configuration::get("PS_SHOP_DOMAIN")!='fluzfluz.co') $dev='dev/';
        //return $this->protocol_content.Tools::getMediaServer($uri_path).$uri_path;
        return _S3_PATH_.'c/'.$id_category.'-'.$type.'.jpg';
    }
}
