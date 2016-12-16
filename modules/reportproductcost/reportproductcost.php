<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportproductcost extends ModuleGrid
{
    private $html;
    private $query;
    private $columns;

    public function __construct()
    {
        $this->name = 'reportproductcost';
        $this->tab = 'analytics_stats';
        $this->version = '1.0';
        $this->author = 'Ingenio Contenido Digital SAS';

        parent::__construct();

        $this->columns = array(
            array(
                'id' => 'id_product',
                'header' => $this->l('ID'),
                'dataIndex' => 'id_product',
                'align' => 'center'
            ),
            array(
                'id' => 'name',
                'header' => $this->l('Producto'),
                'dataIndex' => 'name',
                'align' => 'center'
            ),
            array(
                'id' => 'reference',
                'header' => $this->l('Referencia'),
                'dataIndex' => 'reference',
                'align' => 'center'
            ),
            array(
                'id' => 'price',
                'header' => $this->l('Precio'),
                'dataIndex' => 'price',
                'align' => 'center'
            ),
            array(
                'id' => 'quantity',
                'header' => $this->l('Cantidad'),
                'dataIndex' => 'quantity',
                'align' => 'center'
            ),
            array(
                'id' => 'state',
                'header' => $this->l('Estado'),
                'dataIndex' => 'state',
                'align' => 'center'
            ),
            array(
                'id' => 'supplier',
                'header' => $this->l('Proveedor'),
                'dataIndex' => 'supplier',
                'align' => 'center'
            ),
            array(
                'id' => 'price_supplier',
                'header' => $this->l('Precio Proveedor'),
                'dataIndex' => 'price_supplier',
                'align' => 'center'
            ),
        );

        $this->displayName = $this->l('Reporte Costo de Productos Proveedor');
        $this->description = $this->l('Reporte del costo de productos por proveedor');
    }

    public function install()
    {
        return (parent::install() &&
            $this->registerHook('AdminStatsModules'));
    }

    public function getData()
    {
        // $date_between = $this->getDate();
        $this->query = "SELECT p.id_product, pl.name, p.reference, p.price, SUM(od.product_quantity) quantity, IF(p.active=1,'Activo','Inactivo') state, s.name supplier, ps.product_supplier_price_te price_supplier
                        FROM "._DB_PREFIX_."product p
                        INNER JOIN "._DB_PREFIX_."product_lang pl ON ( p.id_product = pl.id_product AND pl.id_lang = 1 )
                        LEFT JOIN "._DB_PREFIX_."order_detail od ON ( p.id_product = od.product_id )
                        LEFT JOIN "._DB_PREFIX_."supplier s ON ( p.id_supplier = s.id_supplier )
                        LEFT JOIN "._DB_PREFIX_."product_supplier ps ON ( p.id_product = ps.id_product )
                        GROUP BY p.id_product, ps.id_product_supplier";

        $list = Db::getInstance()->executeS($this->query);

        if ( $list[0]['id_product'] != "" ) {
            $this->_values = $list;
        }
    }

    public function hookAdminStatsModules($params)
    {
        $this->context->smarty->assign(array(
            'displayName' => $this->displayName,
        ));

        $engine_params = array(
            'id' => 'id_product',
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