<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportReferralCode extends ModuleGrid
{
    public function __construct(){
        $this->name = 'reportReferralCode';
        $this->tab = 'analytics_stats';
        $this->version = '1.0';
        $this->author = 'Ingenio Contenido Digital SAS';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Reporte Codigo Referidos');
        $this->description = $this->l('Reporte Codigo Referidos de la red');
        
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

    }
    
    public function install()
    {
        return (parent::install() &&
            $this->registerHook('AdminStatsModules'));
    }
    
    public function getData()
    {
       
    }

    public function hookAdminStatsModules($params)
    {
        $this->context->smarty->assign(array(
            'displayName' => $this->displayName,
        ));
        
        return $this->display(__FILE__, 'views/reportReferral_admin.tpl');
        //return $this->html;
    }
}