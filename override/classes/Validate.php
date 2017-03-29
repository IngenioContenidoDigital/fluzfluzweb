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

class Validate extends ValidateCore
{
    public static function isName($name)
    {
        return preg_match(Tools::cleanNonUnicodeSupport('/^[^0-9!<>,;?=+()@#"°{}_$%:]*$/u'), stripslashes($name));
    }
    
    public static function isDiscountName($voucher)
    {
        return preg_match(Tools::cleanNonUnicodeSupport('/^[^!<>,;?=+()@"°{}_$%:]{3,32}$/u'), $voucher);
    }
    
    public static function isCityName($city)
    {
        return preg_match(Tools::cleanNonUnicodeSupport('/^[^!<>;?=+@#"°{}_$%]*$/u'), $city);
    }
    
    public static function isCleanHtml($html, $allow_iframe = false){
    	return true;
    }
    
    public static function isPhoneTelcoNumber($number)
    {
        return preg_match('/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/', $number);
    }
    
    public static function isTagsList($list)
    {
        return preg_match(Tools::cleanNonUnicodeSupport('/^[^!<>;?=+#"°{}_$%]*$/u'), $list);
    }
    
    public static function isCheckDigit($checkdigit)
    {
        return empty($checkdigit) || (bool)preg_match('/^[0-9]{1,1}$/i', $checkdigit);
    }
    
    public static function isDniLite($dni)
    {
        return empty($dni) || (bool)preg_match('/^[0-9A-Za-z-.]{1,16}$/i', $dni);
    }
    
    public static function isIdentification($id)
    {
	$opciones = array('01234','12345','23456','34567','45678','56789','67890','78901','89012','90123',
                            '43210','54321','65432','76543','87654','98765','09876','10987','21098','32109');
	$cant = 0;

	foreach ($opciones as $key => $value) {
            preg_match_all('/'.$value.'/', $id, $matches, PREG_PATTERN_ORDER);
            $cant += count($matches[0]);
	}
        
	if ( !empty($id) && substr($id, 0, 1) != 0 && strlen($id) >= 5 && $cant <= 1 ) {
            if ( preg_match('/^[0-9]{5,}-{1}[0-9]{1}$/', $id) || ($id > 9999 && $id < 100000000) || ($id > 1000000000 && $id < 4099999999) ) {
                return false;
            } else {
                return true;
            }			
	} else {
            return true;	
        }
    }
    
    public static function isIdentificationCE($id)
    {
        if(strlen($id)<6) {
		return "DNI demasiado corto.";
	}
 
	$id = strtoupper($id);
 
	$letra = substr($id, -1, 1);
	$numero = substr($id, 0, 8);
 
	// Si es un NIE hay que cambiar la primera letra por 0, 1 � 2 dependiendo de si es X, Y o Z.
	$numero = str_replace(array('X', 'Y', 'Z'), array(0, 1, 2), $numero);	
 
	$modulo = $numero % 23;
	$letras_validas = "TRWAGMYFPDXBNJZSQVHLCKE";
	$letra_correcta = substr($letras_validas, $modulo, 1);
        
        if($letra_correcta!=$letra) {
		return false;
	}
	else {
		return true;
	}
    }
}

?>