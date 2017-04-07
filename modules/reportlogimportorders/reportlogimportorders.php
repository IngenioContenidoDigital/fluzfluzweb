<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportlogimportorders extends ModuleGrid
{
    private $html;
    private $query;
    private $columns;

    public function __construct()
    {
        $this->name = 'reportlogimportorders';
        $this->tab = 'analytics_stats';
        $this->version = '1.0';
        $this->author = 'Ingenio Contenido Digital SAS';

        parent::__construct();

        $this->columns = array(
            array(
                'id' => 'id_log_import_orders',
                'header' => $this->l('ID'),
                'dataIndex' => 'id_log_import_orders',
                'align' => 'center'
            ),
            array(
                'id' => 'id_cart',
                'header' => $this->l('# ID Cart'),
                'dataIndex' => 'id_cart',
                'align' => 'center'
            ),
            array(
                'id' => 'quantity',
                'header' => $this->l('# Ordenes Importadas'),
                'dataIndex' => 'quantity',
                'align' => 'center'
            ),
            array(
                'id' => 'status',
                'header' => $this->l('Estado'),
                'dataIndex' => 'status',
                'align' => 'center'
            ),
            array(
                'id' => 'payment',
                'header' => $this->l('Tipo de Pago'),
                'dataIndex' => 'payment',
                'align' => 'center'
            ),
            array(
                'id' => 'link_file',
                'header' => $this->l('Archivo'),
                'dataIndex' => 'link_file',
                'align' => 'center'
            ),
            array(
                'id' => 'employee',
                'header' => $this->l('Empleado Importador'),
                'dataIndex' => 'employee',
                'align' => 'center'
            ),
            array(
                'id' => 'date_import',
                'header' => $this->l('Fecha Importacion'),
                'dataIndex' => 'date_import',
                'align' => 'center'
            ),
        );

        $this->displayName = $this->l('Reporte Log Importe de Ordenes');
        $this->description = $this->l('Reporte Log Importe de Ordenes');
    }

    public function install()
    {
        return (parent::install() &&
            $this->registerHook('AdminStatsModules'));
    }

    public function getData()
    {
        // $date_between = $this->getDate();
        $this->query = "SELECT *
                        FROM "._DB_PREFIX_."log_import_orders";

        $list = Db::getInstance()->executeS($this->query);

        if ( $list[0]['id_log_import_orders'] != "" ) {
            $this->_values = $list;
        }
    }

    public function hookAdminStatsModules($params)
    {
        $this->context->smarty->assign(array(
            'displayName' => $this->displayName,
        ));

        $engine_params = array(
            'id' => 'id_log_import_orders',
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