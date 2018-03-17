<?php

class AdminCustomerExportControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;

        $this->context = Context::getContext();

        AdminController::__construct();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Exportar Listado de Usuarios'),
                'icon' => 'icon-download'
            ),
            'input' => array(
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
                'title' => $this->l('Exportar'),
                'name' => 'exportcustomer',
                'type' => 'submit',
                'icon' => 'process-icon-download-alt'
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
        if (Tools::isSubmit('exportcustomer')) {
            Customer::CustomerExport( Tools::getValue('date_from'), Tools::getValue('date_to') );
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
}
