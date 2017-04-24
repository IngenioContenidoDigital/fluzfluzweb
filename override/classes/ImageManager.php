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

/**
 * This class includes functions for image manipulation
 *
 * @since 1.5.0
 */
class ImageManager extends ImageManagerCore
{
 public static function write($type, $resource, $filename)
    {
        static $ps_png_quality = null;
        static $ps_jpeg_quality = null;

        if ($ps_png_quality === null) {
            $ps_png_quality = Configuration::get('PS_PNG_QUALITY');
        }

        if ($ps_jpeg_quality === null) {
            $ps_jpeg_quality = Configuration::get('PS_JPEG_QUALITY');
        }

        switch ($type) {
            case 'gif':
                $success = imagegif($resource, $filename);
            break;

            case 'png':
                $quality = ($ps_png_quality === false ? 7 : $ps_png_quality);
                $success = imagepng($resource, $filename, (int)$quality);
            break;

            case 'jpg':
            case 'jpeg':
            default:
                $quality = ($ps_jpeg_quality === false ? 90 : $ps_jpeg_quality);
                imageinterlace($resource, 1); /// make it PROGRESSIVE
                $success = imagejpeg($resource, $filename, (int)$quality);
            break;
        }        
        imagedestroy($resource);
        @chmod($filename, 0664);
        
        // Sube las imágenes al AWS S3
        $awsObj = new Aws();
        
        if ($filename) {
                $oriPath = str_replace(_PS_IMG_DIR_, "", $filename);
                preg_match('/^([a-zA-Z]+).*(\/[_a-zA-Z0-9-]+\.jpg)$/i', $oriPath, $matches);
                array_shift($matches);
                $objAws = implode('', $matches);
                //error_log("objAws: "."$objAws"."\n",3,"/var/log/php_errors.log");
                if (!($success && $objAws && $awsObj->setObjectImage($filename, $objAws))) {
                    $success = false;
                }
        }
        return $success;
    }   
}
?>