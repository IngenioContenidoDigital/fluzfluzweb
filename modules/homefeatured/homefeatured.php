<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class HomeFeatured extends Module
{
	protected static $cache_products;

	public function __construct()
	{
		$this->name = 'homefeatured';
		$this->tab = 'front_office_features';
		$this->version = '1.8.0';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Featured products on the homepage');
		$this->description = $this->l('Displays featured products in the central column of your homepage.');
	}

	public function install()
	{
		$this->_clearCache('*');
		Configuration::updateValue('HOME_FEATURED_NBR', 8);
		Configuration::updateValue('HOME_FEATURED_CAT', (int)Context::getContext()->shop->getCategory());

		if (!parent::install()
			|| !$this->registerHook('header')
			|| !$this->registerHook('addproduct')
			|| !$this->registerHook('updateproduct')
			|| !$this->registerHook('deleteproduct')
			|| !$this->registerHook('categoryUpdate')
			|| !$this->registerHook('displayHomeTab')
                        || !$this->registerHook('customCMS')
                        || !$this->registerHook('newMerchants')
                        || !$this->registerHook('merchants')        
			|| !$this->registerHook('displayHomeTabContent')
		)
			return false;

		return true;
	}

	public function uninstall()
	{
		$this->_clearCache('*');

		return parent::uninstall();
	}

	public function getContent()
	{
		$output = '';
		$errors = array();
		if (Tools::isSubmit('submitHomeFeatured'))
		{
			$nbr = Tools::getValue('HOME_FEATURED_NBR');
			if (!Validate::isInt($nbr) || $nbr <= 0)
			$errors[] = $this->l('The number of products is invalid. Please enter a positive number.');

			$cat = Tools::getValue('HOME_FEATURED_CAT');
			if (!Validate::isInt($cat) || $cat < 0)
				$errors[] = $this->l('The category ID is invalid. Please choose an existing category ID.');
                        
                        $listcategories = "";
                        foreach ( $_POST as $var => $dat ) {
                            if ( $dat == 'on' ) {
                                $idcategory = str_replace("HOME_FEATURED_CAT_LIST_", "", $var);
                                $listcategories .= $idcategory.",";
                            }
                        }
                        $listcategories = substr($listcategories, 0, -1);
                        if (!$listcategories)
                            $listcategories = "0";

			$rand = Tools::getValue('HOME_FEATURED_RANDOMIZE');
			if (!Validate::isBool($rand))
				$errors[] = $this->l('Invalid value for the "randomize" flag.');
			if (isset($errors) && count($errors))
				$output = $this->displayError(implode('<br />', $errors));
			else
			{
				Configuration::updateValue('HOME_FEATURED_NBR', (int)$nbr);
				Configuration::updateValue('HOME_FEATURED_CAT', (int)$cat);
				Configuration::updateValue('HOME_FEATURED_RANDOMIZE', (bool)$rand);
				Configuration::updateValue('HOME_FEATURED_CAT_LIST', $listcategories);
				Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('homefeatured.tpl'));
				$output = $this->displayConfirmation($this->l('Your settings have been updated.'));
			}
		}

		return $output.$this->renderForm();
	}
        
        public function hookcustomCMS($params)
	 {
	 
	  if (Tools::getValue('id_cms') != 6)
	   return;
	 
	     return $this->hookDisplayHome($params);
	 }
         
        public function hooknewMerchants($params)
        {
	 
            $carousel= ManufacturerCore::getManufacturersCategory();
            
	    /*if (!$this->isCached('merchants.tpl', $this->getCacheId()))
            {*/
                $this->smarty->assign(
                    array(
                        's3'=> _S3_PATH_,
                        'merchants' => $carousel,
                        'sponsor' => $this->getSponsor()
                    )
                );
            //}

            return $this->display(__FILE__, 'newMerchants.tpl');
	 }
         
         public function hookmerchants($params)
	 {
            $carousel = ManufacturerCore::getNewManufacturers();
            
	  /*if (!$this->isCached('newMerchants.tpl', $this->getCacheId()))
		{*/
			$this->smarty->assign(
				array(
                                        's3'=> _S3_PATH_,
					'merchants' => $carousel,
                                        'sponsor' => $this->getSponsor()
				)
			);
		//}

		return $this->display(__FILE__, 'merchants.tpl');
	 }
         
        public function getSponsor(){
            
            $sponsorships = RewardsSponsorshipModel::getSponsorshipAscendants($this->context->customer->id);
            $sponsorships2=array_slice($sponsorships, 1, 15);
            $sponsor = count($sponsorships2)+1;
                return $sponsor;
        } 

	public function hookDisplayHeader($params)
	{
		$this->hookHeader($params);
	}

	public function hookHeader($params)
	{
		if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index')
			$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');
		$this->context->controller->addCSS(($this->_path).'css/homefeatured.css', 'all');
	}

	public function _cacheProducts()
	{
		if (!isset(HomeFeatured::$cache_products))
		{
                    $listProductFeatured = [];
                    if ( (int)Configuration::get('HOME_FEATURED_CAT_LIST') == 0 ) {
                        $categories = Category::getSimpleCategories((int)Context::getContext()->language->id);
                        foreach ( $categories as $cat ) {
                            $category = new Category((int)$cat['id_category'], (int)Context::getContext()->language->id);
                            $nb = (int)Configuration::get('HOME_FEATURED_NBR');
                            if (Configuration::get('HOME_FEATURED_RANDOMIZE')) {
                                    $listProducts = $category->getProducts((int)Context::getContext()->language->id, 1, ($nb ? $nb : 8), null, null, false, true, true, ($nb ? $nb : 8));
                            } else {
                                    $listProducts = $category->getProducts((int)Context::getContext()->language->id, 1, ($nb ? $nb : 8), 'position');
                            }
                            $listProductFeatured = array_merge($listProductFeatured,$listProducts);
                        }
                        HomeFeatured::$cache_products = array_unique($listProductFeatured, SORT_REGULAR);
                    } else {
                        $categories = explode(",",Configuration::get('HOME_FEATURED_CAT_LIST'));
                        foreach ( $categories as $cat ) {
                            $category = new Category((int)$cat, (int)Context::getContext()->language->id);
                            $nb = (int)Configuration::get('HOME_FEATURED_NBR');
                            if (Configuration::get('HOME_FEATURED_RANDOMIZE')) {
                                    $listProducts = $category->getProducts((int)Context::getContext()->language->id, 1, ($nb ? $nb : 8), null, null, false, true, true, ($nb ? $nb : 8));
                            } else {
                                    $listProducts = $category->getProducts((int)Context::getContext()->language->id, 1, ($nb ? $nb : 8), 'position');
                            }
                            $listProductFeatured = array_merge($listProductFeatured,$listProducts);
                        }
                        HomeFeatured::$cache_products = array_unique($listProductFeatured, SORT_REGULAR);
                    }
		}

		if (HomeFeatured::$cache_products === false || empty(HomeFeatured::$cache_products))
			return false;
	}

	public function hookDisplayHomeTab($params)
	{
		if (!$this->isCached('tab.tpl', $this->getCacheId('homefeatured-tab')))
			$this->_cacheProducts();

		return $this->display(__FILE__, 'tab.tpl', $this->getCacheId('homefeatured-tab'));
	}

	public function hookDisplayHome($params)
	{
		if (!$this->isCached('homefeatured.tpl', $this->getCacheId()))
		{
			$this->_cacheProducts();
			$this->smarty->assign(
				array(
                                        's3'=> _S3_PATH_,
					'products' => HomeFeatured::$cache_products,
					'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
					'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
				)
			);
		}

		return $this->display(__FILE__, 'homefeatured.tpl', $this->getCacheId());
	}

	public function hookDisplayHomeTabContent($params)
	{
		return $this->hookDisplayHome($params);
	}

	public function hookAddProduct($params)
	{
		$this->_clearCache('*');
	}

	public function hookUpdateProduct($params)
	{
		$this->_clearCache('*');
	}

	public function hookDeleteProduct($params)
	{
		$this->_clearCache('*');
	}

	public function hookCategoryUpdate($params)
	{
		$this->_clearCache('*');
	}

	public function _clearCache($template, $cache_id = NULL, $compile_id = NULL)
	{
		parent::_clearCache('homefeatured.tpl');
		parent::_clearCache('tab.tpl', 'homefeatured-tab');
	}

	public function renderForm()
	{
            $categories = Category::getAllCategoriesName();
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'description' => $this->l('To add products to your homepage, simply add them to the corresponding product category (default: "Home").'),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Number of products to be displayed'),
						'name' => 'HOME_FEATURED_NBR',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Set the number of products that you would like to display on homepage (default: 8).'),
					),
					/*array(
						'type' => 'text',
						'label' => $this->l('Category from which to pick products to be displayed'),
						'name' => 'HOME_FEATURED_CAT',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Choose the category ID of the products that you would like to display on homepage (default: 2 for "Home").')." Ingrese 0 para seleccionar todas las categorias",
					),*/
                                        array(
                                                'type' => 'checkbox',
                                                'label' => $this->l('Category from which to pick products to be displayed'),
                                                'desc' => $this->l('Elija las categor�as de los productos que desea mostrar en la p�gina de inicio. Si no selecciona ninguna, se mostraran los productos de todas las categorias.'),
                                                'name' => 'HOME_FEATURED_CAT_LIST',
                                                'values' => array(
                                                    'query' => $categories,
                                                    'id' => 'id_category',
                                                    'name' => 'name'
                                                ),
                                        ),
					array(
						'type' => 'switch',
						'label' => $this->l('Randomly display featured products'),
						'name' => 'HOME_FEATURED_RANDOMIZE',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Enable if you wish the products to be displayed randomly (default: no).'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->id = (int)Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitHomeFeatured';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		$fieldsValues = array(
			'HOME_FEATURED_NBR' => Tools::getValue('HOME_FEATURED_NBR', (int)Configuration::get('HOME_FEATURED_NBR')),
			'HOME_FEATURED_CAT' => Tools::getValue('HOME_FEATURED_CAT', (int)Configuration::get('HOME_FEATURED_CAT')),
			'HOME_FEATURED_RANDOMIZE' => Tools::getValue('HOME_FEATURED_RANDOMIZE', (bool)Configuration::get('HOME_FEATURED_RANDOMIZE')),
		);
                
                $listCategories = explode(",",Configuration::get('HOME_FEATURED_CAT_LIST'));
                foreach ( $listCategories as $idcategory ) {
                    $fieldsValues["HOME_FEATURED_CAT_LIST_".$idcategory] = "checked";
                }
                
                return $fieldsValues;
	}
}
