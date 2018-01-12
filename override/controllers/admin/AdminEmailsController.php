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
 * @property Mail $object
 */
class AdminEmailsController extends AdminEmailsControllerCore
{
    public function __construct()
    {
        $this->bootstrap = true;
        if (Configuration::get('PS_LOG_EMAILS')) {
            $this->table = 'mail';
            $this->className = 'Mail';
            $this->lang = false;
            $this->noLink = true;
            $this->list_no_link = true;
            $this->explicitSelect = true;
            $this->addRowAction('delete');
            $this->addRowAction('resend');
            
            /*$this->bulk_actions = array(
                'delete' => array(
                    'text' => $this->l('Delete selected'),
                    'confirm' => $this->l('Delete selected items?'),
                    'icon' => 'icon-trash'
                )
            );*/
            foreach (Language::getLanguages() as $language) {
                $languages[$language['id_lang']] = $language['name'];
            }
            $this->fields_list = array(
                'id_mail' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
                'recipient' => array('title' => $this->l('Recipient')),
                'template' => array('title' => $this->l('Template')),
                'language' => array(
                    'title' => $this->l('Language'),
                    'type' => 'select',
                    'color' => 'color',
                    'list' => $languages,
                    'filter_key' => 'a!id_lang',
                    'filter_type' => 'int',
                    'order_key' => 'language'
                ),
                'subject' => array('title' => $this->l('Subject')),
                'date_add' => array(
                    'title' => $this->l('Sent'),
                    'type' => 'datetime',
                )
            );
            $this->_select .= 'l.name as language';
            $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'lang l ON (a.id_lang = l.id_lang)';
            $this->_use_found_rows = false;
        }
        AdminController::__construct();
        foreach (Contact::getContacts($this->context->language->id) as $contact) {
            $arr[] = array('email_message' => $contact['id_contact'], 'name' => $contact['name']);
        }
        $this->fields_options = array(
            'email' => array(
                'title' => $this->l('Email'),
                'icon' => 'icon-envelope',
                'fields' =>    array(
                    'PS_MAIL_EMAIL_MESSAGE' => array(
                        'title' => $this->l('Send email to'),
                        'desc' => $this->l('Where customers send messages from the order page.'),
                        'validation' => 'isUnsignedId',
                        'type' => 'select',
                        'cast' => 'intval',
                        'identifier' => 'email_message',
                        'list' => $arr
                    ),
                    'PS_MAIL_METHOD' => array(
                        'title' => '',
                        'validation' => 'isGenericName',
                        'type' => 'radio',
                        'required' => true,
                        'choices' => array(
                            3 => $this->l('Never send emails (may be useful for testing purposes)'),
                            2 => $this->l('Set my own SMTP parameters (for advanced users ONLY)')
                        )
                    ),
                    'PS_MAIL_TYPE' => array(
                        'title' => '',
                        'validation' => 'isGenericName',
                        'type' => 'radio',
                        'required' => true,
                        'choices' => array(
                            Mail::TYPE_HTML => $this->l('Send email in HTML format'),
                            Mail::TYPE_TEXT => $this->l('Send email in text format'),
                            Mail::TYPE_BOTH => $this->l('Both')
                        )
                    ),
                    'PS_LOG_EMAILS' => array(
                        'title' => $this->l('Log Emails'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
            'smtp' => array(
                'title' => $this->l('Email'),
                'fields' =>    array(
                    'PS_MAIL_DOMAIN' => array(
                        'title' => $this->l('Mail domain name'),
                        'hint' => $this->l('Fully qualified domain name (keep this field empty if you don\'t know).'),
                        'empty' => true, 'validation' =>
                        'isUrl',
                        'type' => 'text',
                    ),
                    'PS_MAIL_SERVER' => array(
                        'title' => $this->l('SMTP server'),
                        'hint' => $this->l('IP address or server name (e.g. smtp.mydomain.com).'),
                        'validation' => 'isGenericName',
                        'type' => 'text',
                    ),
                    'PS_MAIL_USER' => array(
                        'title' => $this->l('SMTP username'),
                        'hint' => $this->l('Leave blank if not applicable.'),
                        'validation' => 'isGenericName',
                        'type' => 'text',
                    ),
                    'PS_MAIL_PASSWD' => array(
                        'title' => $this->l('SMTP password'),
                        'hint' => $this->l('Leave blank if not applicable.'),
                        'validation' => 'isAnything',
                        'type' => 'password',
                        'autocomplete' => false
                    ),
                    'PS_MAIL_SMTP_ENCRYPTION' => array(
                        'title' => $this->l('Encryption'),
                        'hint' => $this->l('Use an encrypt protocol'),
                        'desc' => extension_loaded('openssl') ? '' : '/!\\ '.$this->l('SSL does not seem to be available on your server.'),
                        'type' => 'select',
                        'cast' => 'strval',
                        'identifier' => 'mode',
                        'list' => array(
                            array(
                                'mode' => 'off',
                                'name' => $this->l('None')
                            ),
                            array(
                                'mode' => 'tls',
                                'name' => $this->l('TLS')
                            ),
                            array(
                                'mode' => 'ssl',
                                'name' => $this->l('SSL')
                            )
                        ),
                    ),
                    'PS_MAIL_SMTP_PORT' => array(
                        'title' => $this->l('Port'),
                        'hint' => $this->l('Port number to use.'),
                        'validation' => 'isInt',
                        'type' => 'text',
                        'cast' => 'intval',
                        'class' => 'fixed-width-sm'
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
            'test' => array(
                'title' =>    $this->l('Test your email configuration'),
                'hide_multishop_checkbox' => true,
                'fields' =>    array(
                    'PS_SHOP_EMAIL' => array(
                        'title' => $this->l('Send a test email to'),
                        'type' => 'text',
                        'id' => 'testEmail',
                        'no_multishop_checkbox' => true
                    ),
                ),
                'bottom' => '<div class="row"><div class="col-lg-9 col-lg-offset-3">
					<div class="alert" id="mailResultCheck" style="display:none;"></div>
				</div></div>',
                'buttons' => array(
                    array('title' => $this->l('Send a test email'),
                        'icon' => 'process-icon-envelope',
                        'name' => 'btEmailTest',
                        'js' => 'verifyMail()',
                        'class' => 'btn btn-default pull-right'
                    )
                )
            )
        );
        if (!defined('_PS_HOST_MODE_')) {
            $this->fields_options['email']['fields']['PS_MAIL_METHOD']['choices'][1] =
                $this->l('Use PHP\'s mail() function (recommended; works in most cases)');
        }
        ksort($this->fields_options['email']['fields']['PS_MAIL_METHOD']['choices']);
    }
}