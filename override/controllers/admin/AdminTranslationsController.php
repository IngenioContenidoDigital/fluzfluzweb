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

class AdminTranslationsController extends AdminTranslationsControllerCore
{
    protected function displayMailBlockHtml($content, $lang, $url, $mail_name, $group_name, $name_for_module = false)
    {
        $title = array();
        $this->cleanMailContent($content, $lang, $title);
        $name_for_module = $name_for_module ? $name_for_module.'|' : '';
        
        $prueba = 'UPDATE '._DB_PREFIX_.'subject_mail SET subject_mail="'.$title['es'].'"
                   WHERE name_template_mail="'.$mail_name.'"';
        Db::getInstance()->execute($prueba);
        
        return '<div class="block-mail" >
                    <div class="mail-form">
                        <div class="form-group">
                            <label class="control-label col-lg-3">'.$this->l('Subject Mail').'</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" name="title_'.$group_name.'_'.$mail_name.'" value="'.(isset($title[$lang]) ? $title[$lang] : '').'" />
                                <p class="help-block">'.(isset($title['en']) ? $title['en'] : '').'</p>
                            </div>
                        </div>
                        <div class="thumbnail email-html-frame" data-email-src="'.$url.'"></div>
                    </div>
                </div>';
    }
}
