<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportpendingsponsorships extends ModuleGrid
{
    private $html;
    private $query;
    private $columns;

    public function __construct()
    {
        $this->name = 'reportpendingsponsorships';
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
                'header' => $this->l('Usuario'),
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
                'id' => 'pendingsinvitation',
                'header' => $this->l('# Espacio Invitaciones'),
                'dataIndex' => 'pendingsinvitation',
                'align' => 'center'
            )
        );

        $this->displayName = $this->l('Reporte Clientes con Espacio para Invitaciones');
        $this->description = $this->l('Reporte Clientes con Espacio para Invitaciones.');
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
                            (2 - COUNT(rs.id_sponsorship)) pendingsinvitation
                        FROM "._DB_PREFIX_."customer c
                        LEFT JOIN "._DB_PREFIX_."rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                        WHERE c.active = 1
                        AND c.kick_out = 0
                        GROUP BY c.id_customer
                        HAVING pendingsinvitation > 0";

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