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

class NewProductsController extends NewProductsControllerCore
{
    
    public function initContent()
    {
        parent::initContent();

        $this->productSort();

        // Override default configuration values: cause the new products page must display latest products first.
        if (!Tools::getIsset('orderway') || !Tools::getIsset('orderby')) {
            $this->orderBy = 'date_add';
            $this->orderWay = 'DESC';
        }

        //$products = Product::getNewProducts($this->context->language->id, (int)$this->p - 1, (int)$this->n, false, $this->orderBy, $this->orderWay);
        $products = Manufacturer::getNewManufacturers();
        $context = Context::getContext();
        
        $nb_products = count($products);
        $this->pagination($nb_products);
        
        foreach ($products as &$p){
           $p['link'] = $context->link->getProductLink((int)$p['id_product'], $p['link_rewrite'], $p['name_category'], $p['ean13']);
           $p['manufacturer_name'] = $p['name'];
           
        }
        $this->addColorsToProductList($products);
        
        $sponsorships = RewardsSponsorshipModel::getSponsorshipAscendants($this->context->customer->id);
        $sponsorships2=array_slice($sponsorships, 1, 15);
        $sponsor = count($sponsorships2)+1;
        $this->context->smarty->assign('sponsor', $sponsor);

        $this->context->smarty->assign(array(
            's3'=> _S3_PATH_,
            'points_subcategories' => $this->pointSubcategories(),
            'products' => $products,
            'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            'nbProducts' => (int)$nb_products,
            'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
            'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM')
        ));

        $this->setTemplate(_PS_THEME_DIR_.'new-products.tpl');
    }
    
    public function pointSubcategories(){
        //$list_products = Product::getNewProducts($this->context->language->id, (int)$this->p - 1, (int)$this->n, false, $this->orderBy, $this->orderWay);
        $list_products = Manufacturer::getNewManufacturers();
        $array_subcat = array();
        foreach ($list_products as $p){
            $query_p = 'SELECT 
                        p.id_product,
                        pa.id_product as id_padre,
                        p.price,
                        (ROUND((p.price*(rp.value/100))/25)) as value,
                        p.reference
                        FROM
                        '._DB_PREFIX_.'product_attribute AS pa
                        RIGHT JOIN '._DB_PREFIX_.'product AS p ON pa.reference = p.reference
                        LEFT JOIN '._DB_PREFIX_.'rewards_product rp ON (rp.id_product = p.id_product)
                        WHERE pa.id_product='.$p['id_product'].' ORDER BY value DESC';
            
            $subcategories_p = Db::getInstance()->executeS($query_p);
            array_push($array_subcat, $subcategories_p[0]);
        }
        
        return $array_subcat;
    }
}
