<?php
/*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class BlockCategoriesOverride extends BlockCategories
{
    public function getTree($resultParents, $resultIds, $maxDepth, $id_category = null, $currentDepth = 0)
	{
		if (is_null($id_category))
			$id_category = $this->context->shop->getCategory();
		$children = array();
		if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth))
			foreach ($resultParents[$id_category] as $subcat)
				$children[] = $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1);
		if (isset($resultIds[$id_category])) 
		{
			$link = $this->context->link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']);
			$name = $resultIds[$id_category]['name'];
			$desc = $resultIds[$id_category]['description'];
		}
		else
			$link = $name = $desc = '';
			
		$return = array(
			'id' => $id_category,
			'link' => $link,
			'name' => $name,
			'desc'=> $desc,
			'children' => $children
		);
		return $return;
	}
        
        public function hookLeftColumn($params)
	{
		$this->setLastVisitedCategory();
		$phpself = $this->context->controller->php_self;
		$current_allowed_controllers = array('category');

		if ($phpself != null && in_array($phpself, $current_allowed_controllers) && Configuration::get('BLOCK_CATEG_ROOT_CATEGORY') && isset($this->context->cookie->last_visited_category) && $this->context->cookie->last_visited_category)
		{
			$category = new Category($this->context->cookie->last_visited_category, $this->context->language->id);
			if (Configuration::get('BLOCK_CATEG_ROOT_CATEGORY') == 2 && !$category->is_root_category && $category->id_parent)
				$category = new Category($category->id_parent, $this->context->language->id);
			elseif (Configuration::get('BLOCK_CATEG_ROOT_CATEGORY') == 3 && !$category->is_root_category && !$category->getSubCategories($category->id, true))
				$category = new Category($category->id_parent, $this->context->language->id);
		}
		else
			$category = new Category((int)Configuration::get('PS_HOME_CATEGORY'), $this->context->language->id);

		$cacheId = $this->getCacheId($category ? $category->id : null);

		if (!$this->isCached('blockcategories.tpl', $cacheId))
		{
			$range = '';
			$maxdepth = Configuration::get('BLOCK_CATEG_MAX_DEPTH');
			if (Validate::isLoadedObject($category))
			{
				if ($maxdepth > 0)
					$maxdepth += $category->level_depth;
				$range = 'AND nleft >= '.(int)$category->nleft.' AND nright <= '.(int)$category->nright;
			}

			$resultIds = array();
			$resultParents = array();
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite
			FROM `'._DB_PREFIX_.'category` c
			INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl').')
			INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = '.(int)$this->context->shop->id.')
			WHERE (c.`active` = 1 OR c.`id_category` = '.(int)Configuration::get('PS_HOME_CATEGORY').')
			AND c.`id_category` != '.(int)Configuration::get('PS_ROOT_CATEGORY').'
			'.((int)$maxdepth != 0 ? ' AND `level_depth` <= '.(int)$maxdepth : '').'
			'.$range.'
			AND c.id_category IN (
				SELECT id_category
				FROM `'._DB_PREFIX_.'category_group`
				WHERE `id_group` IN ('.pSQL(implode(', ', Customer::getGroupsStatic((int)$this->context->customer->id))).')
			)
			ORDER BY `level_depth` ASC, '.(Configuration::get('BLOCK_CATEG_SORT') ? 'cl.`name`' : 'cs.`position`').' '.(Configuration::get('BLOCK_CATEG_SORT_WAY') ? 'DESC' : 'ASC'));
			foreach ($result as &$row)
			{
				$resultParents[$row['id_parent']][] = &$row;
				$resultIds[$row['id_category']] = &$row;
			}

			$blockCategTree = $this->getTree($resultParents, $resultIds, $maxdepth, ($category ? $category->id : null));
                        $this->smarty->assign('blockCategTree', $blockCategTree);

                        $categoryInitial = Category::getRootCategories();
                        $categories = Category::getChildren($categoryInitial[0]['id_category'], 1);
                        foreach ( $categories as $key => &$categoryy ) {
                            $categoryy['link'] = $this->context->link->getCategoryLink($categoryy['id_category'], $categoryy['link_rewrite']);
                            $categoryy['father'] = "true";
                            $categoryy['children'] = Category::getChildren($categoryy['id_category'], 1);
                            foreach ( $categoryy['children'] as &$categ ) {
                                $categ['link'] = $this->context->link->getCategoryLink($categ['id_category'], $categ['link_rewrite']);
                                $categ['father'] = "false";
                                $categ['children'] = array();
                            }
                        }
                        $this->smarty->assign('blockTreeCategories', $categories);

			if ((Tools::getValue('id_product') || Tools::getValue('id_category')) && isset($this->context->cookie->last_visited_category) && $this->context->cookie->last_visited_category)
			{
				$category = new Category($this->context->cookie->last_visited_category, $this->context->language->id);
				if (Validate::isLoadedObject($category))
					$this->smarty->assign(array('currentCategory' => $category, 'currentCategoryId' => $category->id));
			}

			$this->smarty->assign('isDhtml', Configuration::get('BLOCK_CATEG_DHTML'));
			if (file_exists(_PS_THEME_DIR_.'modules/blockcategories/blockcategories.tpl'))
				$this->smarty->assign('branche_tpl_path', _PS_THEME_DIR_.'modules/blockcategories/category-tree-branch.tpl');
			else
				$this->smarty->assign('branche_tpl_path', _PS_MODULE_DIR_.'blockcategories/category-tree-branch.tpl');
		}

                $manufacturersFilter = $this->orderForLetter(Manufacturer::ManufacturersFilter() , 'name' );
                $this->smarty->assign('manufacturers_filter', $manufacturersFilter);

                $citiesManufacturerFilter = $this->orderForLetter( Manufacturer::citiesManufacturerFilter() , 'city' );
                $this->smarty->assign('cities_manufacturer_filter', $citiesManufacturerFilter);
                
		return $this->display(__FILE__, 'blockcategories.tpl', $cacheId);
	}
        
        public function orderForLetter($list, $dataOrder) {
        $orderedList = [];
        foreach ( $list as $option ) {
            $firstLetter = strtolower(substr($option[$dataOrder], 0, 1));
            if ( $firstLetter == "a" || $firstLetter == "b" || $firstLetter == "c" || $firstLetter == "d" || $firstLetter == "e" || $firstLetter == "f" || $firstLetter == "g" ) {
                $orderedList['A-G'][] = $option;
            }
            if ( $firstLetter == "h" || $firstLetter == "i" || $firstLetter == "j" || $firstLetter == "k" || $firstLetter == "l" || $firstLetter == "m" || $firstLetter == "n" ) {
                $orderedList["H-N"][] = $option;
            }
            if ( $firstLetter == "o" || $firstLetter == "p" || $firstLetter == "q" || $firstLetter == "r" || $firstLetter == "s" || $firstLetter == "t" ) {
                $orderedList["O-T"][] = $option;
            }
            if ( $firstLetter == "u" || $firstLetter == "v" || $firstLetter == "w" || $firstLetter == "x" || $firstLetter == "y" || $firstLetter == "z" ) {
                $orderedList["U-Z"][] = $option;
            }
        }
        
        return $orderedList;
    }
}

?>