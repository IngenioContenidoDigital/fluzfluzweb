<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportexpirationcodes extends ModuleGrid
{
    private $html;
    private $query;
    private $columns;

    public function __construct()
    {
        $this->name = 'reportexpirationcodes';
        $this->tab = 'analytics_stats';
        $this->version = '1.0';
        $this->author = 'Ingenio Contenido Digital SAS';

        parent::__construct();

        $this->columns = array(
            array(
                'id' => 'id_product',
                'header' => $this->l('ID Producto'),
                'dataIndex' => 'id_product',
                'align' => 'center'
            ),
            array(
                'id' => 'reference',
                'header' => $this->l('Referencia'),
                'dataIndex' => 'reference',
                'align' => 'center'
            ),
            array(
                'id' => 'name',
                'header' => $this->l('Producto'),
                'dataIndex' => 'name',
                'align' => 'center'
            ),
            array(
                'id' => 'id_product_code',
                'header' => $this->l('ID Codigo'),
                'dataIndex' => 'id_product_code',
                'align' => 'center'
            ),
            array(
                'id' => 'code',
                'header' => $this->l('Codigo'),
                'dataIndex' => 'code',
                'align' => 'center'
            ),
            array(
                'id' => 'expiration',
                'header' => $this->l('Fecha Expiracion'),
                'dataIndex' => 'expiration',
                'align' => 'center'
            ),
        );

        $this->displayName = $this->l('Reporte Codigos Proximos a Vencer');
        $this->description = $this->l('Reporte Codigos Proximos a Vencer');
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
                            p.id_product,
                            p.reference,
                            pl.name,
                            pc.id_product_code,
                            pc.code,
                            p.expiration
                        FROM "._DB_PREFIX_."product_code pc
                        INNER JOIN "._DB_PREFIX_."product p ON ( pc.id_product = p.id_product )
                        INNER JOIN "._DB_PREFIX_."product_lang pl ON ( pc.id_product = pl.id_product AND pl.id_lang = 1 )
                        WHERE p.expiration BETWEEN DATE_ADD(NOW(), INTERVAL -1 MONTH)  AND DATE_ADD(NOW(), INTERVAL 3 MONTH)
                        AND p.expiration BETWEEN ".$date_between."
                        ORDER BY p.expiration ASC";

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