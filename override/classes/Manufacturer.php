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

class Manufacturer extends ManufacturerCore
{
    /** @var int Category */
    public $category;
    
    /** @var varchar Instagram */
    public $instagram;
    
    public static $definition = array(
        'table' => 'manufacturer',
        'primary' => 'id_manufacturer',
        'multilang' => true,
        'fields' => array(
            'name' =>                array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'required' => true, 'size' => 64),
            'category' =>            array('type' => self::TYPE_INT, 'size' => 5),
            'active' =>            array('type' => self::TYPE_BOOL),
            'date_add' =>            array('type' => self::TYPE_DATE),
            'date_upd' =>            array('type' => self::TYPE_DATE),

            /* Lang fields */
            'description' =>        array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'short_description' =>    array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'meta_title' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128),
            'meta_description' =>    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_keywords' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
            'instagram' =>          array('type' => self::TYPE_STRING, 'size' => 50),
        ),
    );
    
    public static function getManufacturers($get_nb_products = false, $id_lang = 0, $active = true, $p = false, $n = false, $all_group = false, $group_by = false)
    {
        if (!$id_lang) {
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }
        if (!Group::isFeatureActive()) {
            $all_group = true;
        }

        $manufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT HIGH_PRIORITY SQL_CACHE, m.*, ml.`description`, ml.`short_description`, p.price, (rp.`value`/100) as value
		FROM `'._DB_PREFIX_.'manufacturer` m
		'.Shop::addSqlAssociation('manufacturer', 'm').'
                LEFT JOIN `'._DB_PREFIX_.'product` as p ON (m.`id_manufacturer`= p.`id_manufacturer`)    
                LEFT JOIN '._DB_PREFIX_.'rewards_product rp ON (p.id_product = rp.id_product)    
		INNER JOIN `'._DB_PREFIX_.'manufacturer_lang` ml ON (m.`id_manufacturer` = ml.`id_manufacturer` AND ml.`id_lang` = '.(int)$id_lang.')
		'.($active ? 'WHERE m.`active` = 1' : '')
        .($group_by ? ' GROUP BY m.`id_manufacturer`' : '').'
		ORDER BY m.`name` ASC
		'.($p ? ' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n : ''));
        if ($manufacturers === false) {
            return false;
        }

        if ($get_nb_products) {
            $sql_groups = '';
            if (!$all_group) {
                $groups = FrontController::getCurrentCustomerGroups();
                $sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
            }

            $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
					SELECT  p.`id_manufacturer`, p.price as p, COUNT(DISTINCT p.`id_product`) as nb_products
					FROM `'._DB_PREFIX_.'product` p USE INDEX (product_manufacturer)
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'manufacturer` as m ON (m.`id_manufacturer`= p.`id_manufacturer`)
					WHERE p.`id_manufacturer` != 0 AND product_shop.`visibility` NOT IN ("none")
					'.($active ? ' AND product_shop.`active` = 1 ' : '').'
					'.(Group::isFeatureActive() && $all_group ? '' : ' AND EXISTS (
						SELECT 1
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE p.`id_product` = cp.`id_product` AND cg.`id_group` '.$sql_groups.'
					)').'
					GROUP BY p.`id_manufacturer`'
                );

            $counts = array();
            foreach ($results as $result) {
                $counts[(int)$result['id_manufacturer']] = (int)$result['nb_products'];
            }

            if (count($counts)) {
                foreach ($manufacturers as $key => $manufacturer) {
                    if (array_key_exists((int)$manufacturer['id_manufacturer'], $counts)) {
                        $manufacturers[$key]['nb_products'] = $counts[(int)$manufacturer['id_manufacturer']];
                    } else {
                        $manufacturers[$key]['nb_products'] = 0;
                    }
                }
            }
        }

        $total_manufacturers = count($manufacturers);
        $rewrite_settings = (int)Configuration::get('PS_REWRITING_SETTINGS');
        for ($i = 0; $i < $total_manufacturers; $i++) {
            $manufacturers[$i]['link_rewrite'] = ($rewrite_settings ? Tools::link_rewrite($manufacturers[$i]['name']) : 0);
        }
        return $manufacturers;
    }
    
    public static function getManufacturersCategory()
    {
        if ( isset($_COOKIE['citymanufacturerfilter']) && !empty($_COOKIE['citymanufacturerfilter']) && $_COOKIE['citymanufacturerfilter'] != "" ) {
            $cityfilter = " AND a.city = '".$_COOKIE['citymanufacturerfilter']."' ";
        }

        if ( isset($_COOKIE['manufacturerfilter']) && !empty($_COOKIE['manufacturerfilter']) && $_COOKIE['manufacturerfilter'] != "" ) {
            $cityfilter = " AND m.id_manufacturer = '".$_COOKIE['manufacturerfilter']."' ";
        }
        
        $query = 'SELECT
                    m.id_manufacturer, 
                    m.name, 
                    m.date_add, 
                    m.date_upd, 
                    p.id_product,
                    p.reference,
                    pl.link_rewrite,
                    m.category,
                    (SELECT (COUNT(p.id_product)) AS contador
                    FROM ps_product AS p
                    WHERE p.id_manufacturer = m.id_manufacturer AND p.product_parent = 1) AS count,
                    m.active,
                    (SELECT ((p.price*(rp.`value`)/100)/25) AS max_puntos
                    FROM '._DB_PREFIX_.'rewards_product AS rp 
                    INNER JOIN '._DB_PREFIX_.'product AS p ON p.id_product = rp.id_product
                    WHERE p.id_manufacturer = m.id_manufacturer
                    ORDER BY max_puntos DESC
                    LIMIT 1) AS value_no_logged,
                    (SELECT ((p.price*(rp.`value`)/100)/25) AS max_puntos
                    FROM '._DB_PREFIX_.'rewards_product AS rp 
                    INNER JOIN '._DB_PREFIX_.'product AS p ON p.id_product = rp.id_product
                    WHERE p.id_manufacturer = m.id_manufacturer
                    ORDER BY max_puntos DESC
                    LIMIT 1) AS value
                FROM '._DB_PREFIX_.'manufacturer AS m
                LEFT JOIN '._DB_PREFIX_.'address a ON ( m.id_manufacturer = a.id_manufacturer )
                LEFT JOIN '._DB_PREFIX_.'product p ON ( m.id_manufacturer = p.id_manufacturer )
                INNER JOIN '._DB_PREFIX_.'category_product cp ON (p.id_product = cp.id_product)
		INNER JOIN '._DB_PREFIX_.'category_lang cl ON (cp.id_category = cl.id_category)
                LEFT JOIN '._DB_PREFIX_.'product_lang pl ON ( pl.id_product = p.id_product )
                WHERE m.active = 1 AND p.product_parent = 1 AND cl.`name` = "Destacados"
                '.$cityfilter.'
                GROUP BY m.id_manufacturer
                HAVING count >= 1
                ORDER BY RAND()
                ';
        
        $manufacturers = Db::getInstance()->executeS($query);
        return $manufacturers;
    }
    
    public static function getManufacturersFeatured()
    {        
        $query = 'SELECT
                    m.id_manufacturer, 
                    m.name, 
                    m.date_add, 
                    m.date_upd, 
                    p.id_product,
                    pl.link_rewrite,
                    m.category,
                    (SELECT (COUNT(p.id_product)) AS contador
                    FROM ps_product AS p
                    WHERE p.id_manufacturer = m.id_manufacturer AND p.product_parent = 1) AS count,
                    m.active,
                    (SELECT ((p.price*(rp.`value`)/100)/25) AS max_puntos
                    FROM '._DB_PREFIX_.'rewards_product AS rp 
                    INNER JOIN '._DB_PREFIX_.'product AS p ON p.id_product = rp.id_product
                    WHERE p.id_manufacturer = m.id_manufacturer
                    ORDER BY max_puntos DESC
                    LIMIT 1) AS value_no_logged,
                    (SELECT ((p.price*(rp.`value`)/100)/25) AS max_puntos
                    FROM '._DB_PREFIX_.'rewards_product AS rp 
                    INNER JOIN '._DB_PREFIX_.'product AS p ON p.id_product = rp.id_product
                    WHERE p.id_manufacturer = m.id_manufacturer
                    ORDER BY max_puntos DESC
                    LIMIT 1) AS value
                FROM '._DB_PREFIX_.'manufacturer AS m
                LEFT JOIN '._DB_PREFIX_.'address a ON ( m.id_manufacturer = a.id_manufacturer )
                LEFT JOIN '._DB_PREFIX_.'product p ON ( m.id_manufacturer = p.id_manufacturer )
                INNER JOIN '._DB_PREFIX_.'category_product cp ON (p.id_product = cp.id_product)
		INNER JOIN '._DB_PREFIX_.'category_lang cl ON (cp.id_category = cl.id_category)
                LEFT JOIN '._DB_PREFIX_.'product_lang pl ON ( pl.id_product = p.id_product )
                WHERE m.active = 1 AND p.product_parent = 1 AND cl.`name` = "Destacados"
                GROUP BY m.id_manufacturer
                HAVING count >= 1
                ORDER BY RAND()
                ';
        
        $manufacturers = Db::getInstance()->executeS($query);
        return $manufacturers;
    }
    
    public static function getNewManufacturers()
    {
        if ( isset($_COOKIE['citymanufacturerfilter']) && !empty($_COOKIE['citymanufacturerfilter']) && $_COOKIE['citymanufacturerfilter'] != "" ) {
            $cityfilter = " AND a.city = '".$_COOKIE['citymanufacturerfilter']."' ";
        }

        if ( isset($_COOKIE['manufacturerfilter']) && !empty($_COOKIE['manufacturerfilter']) && $_COOKIE['manufacturerfilter'] != "" ) {
            $cityfilter = " AND m.id_manufacturer = '".$_COOKIE['manufacturerfilter']."' ";
        }

        $query = 'SELECT
                    m.id_manufacturer, 
                    m.name, 
                    m.date_add, 
                    m.date_upd, 
                    p.id_product,
                    pl.link_rewrite,
                    cl.name as name_category,
                    m.category,
                    (SELECT (COUNT(p.id_product)) AS contador
                    FROM ps_product AS p
                    WHERE p.id_manufacturer = m.id_manufacturer AND p.product_parent = 1) AS count,
                    m.active,
                    (SELECT ((p.price*(rp.`value`)/100)/25) AS max_puntos
                    FROM '._DB_PREFIX_.'rewards_product AS rp 
                    INNER JOIN '._DB_PREFIX_.'product AS p ON p.id_product = rp.id_product
                    WHERE p.id_manufacturer = m.id_manufacturer
                    ORDER BY max_puntos ASC
                    LIMIT 1) AS value_no_logged,
                    (SELECT ((p.price*(rp.`value`)/100)/25) AS max_puntos
                    FROM '._DB_PREFIX_.'rewards_product AS rp 
                    INNER JOIN '._DB_PREFIX_.'product AS p ON p.id_product = rp.id_product
                    WHERE p.id_manufacturer = m.id_manufacturer
                    ORDER BY max_puntos DESC
                    LIMIT 1) AS value
                FROM '._DB_PREFIX_.'manufacturer AS m
                LEFT JOIN '._DB_PREFIX_.'address a ON ( m.id_manufacturer = a.id_manufacturer )
                LEFT JOIN '._DB_PREFIX_.'product p ON ( m.id_manufacturer = p.id_manufacturer )
                LEFT JOIN '._DB_PREFIX_.'product_lang pl ON ( pl.id_product = p.id_product )  
                LEFT JOIN '._DB_PREFIX_.'category_lang cl ON ( cl.id_category = m.category )  
                WHERE m.active = 1 AND p.product_parent = 1
                '.$cityfilter.'
                GROUP BY m.id_manufacturer
                HAVING count >= 1
                ORDER BY m.date_add DESC LIMIT 9';
        
        $manufacturers = Db::getInstance()->executeS($query);  
        
        return $manufacturers;
    }
    
    public static function ManufacturersFilter()
    {
        return Db::getInstance()->executeS("SELECT CONCAT('m',m.id_manufacturer) id, m.name
                                            FROM "._DB_PREFIX_."manufacturer m
                                            INNER JOIN "._DB_PREFIX_."product p ON ( m.id_manufacturer = p.id_manufacturer AND p.active = 1 )
                                            WHERE m.active = 1
                                            GROUP BY m.id_manufacturer
                                            ORDER BY m.name");
    }
    
    public static function citiesManufacturerFilter()
    {
        return Db::getInstance()->executeS("SELECT DISTINCT(a.city)
                                            FROM "._DB_PREFIX_."address a
                                            INNER JOIN "._DB_PREFIX_."product p ON ( a.id_manufacturer = p.id_manufacturer AND p.active = 1 )
                                            WHERE a.id_manufacturer <> 0
                                            ORDER BY a.city");
    }
    
    public function getMediaInstagram( $count = 10 ) {
        $url = 'https://www.instagram.com/'.$this->instagram.'/?__a=1';
        $json = $this->fetchData($url);
        $data = json_decode($json, true);
        
        if( !isset($data['user']['media']['nodes']) ) {
            return array();
        }
        
        $return = array();
        $i = 0;
        
        foreach( $data['user']['media']['nodes'] as $post ) {
            $return[] = array(
                'link' => 'https://www.instagram.com/'.$this->instagram,
                'type' => $post['__typename'],
                'img-small' => $post['thumbnail_resources'][0]['src'],
                'img-medium' => $post['thumbnail_resources'][1]['src'],
                'img-large' => $post['thumbnail_resources'][4]['src'],
            );
            $i++;
            if( $i >= $count ) {
                break;
            }
        }

        return $return;
    }
    
    private function fetchData($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);    $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

?>
