<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class StatsBestSuppliers extends ModuleGrid
{
    private $html = null;
    private $query = null;
    private $columns = null;
    private $default_sort_column = null;
    private $default_sort_direction = null;
    private $empty_message = null;
    private $paging_message = null;

    public function __construct()
    {
        $this->name = 'statsbestsuppliers';
        $this->tab = 'analytics_stats';
        $this->version = '1.4.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->default_sort_column = 'sales';
        $this->default_sort_direction = 'DESC';
        $this->empty_message = $this->l('Empty record set returned');
        $this->paging_message = sprintf($this->l('Displaying %1$s of %2$s'), '{0} - {1}', '{2}');
        
        $this->columns = array(
                array(
                    'id' => 'supplier',
                    'header' => $this->l('Proveedor'),
                    'dataIndex' => 'supplier',
                    'align' => 'center'
                ),
                array(
                    'id' => 'manufacturer',
                    'header' => $this->l('Fabricante'),
                    'dataIndex' => 'manufacturer',
                    'align' => 'center'
                ),
                array(
                    'id' => 'product',
                    'header' => $this->l('Producto'),
                    'dataIndex' => 'product',
                    'align' => 'center'
                ),
                array(
                    'id' => 'product_reference',
                    'header' => $this->l('Referencia'),
                    'dataIndex' => 'product_reference',
                    'align' => 'center'
                ),
                array(
                    'id' => 'product_quantity',
                    'header' => $this->l('Cantidades Vendidas'),
                    'dataIndex' => 'product_quantity',
                    'align' => 'center'
                ),
                array(
                    'id' => 'price_shop',
                    'header' => $this->l('Precio Tienda'),
                    'dataIndex' => 'price_shop',
                    'align' => 'center'
                ),
                array(
                    'id' => 'store_credit_sold',
                    'header' => $this->l('Precio Tienda - Total'),
                    'dataIndex' => 'store_credit_sold',
                    'align' => 'center'
                ),
                array(
                    'id' => 'price',
                    'header' => $this->l('Precio'),
                    'dataIndex' => 'price',
                    'align' => 'center'
                ),
                array(
                    'id' => 'site_revenue',
                    'header' => $this->l('Precio - Total'),
                    'dataIndex' => 'site_revenue',
                    'align' => 'center'
                ),
                array(
                    'id' => 'cost',
                    'header' => $this->l('Precio Proveedor'),
                    'dataIndex' => 'cost',
                    'align' => 'center'
                ),
                array(
                    'id' => 'due_to_merchant',
                    'header' => $this->l('Precio Proveedor - Total'),
                    'dataIndex' => 'due_to_merchant',
                    'align' => 'center'
                ),
                array(
                    'id' => 'quantity',
                    'header' => $this->l('Unidades Disponibles'),
                    'dataIndex' => 'quantity',
                    'align' => 'center'
                ),
        );

        $this->displayName = $this->l('Mejores Ventas Proveedor');
        $this->description = $this->l('Adds a list of the best suppliers to the Stats dashboard.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return (parent::install() && $this->registerHook('AdminStatsModules'));
    }

    public function hookAdminStatsModules($params)
    {
        $engine_params = array(
                'id' => 'product_reference',
                'title' => $this->displayName,
                'columns' => $this->columns,
                'defaultSortColumn' => $this->default_sort_column,
                'defaultSortDirection' => $this->default_sort_direction,
                'emptyMessage' => $this->empty_message,
                'pagingMessage' => $this->paging_message
        );

        if (Tools::getValue('export') == 1)
                        $this->csvExport($engine_params);
        $this->html = '
                <div class="panel-heading">
                        '.$this->displayName.'
                </div>
                '.$this->engine($engine_params).'
                <a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1').'">
                        <i class="icon-cloud-upload"></i> '.$this->l('CSV Export').'
                </a>';
        return $this->html;
    }

    /**
     * @return int Get total of distinct suppliers
     */
    public function getTotalCount()
    {
        $sql = 'SELECT
                        COUNT(DISTINCT(od.product_id))
                FROM '._DB_PREFIX_.'orders o
                LEFT JOIN '._DB_PREFIX_.'order_detail od ON ( o.id_order = od.id_order )
                LEFT JOIN '._DB_PREFIX_.'product p ON ( od.product_id = p.id_product )
                LEFT JOIN '._DB_PREFIX_.'product_lang pl ON ( od.product_id = pl.id_product AND pl.id_lang = 1 )
                LEFT JOIN '._DB_PREFIX_.'supplier s ON ( p.id_supplier = s.id_supplier )
                LEFT JOIN '._DB_PREFIX_.'manufacturer m ON ( p.id_manufacturer = m.id_manufacturer )
                LEFT JOIN '._DB_PREFIX_.'product_supplier ps ON ( od.product_id = ps.id_product )
                LEFT JOIN '._DB_PREFIX_.'stock_available sa ON ( od.product_id = sa.id_product )
                WHERE od.product_reference <> "MFLUZ"
                AND o.date_add BETWEEN '.$this->getDate();

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function getData()
    {
        $this->_totalCount = $this->getTotalCount();

        $this->query = 'SELECT
                                s.name supplier,
                                m.name manufacturer,
                                pl.name product,
                                od.product_reference,
                                SUM(od.product_quantity) product_quantity,
                                p.price_shop,
                                (p.price_shop * SUM(od.product_quantity)) store_credit_sold,
                                p.price,
                                (p.price * SUM(od.product_quantity)) site_revenue,
                                ps.product_supplier_price_te cost,
                                (ps.product_supplier_price_te * SUM(od.product_quantity)) due_to_merchant,
                                sa.quantity
                        FROM '._DB_PREFIX_.'orders o
                        LEFT JOIN '._DB_PREFIX_.'order_detail od ON ( o.id_order = od.id_order )
                        LEFT JOIN '._DB_PREFIX_.'product p ON ( od.product_id = p.id_product )
                        LEFT JOIN '._DB_PREFIX_.'product_lang pl ON ( od.product_id = pl.id_product AND pl.id_lang = 1 )
                        LEFT JOIN '._DB_PREFIX_.'supplier s ON ( p.id_supplier = s.id_supplier )
                        LEFT JOIN '._DB_PREFIX_.'manufacturer m ON ( p.id_manufacturer = m.id_manufacturer )
                        LEFT JOIN '._DB_PREFIX_.'product_supplier ps ON ( od.product_id = ps.id_product )
                        LEFT JOIN '._DB_PREFIX_.'stock_available sa ON ( od.product_id = sa.id_product )
                        WHERE od.product_reference <> "MFLUZ"
                        AND o.date_add BETWEEN '.$this->getDate().'
                        GROUP BY od.product_id
                        ORDER BY SUM(od.product_quantity) DESC';

        $list = Db::getInstance()->executeS($this->query);

        if ( $list[0]['product_reference'] != "" ) {
            $this->_values = $list;
        }
    }
}
