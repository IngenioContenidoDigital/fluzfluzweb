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
/**
 * @since 1.5.0
 */
class HelperList extends HelperListCore
{
    public function displayResendLink($token = null, $id, $name = null)
    {
            $tpl = $this->createTemplate('list_action_resend.tpl');
            
            if (!array_key_exists('Resend', self::$cache_lang)) {
                self::$cache_lang['Resend'] = $this->l('Reenviar Email', 'Helper');
            }
            
            $tpl->assign(array(
                            'href' => $this->currentIndex.'&'.$this->identifier.'='.$id.'&resend'.$this->table.'&token='.($token != null ? $token : $this->token),
                            'id' => $id,
                            'action' => self::$cache_lang['Resend'],
                             $this->identifier => $id,
                            'controller' => str_replace('Controller', '', get_class($this->context->controller)),
                ));
           
            return $tpl->fetch();
    }
}