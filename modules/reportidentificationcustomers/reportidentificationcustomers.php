<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportidentificationcustomers extends ModuleGrid
{
    private $html;
    private $query;
    private $columns;

    public function __construct()
    {
        $this->name = 'reportidentificationcustomers';
        $this->tab = 'analytics_stats';
        $this->version = '1.0';
        $this->author = 'Fluz Fluz';

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
                'id' => 'username',
                'header' => $this->l('Username'),
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
                'id' => 'dni',
                'header' => $this->l('Identificacion'),
                'dataIndex' => 'dni',
                'align' => 'center'
            ),
            array(
                'id' => 'active',
                'header' => $this->l('Activo'),
                'dataIndex' => 'active',
                'align' => 'center'
            ),
            array(
                'id' => 'kick_out',
                'header' => $this->l('Expulsado'),
                'dataIndex' => 'kick_out',
                'align' => 'center'
            ),
            array(
                'id' => 'date_add',
                'header' => $this->l('Fecha Registro'),
                'dataIndex' => 'date_add',
                'align' => 'center'
            ),
        );

        $this->displayName = $this->l('Reporte Identificaciones Clientes');
        $this->description = $this->l('Reporte Identificaciones Clientes');
    }

    public function install()
    {
        return (parent::install() &&
            $this->registerHook('AdminStatsModules'));
    }

    public function getData()
    {
        $date_between = $this->getDate();
        
        $this->query = 'SELECT
                            id_customer,
                            firstname,
                            lastname,
                            username,
                            email,
                            dni,
                            IF(active=1,"SI","NO") active,
                            IF(kick_out=1,"SI","NO") kick_out,
                            date_add
                        FROM ps_customer
                        WHERE date_add BETWEEN '.$date_between.'
                        ORDER BY id_customer DESC';

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
            'id' => 'id_user_registration_data',
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