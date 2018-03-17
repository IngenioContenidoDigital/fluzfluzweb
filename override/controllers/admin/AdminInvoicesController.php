<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminInvoicesController extends AdminInvoicesControllerCore
{
    public function initContent()
    {
        $this->display = 'edit';
        $this->initTabModuleList();
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        $this->content .= $this->initFormByDateExcel();
        $this->content .= $this->initFormByDate();
        $this->content .= $this->initFormByStatus();
        $this->table = 'invoice';
        $this->content .= $this->renderOptions();

        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }
    
    public function initFormByDateExcel()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Excel por fecha'),
                'icon' => 'icon-calendar'
            ),
            'input' => array(
                array(
                    'type' => 'date',
                    'label' => $this->l('From'),
                    'name' => 'date_from_ex',
                    'maxlength' => 10,
                    'required' => true,
                    'hint' => $this->l('Format: 2011-12-31 (inclusive).')
                ),
                array(
                    'type' => 'date',
                    'label' => $this->l('To'),
                    'name' => 'date_to_ex',
                    'maxlength' => 10,
                    'required' => true,
                    'hint' => $this->l('Format: 2012-12-31 (inclusive).')
                )
            ),
            'submit' => array(
                'title' => $this->l('Generar Excel por fecha'),
                'id' => 'submitPrintExcel',
                'icon' => 'process-icon-download-alt'
            )
        );
        
        $this->fields_value = array(
            'date_from_ex' => date('Y-m-d'),
            'date_to_ex' => date('Y-m-d')
        );

        $this->show_toolbar = false;
        $this->show_form_cancel_button = false;
        return parent::renderForm();
    }
    
    public function postProcess()
    {
        if (Tools::isSubmit('submitAddinvoice_date')) {
            if (!Validate::isDate(Tools::getValue('date_from'))) {
                $this->errors[] = $this->l('Invalid "From" date');
            }

            if (!Validate::isDate(Tools::getValue('date_to'))) {
                $this->errors[] = $this->l('Invalid "To" date');
            }

            if (!count($this->errors)) {
                if (count(OrderInvoice::getByDateInterval(Tools::getValue('date_from'), Tools::getValue('date_to')))) {
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminPdf').'&submitAction=generateInvoicesPDF&date_from='.urlencode(Tools::getValue('date_from')).'&date_to='.urlencode(Tools::getValue('date_to')));
                }

                $this->errors[] = $this->l('No invoice has been found for this period.');
            }
        } 
        elseif (Tools::isSubmit('submitAddinvoice')) {
            
            $date_from = Tools::getValue('date_from_ex');
            $date_to = Tools::getValue('date_to_ex');
            
            $query_excel = 'SELECT
                            -- o.id_order,
                            oi.number factura_venta,
                            o.date_add fecha,
                            CONCAT(c.firstname," ",c.lastname) nombre_cliente,
                            a.phone telefono_cliente_1,
                            a.phone_mobile telefono_cliente_2,
                            c.dni identificacion_cliente,
                            c.email email_cliente,
                            CONCAT(a.address1," | ",a.address2) direccion_cliente,
                            a.city ciudad,
                            pl.name producto,
                            od.product_quantity cantidad,
                            od.product_reference ref,
                            od.product_price vr_pesos,
                            o.total_paid valor_total
                            FROM ps_orders o
                            LEFT JOIN ps_order_invoice oi ON ( o.id_order = oi.id_order )
                            LEFT JOIN ps_customer c ON ( o.id_customer = c.id_customer )
                            LEFT JOIN ps_address a ON ( o.id_customer = a.id_customer AND (a.phone != "" OR a.phone_mobile != "") )
                            LEFT JOIN ps_order_detail od ON ( o.id_order = od.id_order )
                            LEFT JOIN ps_product_lang pl ON ( od.product_id = pl.id_product AND pl.id_lang = 1 )
                            WHERE o.date_add BETWEEN "'.$date_from.'" AND "'.$date_to.'"
                            AND o.current_state = 2
                            GROUP BY o.id_order, od.id_order_detail, o.id_customer
                            ORDER BY oi.number';
            
            $invoice_excel = Db::getInstance()->executeS($query_excel);
            
            $report = "<html>
                        <head>
                            <meta http-equiv=Content-Type content=text/html; charset=iso-8859-1 />
                        </head>
                            <body>
                                <table>
                                    <tr>
                                        <th>factura_venta</th>
                                        <th>fecha</th>
                                        <th>nombre_cliente</th>
                                        <th>telefono_cliente_1</th>
                                        <th>telefono_cliente_2</th>
                                        <th>identificacion_cliente</th>
                                        <th>email_cliente</th>
                                        <th>direccion_cliente</th>
                                        <th>ciudad</th>
                                        <th>producto</th>
                                        <th>cantidad</th>
                                        <th>ref</th>
                                        <th>vr_pesos</th>
                                        <th>valor_total</th>
                                        ";
            
            $report .= "</tr>";
            
            foreach ($invoice_excel as $data)
                {
                    $report .= "<tr>
                            <td>".$data['factura_venta']."</td>
                            <td>".$data['fecha']."</td>
                            <td>".$data['nombre_cliente']."</td>
                            <td>".$data['telefono_cliente_1']."</td>
                            <td>".$data['telefono_cliente_2']."</td>
                            <td>".$data['identificacion_cliente']."</td>    
                            <td>".$data['email_cliente']."</td>
                            <td>".$data['direccion_cliente']."</td>
                            <td>".$data['ciudad']."</td>
                            <td>".$data['producto']."</td>
                            <td>".$data['cantidad']."</td>
                            <td>".$data['ref']."</td>
                            <td>".$data['vr_pesos']."</td>
                            <td>".$data['valor_total']."</td>";
                }
            $report .= "         </table>
                        </body>
                    </html>";    
            
            header("Content-Type: application/vnd.ms-excel");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("content-disposition: attachment;filename=reporte_facturacion.xls");
            die($report);
            
        }
        elseif (Tools::isSubmit('submitAddinvoice_status')) {
            if (!is_array($status_array = Tools::getValue('id_order_state')) || !count($status_array)) {
                $this->errors[] = $this->l('You must select at least one order status.');
            } else {
                foreach ($status_array as $id_order_state) {
                    if (count(OrderInvoice::getByStatus((int)$id_order_state))) {
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminPdf').'&submitAction=generateInvoicesPDF2&id_order_state='.implode('-', $status_array));
                    }
                }

                $this->errors[] = $this->l('No invoice has been found for this status.');
            }
        } else {
            parent::postProcess();
        }
    }
}
