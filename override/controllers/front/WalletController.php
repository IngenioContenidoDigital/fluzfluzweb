<?php

include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once(_PS_MODULE_DIR_ . 'allinone_rewards/allinone_rewards.php');
include_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsModel.php');

class WalletController extends FrontController {
    public function initContent() {
        parent::initContent();

        $id_customer = $this->context->customer->id;
        $this->context->smarty->assign('id_customer',$id_customer);
        
        $smarty_values = array(
            's3'=>_S3_PATH_,
            'cards' => Wallet::getCards($this->context->customer->id, Tools::getValue("manufacturer")),
            'addreses_manufacturer' => Wallet::getManufacturerAddress(Tools::getValue("manufacturer")),
            'gift_cards' => Wallet::getCardsGift($this->context->customer->id, Tools::getValue("manufacturer")),
        );

        $this->context->smarty->assign($smarty_values);
        $this->setTemplate(_PS_THEME_DIR_.'wallet.tpl');
    }
    
    public function setMedia() {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'wallet.css');
        $this->addJS(_THEME_JS_DIR_.'wallet.js');
    }
    
    public function postProcess() {
        switch ( Tools::getValue('action') ) {
            case 'mark-used':
                if ( Tools::getValue('card') && Tools::getValue('used') ) {
                    $setUsed = Wallet::setUsedCard(Tools::getValue('card'),Tools::getValue('used'));
                    $response = array('success' => $setUsed);
                    die( Tools::jsonEncode($response) );
                }
                break;
            case 'set-value-used':
                if ( Tools::getValue('card') ) {
                    $setValue = Wallet::setValueUsed(Tools::getValue('card'),Tools::getValue('value'));
                    $response = array('success' => $setValue);
                    die( Tools::jsonEncode($response) );
                }
                break;
            case 'send_gift_card':
                    $id_customer = Tools::getValue('id_customer');
                    $id_customer_receive = Tools::getValue('id_customer_receive');
                    $code = Tools::getValue('code_card');
                    $id_product_code = Tools::getValue('id_product_code');
                    $message = Tools::getValue('message');
                    $customer_receive = new Customer($id_customer_receive);
                    $customer_send = new Customer($id_customer);
                    
                    $id_info_gift = Db::getInstance()->getRow('SELECT pc.id_order as id_order, pc.id_product as id_product, pc.last_digits as last_digits, pc.pin_code as pin_code
                                           FROM '._DB_PREFIX_.'product_code pc WHERE pc.id_product_code = '.$id_product_code);
                    
                    $secure_key_sponsor = Db::getInstance()->getValue('SELECT c.secure_key 
                                           FROM '._DB_PREFIX_.'customer c WHERE c.id_customer = '.$id_customer_receive);
                    
                    $code_encrypt_customer = Encrypt::encrypt($secure_key_sponsor, $code);
                    
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_code SET send_gift = 1
                                       WHERE id_product_code='.$id_product_code.' AND id_order = '.$id_info_gift['id_order']);
                    
                    Db::getInstance()->execute('INSERT INTO '. _DB_PREFIX_ .'transfer_gift (id_product, id_customer_send, id_customer_receive, message_motive)
                                       VALUES ('.(int)$id_info_gift['id_product'].','.$id_customer.','.$id_customer_receive.',"'.$message.'")');
                    
                    $id_transfer_gift = Db::getInstance()->getRow('SELECT id_transfer_gift FROM '._DB_PREFIX_.'transfer_gift WHERE id_customer_send='.(int)$id_customer. ' ORDER BY id_transfer_gift DESC');
                    Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "product_code (id_product, code, last_digits, pin_code, id_order, used, date_add, state, encry, send_gift, id_transfer_gift)
                                            VALUES (" . (int)$id_info_gift['id_product']. ",'".$code_encrypt_customer."','".$id_info_gift['last_digits']."'," . (int)$id_info_gift['pin_code']. ", 0, 0,'" . date("Y-m-d H:i:s") . "','Disponible', 1, 2," .$id_transfer_gift['id_transfer_gift']. ")");
                    
                    $list_product = Db::getInstance()->getRow('SELECT od.product_name, od.product_quantity, o.total_paid, pl.description_short
                                    FROM '. _DB_PREFIX_ .'order_detail od
                                    LEFT JOIN '. _DB_PREFIX_ .'product_code pc ON (pc.id_order = od.id_order)
                                    LEFT JOIN '. _DB_PREFIX_ .'orders o ON (od.id_order = o.id_order)
                                    LEFT JOIN '. _DB_PREFIX_ .'product_lang pl ON (pl.id_product = od.product_id)    
                                    WHERE od.id_order ='.(int)$id_info_gift['id_product']);
                   
                    $vars = array(
                        '{username}' => $customer_receive->username,
                        '{sender_username}' => $customer_send->username,
                        '{sender_message}' => $message,
                        '{name_product}'=> $list_product['product_name'],
                        '{code_product}'=> $code,
                        '{quantity}'=> $list_product['product_quantity'],
                        '{total_products}'=> round($list_product['total_paid']),
                        '{description}' => $list_product['description_short'],
                        '{date}' => Tools::displayDate(date('Y-m-d H:i:s'), null, 1),
                        '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                        '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                        '{shop_url_personal}' => Context::getContext()->link->getPageLink('identity', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                        '{learn_more_url}' => "http://reglas.fluzfluz.co",
                    );
                    print_r($vars);
                    $template = 'send_gift';
                    $prefix_template = '16-send_gift';

                    $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
                    $row_subject = Db::getInstance()->getRow($query_subject);
                    $message_subject = $row_subject['subject_mail'];
                    
                    $allinone_rewards = new allinone_rewards();
                    $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject),$vars, $customer_receive->email, $customer_receive->firstname.' '.$customer_receive->lastname);
                    
                    die();
                break;
            default:
                break;
        }
    }
}
