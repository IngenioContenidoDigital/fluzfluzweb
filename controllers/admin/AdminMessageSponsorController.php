<?php

class AdminMessageSponsorControllerCore extends AdminController
{    
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'message_sponsor';
        $this->lang = false;
        $this->context = Context::getContext();

        $this->_select = 'a.id_message_sponsor, cs.username customer_send, cr.username customer_receive, a.message, a.date_send';
        $this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'customer` cs ON (cs.`id_customer` = a.`id_customer_send`)
                LEFT JOIN `'._DB_PREFIX_.'customer` cr ON (cr.`id_customer` = a.`id_customer_receive`)
                ';
        $this->_orderBy = 'a.id_message_sponsor';
        $this->_orderWay = 'DESC';
        $this->_use_found_rows = true;

        $this->fields_list = array(
            'id_message_sponsor' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center'
            ),
            'customer_send' => array(
                'title' => $this->l('Envia'),
                'align' => 'text-center',
                'filter_key' => 'cs!username'
            ),
            'customer_receive' => array(
                'title' => $this->l('Recibe'),
                'align' => 'text-center',
                'filter_key' => 'cr!username'
            ),
            'message' => array(
                'title' => $this->l('Mensaje'),
                'align' => 'text-center'
            ),
            'date_send' => array(
                'title' => $this->l('Fecha'),
                'type' => 'datetime',
                'align' => 'text-center',
                'filter_key' => 'a!date_send'
            )
        );

        parent::__construct();
    }

    public function postProcess()
    {
        return parent::postProcess();
    }

    public function initContent()
    {
        return parent::initContent();
    }
}
