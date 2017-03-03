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
require_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsModel.php');
require_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsSponsorshipModel.php');
require_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsProductModel.php');

class Category extends CategoryCore
{
    public function getSubCategories($id_lang, $active = true)
    {
        $sql_groups_where = '';
        $sql_groups_join = '';
        if (Group::isFeatureActive()) {
            $sql_groups_join = 'LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = c.`id_category`)';
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups_where = 'AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '='.(int)Group::getCurrent()->id);
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.*, cl.id_lang, cl.name, cl.description, cl.link_rewrite, cl.meta_title, cl.meta_keywords, cl.meta_description
		FROM `'._DB_PREFIX_.'category` c
		'.Shop::addSqlAssociation('category', 'c').'
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.(int)$id_lang.' '.Shop::addSqlRestrictionOnLang('cl').')
		'.$sql_groups_join.'
		WHERE `id_parent` = '.(int)$this->id.'
		'.($active ? 'AND `active` = 1' : '').'
		'.$sql_groups_where.'
		GROUP BY c.`id_category`
		ORDER BY `level_depth` ASC, category_shop.`position` ASC');

        $formated_medium = ImageType::getFormatedName('medium');

        foreach ($result as &$row) {
            $row['id_image'] = Tools::file_exists_cache(_PS_CAT_IMG_DIR_.$row['id_category'].'.jpg') ? (int)$row['id_category'] : Language::getIsoById($id_lang).'-default';
            $row['legend'] = 'no picture';
        }
        return $result;
    }
    
    public function getProducts($id_lang, $p, $n, $order_by = null, $order_way = null, $get_total = false, $active = true, $random = false, $random_number_products = 1, $check_access = true, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        if ($check_access && !$this->checkAccess($context->customer->id)) {
            return false;
        }

        $front = in_array($context->controller->controller_type, array('front', 'modulefront'));
        $id_supplier = (int)Tools::getValue('id_supplier');

        /** Return only the number of products */
        if ($get_total) {
            $sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE cp.`id_category` = '.(int)$this->id.' AND p.product_parent = 1'.
                ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
                ($active ? ' AND product_shop.`active` = 1' : '').
                ($id_supplier ? 'AND p.id_supplier = '.(int)$id_supplier : '');

            return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }
        
        
        if ($p < 1) {
            $p = 1;
        }

        /** Tools::strtolower is a fix for all modules which are now using lowercase values for 'orderBy' parameter */
        $order_by  = Validate::isOrderBy($order_by)   ? Tools::strtolower($order_by)  : 'position';
        $order_way = Validate::isOrderWay($order_way) ? Tools::strtoupper($order_way) : 'ASC';

        $order_by_prefix = false;
        if ($order_by == 'id_product' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'manufacturer' || $order_by == 'manufacturer_name') {
            $order_by_prefix = 'm';
            $order_by = 'name';
        } elseif ($order_by == 'position') {
            $order_by_prefix = 'cp';
        }

        if ($order_by == 'price') {
            $order_by = 'orderprice';
        }

        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }
        
        /*'.RewardsModel::getRewardReadyForDisplay('p.price', 1).' AS points*/
        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity'.(Combination::isFeatureActive() ? ', IFNULL(product_attribute_shop.id_product_attribute, 0) AS id_product_attribute,
					product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity' : '').', pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image` id_image,
					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB("'.date('Y-m-d').' 00:00:00",
					INTERVAL '.(int)$nb_days_new_product.' DAY)) > 0 AS new, product_shop.price AS orderprice   
				FROM `'._DB_PREFIX_.'category_product` cp
                                LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').
                (Combination::isFeatureActive() ? ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')':'').'
				'.Product::sqlStock('p', 0).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `ps_rewards_product` AS rp
					ON (rp.id_product = p.`id_product`)
                                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE product_shop.`id_shop` = '.(int)$context->shop->id.' AND p.product_parent = 1
					AND cp.`id_category` = '.(int)$this->id
                    .($active ? ' AND product_shop.`active` = 1' : '')
                    .($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
                    .($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '');

        if ($random === true) {
            $sql .= ' ORDER BY RAND() LIMIT '.(int)$random_number_products;
        } else {
            $sql .= ' ORDER BY '.(!empty($order_by_prefix) ? $order_by_prefix.'.' : '').'`'.bqSQL($order_by).'` '.pSQL($order_way).'
			LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;
        }

        $lista=Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);
        $result= array();
        foreach($lista as $x){
            $sponsorships = RewardsSponsorshipModel::getSponsorshipAscendants($context->customer->id);
            $sponsorships2=array_slice($sponsorships, 1, 15);
            $precio = RewardsProductModel::getProductReward($x['id_product'],$x['price'],1, $context->currency->id);
            $x['points']=round(RewardsModel::getRewardReadyForDisplay($precio, $context->currency->id)/(count($sponsorships2)+1));
            $x['pointsNl']=round(RewardsModel::getRewardReadyForDisplay($precio, $context->currency->id)/16);
            array_push($result,$x);
         }
        
        //$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);

        if (!$result) {
            return array();
        }

        if ($order_by == 'orderprice') {
            Tools::orderbyPrice($result, $order_way);
        }

        /** Modify SQL result */
        return Product::getProductsProperties($id_lang, $result);
    }
    
     public static function getChildren($id_parent, $id_lang, $active = true, $id_shop = false)
    {
        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        $cache_id = 'Category::getChildren_'.(int)$id_parent.'-'.(int)$id_lang.'-'.(bool)$active.'-'.(int)$id_shop;
        if (!Cache::isStored($cache_id)) {
            $query = 'SELECT c.`id_category`, cl.`name`, cl.`link_rewrite`, category_shop.`id_shop`
			FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').')
                        INNER JOIN `'._DB_PREFIX_.'category_product` cp ON (c.`id_category` = cp.`id_category`)
			'.Shop::addSqlAssociation('category', 'c').'
			WHERE `id_lang` = '.(int)$id_lang.'
			AND c.`id_parent` = '.(int)$id_parent.'
			'.($active ? 'AND `active` = 1' : '').'
			GROUP BY c.`id_category`
			ORDER BY category_shop.`position` ASC';
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }
    
    
}
