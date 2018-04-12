<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportuserregistrationdata extends ModuleGrid
{
    private $html;
    private $query;
    private $columns;

    public function __construct()
    {
        $this->name = 'reportuserregistrationdata';
        $this->tab = 'analytics_stats';
        $this->version = '1.0';
        $this->author = 'Fluz Fluz';

        parent::__construct();

        $this->columns = array(
            array(
                'id' => 'id_user_registration_data',
                'header' => $this->l('ID'),
                'dataIndex' => 'id_user_registration_data',
                'align' => 'center'
            ),
            array(
                'id' => 'name',
                'header' => $this->l('Nombre'),
                'dataIndex' => 'name',
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
                'id' => 'phone',
                'header' => $this->l('Telefono'),
                'dataIndex' => 'phone',
                'align' => 'center'
            ),
            array(
                'id' => 'address',
                'header' => $this->l('Direccion'),
                'dataIndex' => 'address',
                'align' => 'center'
            ),
            array(
                'id' => 'country_name',
                'header' => $this->l('Pais'),
                'dataIndex' => 'country_name',
                'align' => 'center'
            ),
            array(
                'id' => 'city',
                'header' => $this->l('Ciudad'),
                'dataIndex' => 'city',
                'align' => 'center'
            ),
            array(
                'id' => 'typedocument',
                'header' => $this->l('Tipo Documento'),
                'dataIndex' => 'typedocument',
                'align' => 'center'
            ),
            array(
                'id' => 'dni',
                'header' => $this->l('Documento'),
                'dataIndex' => 'dni',
                'align' => 'center'
            ),
            array(
                'id' => 'date_add',
                'header' => $this->l('Fecha Registro'),
                'dataIndex' => 'date_add',
                'align' => 'center'
            ),
        );

        $this->displayName = $this->l('Reporte Registro Informacion Usuarios');
        $this->description = $this->l('Reporte Registro Informacion Usuarios');
    }

    public function install()
    {
        return (parent::install() &&
            $this->registerHook('AdminStatsModules'));
    }

    public function getData()
    {
        $date_between = $this->getDate();
        
        $this->query = "SELECT ud.*, c.name country_name
                        FROM "._DB_PREFIX_."user_registration_data ud
                        INNER JOIN "._DB_PREFIX_."country_lang c ON ( ud.country = c.id_country AND c.id_lang = 1 )
                        WHERE ud.date_add BETWEEN ".$date_between;

        $list = Db::getInstance()->executeS($this->query);

        if ( $list[0]['id_user_registration_data'] != "" ) {
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