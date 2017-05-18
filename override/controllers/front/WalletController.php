<?php

class WalletController extends FrontController {
    public function initContent() {
        parent::initContent();

        $smarty_values = array(
            's3'=>_S3_PATH_,
            'cards' => Wallet::getCards($this->context->customer->id, Tools::getValue("manufacturer")),
            'addreses_manufacturer' => Wallet::getManufacturerAddress(Tools::getValue("manufacturer")),
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
            default:
                break;
        }
    }
}
