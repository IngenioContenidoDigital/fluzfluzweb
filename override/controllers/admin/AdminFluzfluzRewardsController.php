<?php

class AdminFluzfluzRewardsControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;

        $this->context = Context::getContext();

        AdminController::__construct();
    }

    public function renderForm()
    {
        $list_values[0] = array(id_state => 0, stateReward => 'Desactivar');
        $list_values[1] = array(id_state => 1, stateReward => 'Activar');
        
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('FluzFluz Customer Rewards'),
                'icon' => 'icon-download'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Tipo de visualizacion'),
                    'name' => 'stateReward',
                    'required' => true,
                    'desc' => $this->l('Activado / Desactivado'),
                    'options' => array(
                        'query' => $list_values,
                        'id' => 'id_state',
                        'name' => 'stateReward'
                    ),
                    'hint' => $this->l('Escoge estado. Activado / Desactivado')
                ), 
                array(
                    'type' => 'text',
                    'label' => $this->l('Fluz a Distribuir'),
                    'name' => 'rewardsCustomer',
                    'required' => true,
                    'col' => '1',
                    'hint' => $this->l('Invalid characters:').'!&lt;&gt;,;?=+()@#"°{}_$%:'
                ),
                array(
                    'type' => 'date',
                    'label' => $this->l('From'),
                    'name' => 'date_from',
                    'maxlength' => 10,
                    'required' => true,
                    'hint' => $this->l('Format: 2011-12-31 (inclusive).')
                ),
                array(
                    'type' => 'date',
                    'label' => $this->l('To'),
                    'name' => 'date_to',
                    'maxlength' => 10,
                    'required' => true,
                    'hint' => $this->l('Format: 2012-12-31 (inclusive).')
                )
            ),
            'submit' => array(
                'title' => $this->l('Guardar'),
                'name' => 'saveRewards',
                'type' => 'submit',
                'icon' => 'process-icon-save'
            )
        );

        $this->fields_value = array(
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d')
        );

        return AdminController::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('saveRewards')) {
            $this->saveCustomerRewards(Tools::getValue('stateReward'),Tools::getValue('rewardsCustomer'),Tools::getValue('date_from'), Tools::getValue('date_to') );
        }
    }

    public function initContent()
    {
        $this->initTabModuleList();
        $this->initPageHeaderToolbar();
        $this->show_toolbar = false;
        $this->content .= $this->renderForm();
        $this->content .= $this->renderOptions();
        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }
    
    public function saveCustomerRewards($state,$rewards, $date_from = "", $date_to = ""){
        
        //insertar datos.
        
    }
}
