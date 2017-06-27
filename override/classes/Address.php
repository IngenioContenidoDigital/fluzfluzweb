<?php
/**
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Address extends AddressCore
{
    /** @var string Type Document */
    public $type_document;
    
    /** @var string Check Digit number */
    public $checkdigit;
    
    public static $definition = array(
        'table' => 'address',
        'primary' => 'id_address',
        'fields' => array(
            'id_customer' =>        array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_manufacturer' =>    array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_supplier' =>        array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_warehouse' =>        array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_country' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_state' =>            array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId'),
            'alias' =>                array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
            'company' =>            array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64),
            'lastname' =>            array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
            'firstname' =>            array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
            'vat_number' =>            array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'address1' =>            array('type' => self::TYPE_STRING, 'validate' => 'isAddress', 'required' => true, 'size' => 128),
            'address2' =>            array('type' => self::TYPE_STRING, 'validate' => 'isAddress', 'size' => 128),
            'postcode' =>            array('type' => self::TYPE_STRING, 'validate' => 'isPostCode', 'size' => 12),
            'city' =>                array('type' => self::TYPE_STRING, 'validate' => 'isCityName', 'required' => true, 'size' => 64),
            'other' =>                array('type' => self::TYPE_STRING, 'validate' => 'isMessage', 'size' => 300),
            'phone' =>                array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 32),
            'phone_mobile' =>        array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 32),
            'type_document' =>      array('type' => self::TYPE_INT, 'size' => 1),
            'dni' =>                array('type' => self::TYPE_STRING, 'validate' => 'isDniLite', 'size' => 16),
            'checkdigit' =>         array('type' => self::TYPE_INT, 'validate' => 'isCheckDigit', 'size' => 1),
            'deleted' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'date_add' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );
    
    
    /**
     * Obtiene las direcciones, con el id de cliente o
     * con el id de cliente y con el id de la dirreción específica a consultar.
     * @param int $id_customer
     * @param int $id_address
     * @return boolean
     * @author faber.herrera@ingeniocontenido.co
     */
//    public static function get_list_address_app($id_customer, $id_address = NULL) {
//        
//        if ($id_customer !== '') {
//            $query = "SELECT id_address, 
//                        alias, 
//                        address1, 
//                        address2,
//                        city,
//                        phone,
//                        phone_mobile,
//                        active,
//                        default_number
//                    FROM ps_address
//                    WHERE id_customer = ".(int) $id_customer.($id_address != NULL ? (' AND address.id_address = '.(int)$id_address) : '').";";
//            //error_log("\n\nEsto es el query: ".print_r($query, true),3,"/tmp/error.log");
//            
//            if ($results = Db::getInstance()->ExecuteS($query)) {
//                if ( count($results) > 0 && !empty($results) && is_array($results)) {
//                    if(count($results) === 1 && !empty($id_address) && (int)$id_address > 0){
//                        //error_log("\n\nEsto es lo que retorna 1: ".print_r($results, true),3,"/tmp/error.log");
//                        return $results[0];
//                    }
//                    //error_log("\n\nEsto es lo que retorna 2: ".print_r($results, true),3,"/tmp/error.log");
//                    return $results;
//                }
//            }
//        }
//        return false;
//    }
    
    
    
    
    
    
    
    public static function get_list_address_app($id_customer, $id_address = NULL) {

    $val_express = Configuration::get('EXPRESS') ? Configuration::get('EXPRESS') : 0;

    if ($id_customer !== '') {

        $query = "SELECT address.id_address, address.alias, address.postcode, address.address1,address.address2, cities.city_name, 
        state.`name` AS state_name, country.`name` AS country_name,
        cac.precio_kilo, car.id_carrier, 
        SUBSTRING(REPLACE( crp.delimiter2,'.',','),1,
                  LENGTH(REPLACE( crp.delimiter2,'.',',')) -7) AS delimiter2,
cac.express_abajo AS abajo,
cac.express_arriba AS arriba,address.phone,address.phone_mobile as mobile,address.id_state, add_city.id_city 
FROM "._DB_PREFIX_."customer customer 
INNER JOIN "._DB_PREFIX_."address address ON (customer.id_customer=address.id_customer)
INNER JOIN "._DB_PREFIX_."address_city city ON (city.id_address=address.id_address)
INNER JOIN "._DB_PREFIX_."cities_col cities ON (cities.id_city=city.id_city)
INNER JOIN "._DB_PREFIX_."state state ON ( state.id_state=cities.id_state)
INNER JOIN "._DB_PREFIX_."country_lang country ON ( country.id_country=state.id_country)
INNER JOIN "._DB_PREFIX_."carrier_city cac ON (cac.id_city_des = city.id_city) 
INNER JOIN "._DB_PREFIX_."carrier car ON (car.id_reference = cac.id_carrier AND car.deleted = 0 AND car.active=1) 
INNER JOIN "._DB_PREFIX_."range_price crp ON (crp.id_carrier = car.id_carrier)
INNER JOIN "._DB_PREFIX_."address_city add_city ON(address.id_address = add_city.id_address)
WHERE customer.id_customer = ".(int) $id_customer.($id_address != NULL ? (' AND address.id_address = '.(int)$id_address) : '') ;
        
    error_log("Este es el query que trae direcciones: \n".print_r($query, true),3,"/tmp/error.log");
        
        

if ($results = Db::getInstance()->ExecuteS($query)) {

    if ( count($results) > 0 && !empty($results) && is_array($results)) {

if(count($results) === 1 && !empty($id_address) && (int)$id_address > 0)
        return $results[0];
    return $results;

    }
}           

}

return false;
}
    
    
    
    
    
    
    
    
    /**
     * Trae todas departamentos del pais.
     * @param int $id_country
     * @return boolean
     */
    static public function get_states_app($id_country) {

        $query = "select  state.id_state as `id`,state.`name` FROM
        "._DB_PREFIX_."country country
        INNER JOIN "._DB_PREFIX_."state state ON (country.id_country= state.id_country)
        WHERE country.id_country=" . (int) $id_country . ";";
        $citye_obj = new City();
        if ($results = Db::getInstance()->ExecuteS($query)) {
            $cities = $citye_obj->getPriorityCitiesWithState();
            error_log(gettype($cities));
            $results = array_merge($cities, $results);
            return $results;
        }

        return false;
    }   
    
    
    
    static public function get_cities_app($id_state) {
        //error_log("\n\nEste es el id_state: \n".print_r($id_state, true),3,"/tmp/error.log");
        $citye_obj = new City();
        $cities = $citye_obj->getCitiesByStateAvailable($id_state);
        if (count($cities) > 0 && !empty($cities) && is_array($cities) ) {
            $out_array = array();
            foreach ($cities as $key => $value) {
                $out_array[$key]['id'] = $value['id_city'];
                $out_array[$key]['name'] = $value['city_name'];
            }
            return $out_array;
        }
        return false;
    }    
    
}
?>