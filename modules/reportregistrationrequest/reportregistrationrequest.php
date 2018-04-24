<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportregistrationrequest extends ModuleGrid
{
    private $html;
    private $query;
    private $columns;

    public function __construct()
    {
        $this->name = 'reportregistrationrequest';
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
                'id' => 'phone',
                'header' => $this->l('Telefono'),
                'dataIndex' => 'phone',
                'align' => 'center'
            ),
            array(
                'id' => 'dni',
                'header' => $this->l('Documento'),
                'dataIndex' => 'dni',
                'align' => 'center'
            ),
            array(
                'id' => 'address1',
                'header' => $this->l('Direccion'),
                'dataIndex' => 'address1',
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
                'id' => 'active',
                'header' => $this->l('Activo'),
                'dataIndex' => 'active',
                'align' => 'center'
            ),
            array(
                'id' => 'expulsado',
                'header' => $this->l('Expulsado'),
                'dataIndex' => 'expulsado',
                'align' => 'center'
            ),
            array(
                'id' => 'sms_confirm',
                'header' => $this->l('SMS Code'),
                'dataIndex' => 'sms_confirm',
                'align' => 'center'
            ),
            array(
                'id' => 'date_add',
                'header' => $this->l('Fecha Registro'),
                'dataIndex' => 'date_add',
                'align' => 'center'
            ),
        );

        $this->displayName = $this->l('Reporte Registro Usuarios Pagina solicitud-registro');
        $this->description = $this->l('Reporte Registro Usuarios Pagina solicitud-registro');
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
                            c.id_customer,
                            c.firstname,
                            c.lastname,
                            c.username,
                            c.email,
                            c.phone,
                            c.dni,
                            a.address1,
                            cl.name country_name,
                            a.city,
                            c.date_add,
                            IF(c.active=1,"Activo","Inactivo") active,
                            IF(c.kick_out=1,"Si","No") expulsado,
                            c.sms_confirm
                        FROM '._DB_PREFIX_.'customer c
                        LEFT JOIN '._DB_PREFIX_.'address a ON ( c.id_customer = a.id_customer )
                        LEFT JOIN '._DB_PREFIX_.'country_lang cl ON ( a.id_country = cl.id_country AND cl.id_lang = 1 )
                        WHERE c.method_add LIKE "%SolicitudRegistro%"
                        AND c.date_add BETWEEN '.$date_between.'
                        GROUP BY c.id_customer';

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