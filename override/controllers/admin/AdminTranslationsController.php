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
include_once(_PS_MODULE_DIR_.'/allinone_rewards/allinone_rewards.php');

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
                
                $template = Tools::getValue('mail_name');
                $email = Tools::getValue('testEmail_trasnlations');
                
                $mailVars = array(
                            '{order_link}' => Context::getContext()->link->getPageLink('order', false, Context::getContext()->language->id, 'step=3&recover_cart='.(int)$cart_normal->id.'&token_cart='.md5(_COOKIE_KEY_.'recover_cart_'.(int)$cart_normal->id)),
                            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                            '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                        );
                
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
    
    protected function displayMailContent($mails, $all_subject_mail, $obj_lang, $id_html, $title, $name_for_module = false)
    {
        $str_return = '';
        $group_name = 'mail';
        if (array_key_exists('group_name', $mails)) {
            $group_name = $mails['group_name'];
        }

        if ($mails['empty_values'] == 0) {
            $translation_missing_badge_type = 'badge-success';
        } else {
            $translation_missing_badge_type = 'badge-danger';
        }
        $str_return .= '<div class="mails_field">
            <h4>
            <span class="badge">'.((int)$mails['empty_values'] + (int)$mails['total_filled']).' <i class="icon-envelope-o"></i></span>
            <a href="javascript:void(0);" onclick="$(\'#'.$id_html.'\').slideToggle();">'.$title.'</a>
            <span class="pull-right badge '.$translation_missing_badge_type.'">'.$mails['empty_values'].' '.$this->l('missing translation(s)').'</span>
            </h4>
            <div name="mails_div" id="'.$id_html.'" class="panel-group">';

        if (!empty($mails['files'])) {
            $topic_already_displayed = array();
            foreach ($mails['files'] as $mail_name => $mail_files) {
                $str_return .= '<div class="panel translations-email-panel">';
                $str_return .= '<a href="#'.$id_html.'-'.$mail_name.'" class="panel-title" data-toggle="collapse" data-parent="#'.$id_html.'" >'.$mail_name.' <i class="icon-caret-down"></i> </a>';
                
                $query_status = 'SELECT status_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail = '."'$mail_name'";
                $row_m = Db::getInstance()->getRow($query_status);
                $status_m = $row_m['status_mail'];
                
                $str_return .= '<div class="col-lg-6" style="float:right;">
                                    <label for="status_enable">';
                if ($status_m==0){
                   $str_return .='<input type="radio" name="'.$mail_name.'" class="habilitar" id="status_enable" checked="checked" value="0">';
                }else{
                   $str_return .='<input type="radio" name="'.$mail_name.'" class="habilitar" id="status_enable" value="0">'; 
                }
                $str_return .='Habilitado
                                    </label> &nbsp;&nbsp;
                                    <label for="status_disable">';
                if ($status_m==1){
                    $str_return .='<input type="radio" name="'.$mail_name.'" class="deshabilitar" id="status_disable" checked="checked"value="1">';
                }else{
                    $str_return .='<input type="radio" name="'.$mail_name.'" class="deshabilitar" id="status_disable" value="1">';
                }
                $str_return .='Deshabilitar
                                    </label>
                                </div>';
                if($status_m==0){
                    $str_return .= '<div id="'.$id_html.'-'.$mail_name.'" class="email-collapse panel-collapse collapse">';
                }
                else{
                    $str_return .= '<div id="'.$id_html.'-'.$mail_name.'" class="email-collapse panel-collapse collapse" style="display:none;">';
                }
                if (array_key_exists('html', $mail_files) || array_key_exists('txt', $mail_files)) {
                    if (array_key_exists($mail_name, $all_subject_mail)) {
                        foreach ($all_subject_mail[$mail_name] as $subject_mail) {
                            $subject_key = 'subject['.Tools::htmlentitiesUTF8($group_name).']['.Tools::htmlentitiesUTF8($subject_mail).']';
                            if (in_array($subject_key, $topic_already_displayed)) {
                                continue;
                            }
                            $topic_already_displayed[] = $subject_key;
                            $value_subject_mail = isset($mails['subject'][$subject_mail]) ? $mails['subject'][$subject_mail] : '';
                            $str_return .= '
                            <div class="label-subject row">
                                <label class="control-label col-lg-3">'.sprintf($this->l('Email subject'));
                            if (isset($value_subject_mail['use_sprintf']) && $value_subject_mail['use_sprintf']) {
                                $str_return .= '<span class="useSpecialSyntax" title="'.$this->l('This expression uses a special syntax:').' '.$value_subject_mail['use_sprintf'].'">
                                    <i class="icon-exclamation-triangle"></i>
                                </span>';
                            }
                            $str_return .= '</label><div class="col-lg-9">';
                            if (isset($value_subject_mail['trad']) && $value_subject_mail['trad']) {
                                $str_return .= '<input class="form-control" type="text" name="subject['.Tools::htmlentitiesUTF8($group_name).']['.Tools::htmlentitiesUTF8($subject_mail).']" value="'.$value_subject_mail['trad'].'" />';
                            } else {
                                $str_return .= '<input class="form-control" type="text" name="subject['.Tools::htmlentitiesUTF8($group_name).']['.Tools::htmlentitiesUTF8($subject_mail).']" value="" />';
                            }
                            $str_return .= '<p class="help-block">'.stripcslashes($subject_mail).'</p>';
                            $str_return .= '</div></div>';
                        }
                    } else {
                        $str_return .= '
                            <hr><div class="alert alert-info">'
                            .sprintf($this->l('No Subject was found for %s in the database.'), $mail_name)
                            .'</div>';
                    }
                    // tab menu
                    $str_return .= '<hr><ul class="nav nav-pills">
                        <li class="active"><a href="#'.$mail_name.'-html" data-toggle="tab">'.$this->l('View HTML version').'</a></li>
                        <li><a href="#'.$mail_name.'-editor" data-toggle="tab">'.$this->l('Edit HTML version').'</a></li>
                        <li><a href="#'.$mail_name.'-text" data-toggle="tab">'.$this->l('View/Edit TXT version').'</a></li>
                        </ul>';
                    // tab-content
                    $str_return .= '<div class="tab-content">';

                    if (array_key_exists('html', $mail_files)) {
                        $str_return .= '<div class="tab-pane active" id="'.$mail_name.'-html">';
                        $base_uri = str_replace(_PS_ROOT_DIR_, __PS_BASE_URI__, $mails['directory']);
                        $base_uri = str_replace('//', '/', $base_uri);
                        $url_mail = $base_uri.$mail_name.'.html';
                        $str_return .= $this->displayMailBlockHtml($mail_files['html'], $obj_lang->iso_code, $url_mail, $mail_name, $group_name, $name_for_module);
                        $str_return .= '</div>';
                    }

                    if (array_key_exists('txt', $mail_files)) {
                        $str_return .= '<div class="tab-pane" id="'.$mail_name.'-text">';
                        $str_return .= $this->displayMailBlockTxt($mail_files['txt'], $obj_lang->iso_code, $mail_name, $group_name, $name_for_module);
                        $str_return .= '</div>';
                    }

                    $str_return .= '<div class="tab-pane" id="'.$mail_name.'-editor">';
                    if (isset($mail_files['html'])) {
                        $str_return .= $this->displayMailEditor($mail_files['html'], $obj_lang->iso_code, $url_mail, $mail_name, $group_name, $name_for_module);
                    }
                    $str_return .= '</div>';

                    $str_return .= '</div>';
                    $str_return .= '</div><!--end .panel-collapse -->';
                    $str_return .= '</div><!--end .panel -->';
                }
            }
            $str_return .= '<script>
                                $(".habilitar").click(function(e) {
                                    var value_status = $("#status_enable").val();
                                    var email = $(this).attr("name");
                                    var status_name = "Habilitado";
                                        $.ajax({
                                        method:"POST",
                                        data: {"value_status": value_status,"email": email,"status_name": status_name},
                                        url: "/disableMail.php",
                                        success: function(result){
                                            
                                          $("#allinone_rewards-"+email).addClass("in");
                                          $("#allinone_rewards-"+email).show();
                                        }
                                    });
                                    e.preventdefault();
                                });
                                $(".deshabilitar").click(function(e) {
                                    var value_status = $("#status_disable").val();
                                    var email = $(this).attr("name");
                                    var status_name = "Deshabilitado";
                                    $.ajax({
                                        method:"POST",
                                        data: {"value_status": value_status,"email": email,"status_name": status_name},
                                        url: "/disableMail.php",
                                        success: function(result){
                                            $("#allinone_rewards-"+email).removeClass("in");
                                            $("#allinone_rewards-"+email).hide();
                                        }
                                    });
                                    e.preventdefault();
                                });
                          </script>';
        } else {
            $str_return .= '<p class="error">
                '.$this->l('There was a problem getting the mail files.').'<br>
                '.sprintf($this->l('English language files must exist in %s folder'), '<em>'.preg_replace('@/[a-z]{2}(/?)$@', '/en$1', $mails['directory']).'</em>').'
            </p>';
        }

        $str_return .= '</div><!-- #'.$id_html.' --></div><!-- end .mails_field -->';
        return $str_return;
    }
}
