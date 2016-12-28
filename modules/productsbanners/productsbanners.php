<?php

if (!defined('_PS_VERSION_'))
    exit;

class productsbanners extends Module {
    
    public function __construct(){
        $this->name = 'productsbanners';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'Ingenio Contenido Digital';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Banners producto');
        $this->description = $this->l('Banners producto');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('productsbanners'))      
            $this->warning = $this->l('No name provided');
    }
    
    public function install() {

        if (!parent::install() || !$this->registerHook('displayAdminProductsExtra') || !Configuration::updateValue('productsbanners', '1'))
            return false;

        return true;
    }
    
    public function uninstall() {

        if (!parent::uninstall() || !Configuration::deleteByName('productsbanners'))
            return false;

        return true;
    }
    
    public function hookdisplayAdminProductsExtra($params) {
        $images[] = "/img/p-banners/".Tools::getValue('id_product')."_0.jpg";
        $images[] = "/img/p-banners/".Tools::getValue('id_product')."_1.jpg";
        
        $this->context->smarty->assign('id_product', Tools::getValue('id_product') );
        $this->context->smarty->assign('images', $images );
        return $this->display(__FILE__, 'views/productsbanners.tpl');
    }
}
