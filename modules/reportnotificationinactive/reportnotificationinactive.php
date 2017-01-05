<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportnotificationinactive extends ModuleGrid
{
    private $html;
    private $query;
    private $columns;

    public function __construct()
    {
        $this->name = 'reportnotificationinactive';
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
                'id' => 'email',
                'header' => $this->l('Email'),
                'dataIndex' => 'email',
                'align' => 'center'
            ),
            array(
                'id' => 'date_add',
                'header' => $this->l('Fecha Registro'),
                'dataIndex' => 'date_add',
                'align' => 'center'
            ),
            array(
                'id' => 'last_purchase',
                'header' => $this->l('Ultima Compra'),
                'dataIndex' => 'last_purchase',
                'align' => 'center'
            ),
            array(
                'id' => 'date_alert_30',
                'header' => $this->l('Fecha Alerta Dia 30'),
                'dataIndex' => 'date_alert_30',
                'align' => 'center'
            ),
            array(
                'id' => 'date_alert_45',
                'header' => $this->l('Fecha Alerta Dia 45'),
                'dataIndex' => 'date_alert_45',
                'align' => 'center'
            ),
            array(
                'id' => 'date_alert_52',
                'header' => $this->l('Fecha Alerta Dia 52'),
                'dataIndex' => 'date_alert_52',
                'align' => 'center'
            ),
            array(
                'id' => 'date_alert_59',
                'header' => $this->l('Fecha Alerta Dia 59'),
                'dataIndex' => 'date_alert_59',
                'align' => 'center'
            ),
            array(
                'id' => 'date_alert_60',
                'header' => $this->l('Fecha Alerta Dia 60'),
                'dataIndex' => 'date_alert_60',
                'align' => 'center'
            ),
            array(
                'id' => 'date_alert_90',
                'header' => $this->l('Fecha Alerta Dia 90'),
                'dataIndex' => 'date_alert_90',
                'align' => 'center'
            ),
        );

        $this->displayName = $this->l('Reporte Notificaciones Inactividad');
        $this->description = $this->l('Reporte de notificaciones a los clientes por inactividad.');
    }

    public function install()
    {
        return (parent::install() &&
            $this->registerHook('AdminStatsModules'));
    }

    public function getData()
    {
        // $date_between = $this->getDate();
        $this->query = "SELECT
                            c.id_customer,
                            c.username,
                            c.email,
                            c.date_add,
                            ni.date_alert_30,
                            ni.date_alert_45,
                            ni.date_alert_52,
                            ni.date_alert_59,
                            ni.date_alert_60,
                            ni.date_alert_90,
                            (SELECT MAX(date_add)
                            FROM "._DB_PREFIX_."orders
                            WHERE id_customer = ni.id_customer ) last_purchase
                        FROM "._DB_PREFIX_."notification_inactive ni
                        INNER JOIN "._DB_PREFIX_."customer c ON ( ni.id_customer = c.id_customer )";

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