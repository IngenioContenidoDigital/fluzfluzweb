<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportkickoutcustomersnotorders extends ModuleGrid
{
    private $html;
    private $query;
    private $columns;

    public function __construct()
    {
        $this->name = 'reportkickoutcustomersnotorders';
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
                'id' => 'name',
                'header' => $this->l('Cliente'),
                'dataIndex' => 'name',
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
                'id' => 'points',
                'header' => $this->l('Puntos'),
                'dataIndex' => 'points',
                'align' => 'center'
            ),
            array(
                'id' => 'date_kick_out',
                'header' => $this->l('Fecha Expulsion'),
                'dataIndex' => 'date_kick_out',
                'align' => 'center'
            ),
        );

        $this->displayName = $this->l('Reporte Clientes Expulsados Sin Ordenes');
        $this->description = $this->l('Reporte Clientes Expulsados Sin Ordenes');
    }

    public function install()
    {
        return (parent::install() &&
            $this->registerHook('AdminStatsModules'));
    }

    public function getData()
    {
        $date_between = $this->getDate();
        $this->query = "SELECT
                            ko.id_customer,
                            c.username,
                            ko.email,
                            CONCAT(ko.firstname,' ',ko.lastname) name,
                            ko.date_kick_out,
                            a.phone,
                            a.phone_mobile,
                            SUM(r.credits) points
                        FROM ps_rewards_sponsorship_kick_out ko
                        LEFT JOIN "._DB_PREFIX_."customer c ON ( ko.id_customer = c.id_customer )
                        LEFT JOIN "._DB_PREFIX_."orders o ON ( ko.id_customer = o.id_customer )
                        LEFT JOIN "._DB_PREFIX_."order_detail od ON ( o.id_order = od.id_order )
                        LEFT JOIN "._DB_PREFIX_."address a ON ( ko.id_customer = a.id_customer )
                        LEFT JOIN "._DB_PREFIX_."rewards r ON ( ko.id_customer = r.id_customer AND r.id_reward_state = 2 )
                        WHERE ( od.product_reference NOT LIKE 'MFLUZ%' OR o.id_order IS NULL )
                        AND ko.date_kick_out BETWEEN ".$date_between."
                        GROUP BY ko.id_customer";

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
