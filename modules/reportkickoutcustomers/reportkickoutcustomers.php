<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportkickoutcustomers extends ModuleGrid
{
    private $html;
    private $query;
    private $columns;

    public function __construct()
    {
        $this->name = 'reportkickoutcustomers';
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
                'id' => 'firstname',
                'header' => $this->l('Nombre'),
                'dataIndex' => 'firstname',
                'align' => 'center'
            ),
            array(
                'id' => 'lastname',
                'header' => $this->l('Apellido'),
                'dataIndex' => 'lastname',
                'align' => 'center'
            ),
            array(
                'id' => 'email',
                'header' => $this->l('Email'),
                'dataIndex' => 'email',
                'align' => 'center'
            ),
            array(
                'id' => 'points',
                'header' => $this->l('Puntos'),
                'dataIndex' => 'points',
                'align' => 'center'
            ),
            array(
                'id' => 'phone',
                'header' => $this->l('Telefono'),
                'dataIndex' => 'phone',
                'align' => 'center'
            ),
            array(
                'id' => 'phone_mobile',
                'header' => $this->l('Telefono Movil'),
                'dataIndex' => 'phone_mobile',
                'align' => 'center'
            ),
            array(
                'id' => 'date_kick_out',
                'header' => $this->l('Fecha Expulsion'),
                'dataIndex' => 'date_kick_out',
                'align' => 'center'
            ),
        );

        $this->displayName = $this->l('Reporte Clientes Expulsados');
        $this->description = $this->l('Reporte clientes expulsados de la red');
    }

    public function install()
    {
        return (parent::install() &&
            $this->registerHook('AdminStatsModules'));
    }

    public function getData()
    {
        $date_between = $this->getDate();
        $this->query = "SELECT rws.id_customer, rws.firstname, rws.lastname, rws.email, IFNULL(c.phone, a.phone) as phone, a.phone_mobile, rws.date_kick_out, IFNULL(SUM(r.credits), '0') points
                        FROM "._DB_PREFIX_."rewards_sponsorship_kick_out rws
                        LEFT JOIN "._DB_PREFIX_."rewards r ON ( rws.id_customer = r.id_customer )
                        LEFT JOIN "._DB_PREFIX_."customer c ON ( rws.id_customer = c.id_customer )
                        LEFT JOIN "._DB_PREFIX_."address a ON ( rws.id_customer = a.id_customer )    
                        WHERE rws.date_kick_out BETWEEN ".$date_between."
                        GROUP BY rws.id_customer";

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