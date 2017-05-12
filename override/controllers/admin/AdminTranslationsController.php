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
    
    public function postProcess()
    {
        $this->getInformations();

        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = Tools::displayError('This functionality has been disabled.');
            return;
        }
        /* PrestaShop demo mode */

        try {
            if (Tools::isSubmit('submitCopyLang')) {
                if ($this->tabAccess['add'] === '1') {
                    $this->submitCopyLang();
                } else {
                    $this->errors[] = Tools::displayError('You do not have permission to add this.');
                }
            } elseif (Tools::isSubmit('submitExport')) {
                if ($this->tabAccess['add'] === '1') {
                    $this->submitExportLang();
                } else {
                    $this->errors[] = Tools::displayError('You do not have permission to add this.');
                }
            } elseif (Tools::isSubmit('submitImport')) {
                if ($this->tabAccess['add'] === '1') {
                    $this->submitImportLang();
                } else {
                    $this->errors[] = Tools::displayError('You do not have permission to add this.');
                }
            } elseif (Tools::isSubmit('submitAddLanguage')) {
                if ($this->tabAccess['add'] === '1') {
                    $this->submitAddLang();
                } else {
                    $this->errors[] = Tools::displayError('You do not have permission to add this.');
                }
            } elseif (Tools::isSubmit('submitTranslationsPdf')) {
                if ($this->tabAccess['edit'] === '1') {
                    // Only the PrestaShop team should write the translations into the _PS_TRANSLATIONS_DIR_
                    if (!$this->theme_selected) {
                        $this->writeTranslationFile();
                    } else {
                        $this->writeTranslationFile(true);
                    }
                } else {
                    $this->errors[] = Tools::displayError('You do not have permission to edit this.');
                }
            } elseif (Tools::isSubmit('submitTranslationsBack') || Tools::isSubmit('submitTranslationsErrors') || Tools::isSubmit('submitTranslationsFields') || Tools::isSubmit('submitTranslationsFront')) {
                if ($this->tabAccess['edit'] === '1') {
                    $this->writeTranslationFile();
                } else {
                    $this->errors[] = Tools::displayError('You do not have permission to edit this.');
                }
            } 
            elseif (Tools::isSubmit('submitTranslationsTest')) {
                
                $email = Tools::getValue('testEmail_trasnlations');
                $mailVars = array(
                            '{order_link}' => Context::getContext()->link->getPageLink('order', false, (int)$cart_normal->id_lang, 'step=3&recover_cart='.(int)$cart_normal->id.'&token_cart='.md5(_COOKIE_KEY_.'recover_cart_'.(int)$cart_normal->id)),
                            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                            '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                        );
                //$template = Tools::getValue($key);
                $template = 'backoffice_order';
                $prefix_template = '16-'.''.$template.'';

                $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'subject_mail WHERE name_template_mail ="'.$prefix_template.'"';
                $row_subject = Db::getInstance()->getRow($query_subject);
                $message_subject = $row_subject['subject_mail'];

                $allinone_rewards = new allinone_rewards();
                $allinone_rewards->sendMail(1, $template, $allinone_rewards->getL($message_subject), $mailVars, $email);
            }
            elseif (Tools::isSubmit('submitTranslationsMails') || Tools::isSubmit('submitTranslationsMailsAndStay')) {
                if ($this->tabAccess['edit'] === '1') {
                    $this->submitTranslationsMails();
                } else {
                    $this->errors[] = Tools::displayError('You do not have permission to edit this.');
                }
            } elseif (Tools::isSubmit('submitTranslationsModules')) {
                if ($this->tabAccess['edit'] === '1') {
                    // Get list of modules
                    if ($modules = $this->getListModules()) {
                        // Get files of all modules
                        $arr_files = $this->getAllModuleFiles($modules, null, $this->lang_selected->iso_code, true);

                        // Find and write all translation modules files
                        foreach ($arr_files as $value) {
                            $this->findAndWriteTranslationsIntoFile($value['file_name'], $value['files'], $value['theme'], $value['module'], $value['dir']);
                        }

                        // Clear modules cache
                        Tools::clearCache();

                        // Redirect
                        if (Tools::getIsset('submitTranslationsModulesAndStay')) {
                            $this->redirect(true);
                        } else {
                            $this->redirect();
                        }
                    }
                } else {
                    $this->errors[] = Tools::displayError('You do not have permission to edit this.');
                }
            }
        } catch (PrestaShopException $e) {
            $this->errors[] = $e->getMessage();
        }
    }
}
