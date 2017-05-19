<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportinvitationsnotaccepted extends ModuleGrid
{
    private $html;
    private $query;
    private $columns;

    public function __construct()
    {
        $this->name = 'reportinvitationsnotaccepted';
        $this->tab = 'analytics_stats';
        $this->version = '1.0';
        $this->author = 'Ingenio Contenido Digital SAS';

        parent::__construct();

        $this->columns = array(
            array(
                'id' => 'id_sponsorship',
                'header' => $this->l('ID'),
                'dataIndex' => 'id_sponsorship',
                'align' => 'center'
            ),
            array(
                'id' => 'sponsor_username',
                'header' => $this->l('Sponsor'),
                'dataIndex' => 'sponsor_username',
                'align' => 'center'
            ),
            array(
                'id' => 'sponsor_email',
                'header' => $this->l('Sponsor Email'),
                'dataIndex' => 'sponsor_email',
                'align' => 'center'
            ),
            array(
                'id' => 'sponsor_firstname',
                'header' => $this->l('Sponsor Nombre'),
                'dataIndex' => 'sponsor_firstname',
                'align' => 'center'
            ),
            array(
                'id' => 'sponsor_lastname',
                'header' => $this->l('Sponsor Apellido'),
                'dataIndex' => 'sponsor_lastname',
                'align' => 'center'
            ),
            array(
                'id' => 'firstname',
                'header' => $this->l('Invitado Nombre'),
                'dataIndex' => 'firstname',
                'align' => 'center'
            ),
            array(
                'id' => 'lastname',
                'header' => $this->l('Invitado Apellido'),
                'dataIndex' => 'lastname',
                'align' => 'center'
            ),
            array(
                'id' => 'email',
                'header' => $this->l('Invitado Email'),
                'dataIndex' => 'email',
                'align' => 'center'
            ),
            array(
                'id' => 'date_add',
                'header' => $this->l('Fecha Invitacion'),
                'dataIndex' => 'date_add',
                'align' => 'center'
            ),
            array(
                'id' => 'date_end',
                'header' => $this->l('Fecha Cancelacion'),
                'dataIndex' => 'date_end',
                'align' => 'center'
            ),
        );

        $this->displayName = $this->l('Reporte Invitaciones No Aceptadas');
        $this->description = $this->l('Reporte Invitaciones No Aceptadas');
    }

    public function install()
    {
        return (parent::install() &&
            $this->registerHook('AdminStatsModules'));
    }

    public function getData()
    {
        $date_between = $this->getDate();
        $this->query = "SELECT *
                        FROM "._DB_PREFIX_."rewards_sponsorship_not_accepted
                        WHERE date_end BETWEEN ".$date_between;

        $list = Db::getInstance()->executeS($this->query);

        if ( $list[0]['id_sponsorship'] != "" ) {
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