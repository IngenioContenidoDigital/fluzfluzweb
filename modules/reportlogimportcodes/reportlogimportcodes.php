<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportlogimportcodes extends ModuleGrid
{
    private $html;
    private $query;
    private $columns;

    public function __construct()
    {
        $this->name = 'reportlogimportcodes';
        $this->tab = 'analytics_stats';
        $this->version = '1.0';
        $this->author = 'Ingenio Contenido Digital SAS';

        parent::__construct();

        $this->columns = array(
            array(
                'id' => 'id_log_import_codes',
                'header' => $this->l('ID'),
                'dataIndex' => 'id_log_import_codes',
                'align' => 'center'
            ),
            array(
                'id' => 'merchant',
                'header' => $this->l('Comerciante'),
                'dataIndex' => 'merchant',
                'align' => 'center'
            ),
            array(
                'id' => 'sku',
                'header' => $this->l('ID Producto'),
                'dataIndex' => 'sku',
                'align' => 'center'
            ),
            array(
                'id' => 'product',
                'header' => $this->l('Producto'),
                'dataIndex' => 'product',
                'align' => 'center'
            ),
            array(
                'id' => 'quantity',
                'header' => $this->l('# Codigos Importados'),
                'dataIndex' => 'quantity',
                'align' => 'center'
            ),
            array(
                'id' => 'employee',
                'header' => $this->l('Empleado Importador'),
                'dataIndex' => 'employee',
                'align' => 'center'
            ),
            array(
                'id' => 'api',
                'header' => $this->l('API Importador'),
                'dataIndex' => 'api',
                'align' => 'center'
            ),
            array(
                'id' => 'file',
                'header' => $this->l('Archivo'),
                'dataIndex' => 'file',
                'align' => 'center'
            ),
            array(
                'id' => 'date_import',
                'header' => $this->l('Fecha Importacion'),
                'dataIndex' => 'date_import',
                'align' => 'center'
            ),
        );

        $this->displayName = $this->l('Reporte Log Importe de Codigos de Productos');
        $this->description = $this->l('Reporte Log Importe de Codigos de Productos');
    }

    public function install()
    {
        return (parent::install() &&
            $this->registerHook('AdminStatsModules'));
    }

    public function getData()
    {
        // $date_between = $this->getDate();
        $this->query = "SELECT lic.id_log_import_codes, m.name merchant, lic.sku, pl.name product, lic.quantity, lic.employee, lic.api, lic.date_import, lic.file
                        FROM "._DB_PREFIX_."log_import_codes lic
                        LEFT JOIN "._DB_PREFIX_."manufacturer m ON ( lic.merchant = m.id_manufacturer )
                        LEFT JOIN "._DB_PREFIX_."product_lang pl ON ( lic.sku = pl.id_product AND pl.id_lang = 1 )";

        $list = Db::getInstance()->executeS($this->query);

        if ( $list[0]['id_log_import_codes'] != "" ) {
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