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

class ProductsCategoryOverride extends ProductsCategory
{
    public function hookProductFooter($params)
	{
		$id_product = (int)$params['product']->id;
		$product = $params['product'];
                
		//$cache_id = 'productscategory|'.$id_product.'|'.(isset($params['category']->id_category) ? (int)$params['category']->id_category : (int)$product->id_category_default);

		/*if (!$this->isCached('productscategory.tpl', $this->getCacheId($cache_id)))
		{*/

			$category = false;
			if (isset($params['category']->id_category))
				$category = $params['category'];
			else
			{
				if (isset($product->id_category_default) && $product->id_category_default > 1)
					$category = new Category((int)$product->id_category_default);
			}

			if (!Validate::isLoadedObject($category) || !$category->active)
				return false;

			// Get infos
			$category_products = $category->getProducts($this->context->language->id, 1, 100); /* 100 products max. */
			$nb_category_products = (int)count($category_products);
			$middle_position = 0;
                        
                        $keys = array_keys($category_products); 
                        shuffle($keys); 
                        $random = array(); 
                        foreach ($keys as $key) { 
                            $random[$key] = $category_products[$key];
                        }
                        
                        foreach ($random as &$r){
                            
                           if($r['id_image_parent']!=''){
                               $url_image = _S3_PATH_.'p/'.$r['id_image_parent'].'.jpg';
                           }
                           else{
                               $url_image = _S3_PATH_.'m/m/'.$r['id_manufacturer'].'.jpg';
                           }
                            
                           $r['url_exists'] = $url_image;
                        }
                        
                        $array_recomend = array_slice($random, 0, 4);
                        $products_recomend = array_chunk($array_recomend, ceil(count($array_recomend)/4));
                        $list_products = array_map('current', $products_recomend);
                        
                        $array_subcat = array();
                        
                        foreach ($list_products as &$p){
                            $query_p = 'SELECT 
                                        p.id_product,
                                        p.id_manufacturer,
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
                        
			// Remove current product from the list
			if (is_array($category_products) && count($category_products))
			{
				foreach ($category_products as $key => $category_product)
				{
					if ($category_product['id_product'] == $id_product)
					{
						unset($category_products[$key]);
						break;
					}
				}

				$taxes = Product::getTaxCalculationMethod();
				if (Configuration::get('PRODUCTSCATEGORY_DISPLAY_PRICE'))
				{
					foreach ($category_products as $key => $category_product)
					{
						if ($category_product['id_product'] != $id_product)
						{
							if ($taxes == 0 || $taxes == 2)
							{
								$category_products[$key]['displayed_price'] = Product::getPriceStatic(
									(int)$category_product['id_product'],
									true,
									null,
									2
								);
							} elseif ($taxes == 1)
							{
								$category_products[$key]['displayed_price'] = Product::getPriceStatic(
									(int)$category_product['id_product'],
									false,
									null,
									2
								);
							}
						}
					}
				}

				// Get positions
				$middle_position = (int)round($nb_category_products / 2, 0);
				$product_position = $this->getCurrentProduct($category_products, (int)$id_product);

				// Flip middle product with current product
				if ($product_position)
				{
					$tmp = $category_products[$middle_position - 1];
					$category_products[$middle_position - 1] = $category_products[$product_position];
					$category_products[$product_position] = $tmp;
				}

				// If products tab higher than 30, slice it
				if ($nb_category_products > 30)
				{
					$category_products = array_slice($category_products, $middle_position - 15, 30, true);
					$middle_position = 15;
				}
			}

			// Display tpl
			$this->smarty->assign(
				array(
                                        's3'=>_S3_PATH_,
					'categoryProducts' => $products_recomend[0],
                                        'categoryProducts2' => $products_recomend[1],
                                        'categoryProducts3' => $products_recomend[2],
                                        'categoryProducts4' => $products_recomend[3],
                                        'points_subcategories' => $array_subcat,
                                        'categoryMovil' => $array_recomend,
					'middlePosition' => (int)$middle_position,
					'ProdDisplayPrice' => Configuration::get('PRODUCTSCATEGORY_DISPLAY_PRICE')
				)
			);
		//}

		return $this->display(__FILE__, 'productscategory.tpl');
	}
        
}

?>