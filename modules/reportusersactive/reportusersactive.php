<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportusersactive extends ModuleGrid
{
    private $html;
    private $query;
    private $columns;

    public function __construct()
    {
        $this->name = 'reportusersactive';
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
                'id' => 'gender',
                'header' => $this->l('Genero'),
                'dataIndex' => 'gender',
                'align' => 'center'
            ),
            array(
                'id' => 'name_complete',
                'header' => $this->l('Nombre'),
                'dataIndex' => 'name_complete',
                'align' => 'center'
            ),
            array(
                'id' => 'email',
                'header' => $this->l('Email'),
                'dataIndex' => 'email',
                'align' => 'center'
            ),
            array(
                'id' => 'ordenes',
                'header' => $this->l('Compras'),
                'dataIndex' => 'ordenes',
                'align' => 'center'
            ),
            array(
                'id' => 'fluz',
                'header' => $this->l('Fluz'),
                'dataIndex' => 'fluz',
                'align' => 'center'
            ),
            array(
                'id' => 'city',
                'header' => $this->l('Ciudad'),
                'dataIndex' => 'city',
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
        );

        $this->displayName = $this->l('Reporte Clientes Activos');
        $this->description = $this->l('Reporte de clientes activos.');
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
                            c.id_customer,
                            gl.name gender,
                            CONCAT(c.firstname,' ',c.lastname) name_complete,
                            c.email,
                            (SELECT COUNT(o.id_order)
                            FROM "._DB_PREFIX_."orders o
                            WHERE o.id_customer = c.id_customer
                            AND o.current_state = 2) ordenes,
                            a.city,
                            IFNULL((SELECT SUM(credits)
                            FROM ps_rewards
                            WHERE id_customer = c.id_customer
                            AND id_reward_state = 2),0) fluz,
                            a.phone,
                            a.phone_mobile
                        FROM "._DB_PREFIX_."customer c
                        INNER JOIN "._DB_PREFIX_."rewards_sponsorship rs ON ( c.id_customer = rs.id_customer )
                        INNER JOIN "._DB_PREFIX_."address a ON ( c.id_customer = a.id_customer )
                        INNER JOIN "._DB_PREFIX_."gender_lang gl ON ( c.id_gender = gl.id_gender AND gl.id_lang = 1 )
                        WHERE c.active = 1
                        AND c.kick_out = 0
                        GROUP BY c.id_customer";

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