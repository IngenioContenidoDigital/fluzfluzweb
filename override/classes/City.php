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


class CityCore extends ObjectModel
{
    public $id;
    public $codigo_departamento;
    public $departamento;
    public $codigo_ciudad;
    public $ciudad;

    public static $definition = array(
        'table' => 'cities',
        'primary' => 'id_cities',
        'fields' => array(
            'codigo_departamento' =>    array('type' => self::TYPE_INT),
            'departamento' =>           array('type' => self::TYPE_STRING),
            'codigo_ciudad' =>          array('type' => self::TYPE_STRING),
            'ciudad' =>                 array('type' => self::TYPE_STRING),
        ),
    );

    public static function getCities()
    {
        $countries = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("SELECT ciudad
                                                                    FROM "._DB_PREFIX_."cities
                                                                    ORDER BY ciudad");
        return $countries;
    }
    
    /**
     * Toma el id del pais y retorna las ciudades de ese pais.
     * @param int $id_country
     * @return array     Contiene todas las ciudades del pais.
     */
    public static function getCitiesByStateAvailable($id_state)
    {
        $q_city_unique='
        SELECT cit.id_city, cit.city_name
        FROM `'._DB_PREFIX_.'cities_col` cit
        INNER JOIN `'._DB_PREFIX_.'carrier_city` car
        ON (car.id_city_des = cit.id_city)
        INNER JOIN ps_state s  ON ( s.id_state = cit.id_state AND s.id_country = '.(int)Configuration::get('PS_COUNTRY_DEFAULT').' )
        WHERE cit.id_state = '. $id_state .'
        GROUP BY cit.id_city, cit.city_name
        ORDER BY cit.city_name ASC';
        
        //error_log("\n\nEste es el query: \n".print_r($q_city_unique, true),3,"/tmp/error.log");
        
        return Db::getInstance()->executeS($q_city_unique);
    }
    
    
    
    
    public static function getPriorityCitiesWithState()
	{
		$q_city_unique='
		SELECT s.id_state as id, CONCAT(cc.ciudad, " - ", s.name) as name, cit.id_city as id_city
		FROM `'._DB_PREFIX_.'priority_city` cit
		INNER JOIN `'._DB_PREFIX_.'cities` cc ON (cc.id_cities = cit.id_city)
		INNER JOIN `'._DB_PREFIX_.'state` s ON (s.id_state = cc.id_state)
		WHERE cit.in_app = 1
		ORDER BY cit.order ASC';
                
		$result = Db::getInstance()->executeS($q_city_unique);
                error_log(gettype($result));
		return $result;
	}
}
