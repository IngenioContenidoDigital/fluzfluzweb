<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportpendinginvitations extends ModuleGrid
{
    private $html;
    private $query;
    private $columns;

    public function __construct()
    {
        $this->name = 'reportpendinginvitations';
        $this->tab = 'analytics_stats';
        $this->version = '1.0';
        $this->author = 'Ingenio Contenido Digital SAS';

        parent::__construct();

        $this->columns = array(
            array(
                'id' => 'sponsor',
                'header' => $this->l('Usuario Padre'),
                'dataIndex' => 'sponsor',
                'align' => 'center'
            ),
            array(
                'id' => 'user',
                'header' => $this->l('Usuario Invitado'),
                'dataIndex' => 'user',
                'align' => 'center'
            ),
            array(
                'id' => 'date_add',
                'header' => $this->l('Fecha Invitacion'),
                'dataIndex' => 'date_add',
                'align' => 'center'
            ),
            array(
                'id' => 'last_email',
                'header' => $this->l('Ultimo correo recordatorio'),
                'dataIndex' => 'last_email',
                'align' => 'center'
            ),
        );

        $this->displayName = $this->l('Reporte Invitaciones Pendientes');
        $this->description = $this->l('Reporte invitaciones pendientes por responder.');
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
                            IFNULL(c2.email, '') sponsor,
                            rs.email user,
                            rs.date_add,
                            IFNULL(
                                (SELECT nh.date_send
                                FROM "._DB_PREFIX_."notification_history nh
                                WHERE nh.type_message = 'Recordatorio invitacion'
                                AND nh.id_customer = rs.id_customer
                                ORDER BY nh.date_send DESC
                                LIMIT 1)
                            , '') last_email
                        FROM "._DB_PREFIX_."rewards_sponsorship rs
                        LEFT JOIN "._DB_PREFIX_."customer c ON ( rs.id_customer = c.id_customer )
                        LEFT JOIN "._DB_PREFIX_."customer c2 ON ( rs.id_sponsor = c2.id_customer )
                        WHERE c.id_customer IS NULL";

        $list = Db::getInstance()->executeS($this->query);

        if ( $list[0]['user'] != "" ) {
            $this->_values = $list;
        }
    }

    public function hookAdminStatsModules($params)
    {
        $this->context->smarty->assign(array(
            'displayName' => $this->displayName,
        ));

        $engine_params = array(
            'id' => 'user',
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