<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportnotificationhistory extends ModuleGrid
{
    private $html;
    private $query;
    private $columns;

    public function __construct()
    {
        $this->name = 'reportnotificationhistory';
        $this->tab = 'analytics_stats';
        $this->version = '1.0';
        $this->author = 'Ingenio Contenido Digital SAS';

        parent::__construct();

        $this->columns = array(
            array(
                'id' => 'id_customer',
                'header' => $this->l('ID'),
                'dataIndex' => 'id_customer',
                'align' => 'center'
            ),
            array(
                'id' => 'username',
                'header' => $this->l('Cliente'),
                'dataIndex' => 'username',
                'align' => 'center'
            ),
            array(
                'id' => 'type_message',
                'header' => $this->l('Tipo Mensaje'),
                'dataIndex' => 'type_message',
                'align' => 'center'
            ),
            array(
                'id' => 'message',
                'header' => $this->l('Mensaje'),
                'dataIndex' => 'message',
                'align' => 'center'
            ),
            array(
                'id' => 'date_send',
                'header' => $this->l('Fecha Enviado'),
                'dataIndex' => 'date_send',
                'align' => 'center'
            ),
        );

        $this->displayName = $this->l('Reporte Historial Notificaciones');
        $this->description = $this->l('Reporte historial de notificaciones');
    }

    public function install()
    {
        return (parent::install() &&
            $this->registerHook('AdminStatsModules'));
    }

    public function getData()
    {
        $date_between = $this->getDate();
        $this->query = "SELECT nh.id_customer, c.username, nh.type_message, nh.message, nh.date_send
                        FROM "._DB_PREFIX_."notification_history nh
                        INNER JOIN "._DB_PREFIX_."customer c ON ( nh.id_customer = c.id_customer )
                        WHERE nh.date_send BETWEEN ".$date_between;

        $list = Db::getInstance()->executeS($this->query);

        if ( $list[0]['id_customer'] != "" ) {
            $this->_values = $list;
        }
    }

    public function hookAdminStatsModules($params)
    {
        $this->context->smarty->assign(array(
            'displayName' => $this->displayName,
        ));

        $engine_params = array(
            'id' => 'id_customer',
            'title' => $this->displayName,
            'columns' => $this->columns
        );

        if (Tools::getValue('export')) {
            $this->csvExport($engine_params);
        }

        $this->html = '
		<div class="panel-heading">
			'.$this->displayName.'
		</div>
                '.$this->display(__FILE__, 'AdminStatsModules.tpl').'
		'.$this->engine($engine_params).'
		<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=').'1">
			<i class="icon-cloud-upload"></i> '.$this->l('CSV Export').'
		</a>';

        return $this->html;
    }
}