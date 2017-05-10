<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

//include('./classes/codeBar/barcode.class.php');

class Order extends OrderCore
{
    public function codesAssign(){
        $codeText = 'SELECT pc.id_product, p.product_name, pc.code FROM '._DB_PREFIX_.'product_code AS pc LEFT JOIN '._DB_PREFIX_.'order_detail as p ON pc.id_order = p.id_order and p.product_id=pc.id_product WHERE pc.id_order ='.(int)$this->id;
        return Db::getInstance()->executeS($codeText);
    }  
    
    public function numcodesAssign(){
        $codeText = 'select COUNT(code) FROM '._DB_PREFIX_.'product_code WHERE id_order = '.(int)$this->id;
            $rowCode = Db::getInstance()->getRow($codeText);
            
            return $rowCode['COUNT(code)'];    
    }
    
    public static function updateCodes($order){
        
            $context = Context::getContext();
            $invoice = new Address((int)$order->id_address_invoice);
            $delivery = new Address((int)$order->id_address_delivery);
            $customer = new Customer((int)$order->id_customer);
            $delivery_state = $delivery->id_state ? new State((int)$delivery->id_state) : false;
            $invoice_state = $invoice->id_state ? new State((int)$invoice->id_state) : false;
            
            $query = 'SELECT OD.product_id, OD.product_quantity FROM '._DB_PREFIX_.'order_detail AS OD WHERE OD.id_order='.(int)$order->id;
            $productId = Db::getInstance()->executeS($query);
            
            $qstate="UPDATE "._DB_PREFIX_."rewards AS r SET r.id_reward_state= 2 WHERE r.id_order=".$order->id;
            Db::getInstance()->execute($qstate); 

                foreach ($productId as $valor) {
                    for($i=0;$i<$valor['product_quantity'];$i++){
                        $query1=Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_code AS PC SET PC.id_order='.(int)$order->id.' WHERE PC.id_product = '.(int)$valor['product_id'].' AND PC.id_order = 0 LIMIT 1');
                    }
                }
                
                $query = "SELECT pc.id_product_code, pc.code, pc.id_order, c.id_customer, c.secure_key
                            FROM "._DB_PREFIX_."product_code pc
                            INNER JOIN "._DB_PREFIX_."orders o ON ( pc.id_order = o.id_order )
                            INNER JOIN "._DB_PREFIX_."customer c ON ( o.id_customer = c.id_customer )
                            AND pc.id_order = ".(int)$order->id;
                $codes = Db::getInstance()->executeS($query);

                foreach ( $codes as $code ) {
                    $codedecrypt = Encrypt::decrypt(Configuration::get('PS_FLUZ_CODPRO_KEY') , $code['code']);
                    $codeencrypt = Encrypt::encrypt($code['secure_key'] , $codedecrypt);

                    Db::getInstance()->execute("UPDATE "._DB_PREFIX_."product_code
                                                SET code = '".$codeencrypt."'
                                                WHERE id_product_code = ".$code['id_product_code']);
                }

                $product_list = $order->getProducts();
                $total_value = "";
                $virtual_product = true;
                $product_var_tpl_list = array();
                foreach ($product_list as $product) {
                    $image_url = "";    
                    $codeText = 'select code, id_product, pin_code FROM '._DB_PREFIX_.'product_code WHERE id_order = '.(int)$order->id.' AND id_product = '.$product['id_product'];
                    $rowCode = Db::getInstance()->executeS($codeText);
                    
                    $query = 'SELECT codetype FROM '._DB_PREFIX_.'product WHERE id_product = '.$product['id_product'];
                    $rowType = Db::getInstance()->getRow($query);
                    $code2 = $rowType["codetype"];

                    foreach ($rowCode AS $code){
                        $customer = new Customer($order->id_customer);
                        $codecrypt = Encrypt::decrypt($customer->secure_key , $code['code']);
                        if ($code2 == 2){
                           if($code['pin_code']==''){
                                $image_url .=  "<label>".$codecrypt."</label><br>";
                            }
                            else{
                                $image_url .=  "<label>".$codecrypt."-".$code['pin_code']."</label><br>";
                            }
                        }
                        else{
                            PaymentModule::consultcodebar($code['id_product'], $code['code']);
                            if (isset($_SERVER['HTTPS'])) {
                                    $image_url .=  "<label>".$codecrypt."-".$code['pin_code']."</label><br><img src='https://".Configuration::get('PS_SHOP_DOMAIN')."/upload/code-".$code['code'].".png'/><br>";
                            }
                            else{
                                $image_url .=  "<center><label>".$codecrypt."</label>-".$code['pin_code']."</center><br><img src='http://".Configuration::get('PS_SHOP_DOMAIN')."/upload/code-".$code['code'].".png'/><br>";
                            }
                        }
                    }
                    
                    $total_value .= "<label>".round($product['price_shop'])."</label><br>";
                    $price = Product::getPriceStatic((int)$product['id_product'], false, ($product['id_product_attribute'] ? (int)$product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int)$order->id_customer, (int)$order->id_cart, (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
                    $price_wt = Product::getPriceStatic((int)$product['id_product'], true, ($product['id_product_attribute'] ? (int)$product['id_product_attribute'] : null), 2, null, false, true, $product['cart_quantity'], false, (int)$order->id_customer, (int)$order->id_cart, (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});

                    $product_price = Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, 2) : $price_wt;

                    $query = 'SELECT description_short FROM '._DB_PREFIX_.'product_lang WHERE id_product = '.$product['id_product'];
                    $row = Db::getInstance()->getRow($query);
                    $desc = $row['description_short'];

                    $product_var_tpl = array(
                        'reference' => $product['reference'],
                        'name' => $product['product_name'].(isset($product['attributes']) ? ' - '.$product['attributes'] : ''),
                        'descripcion'=>$desc,
                        'image_code'=> $image_url,
                        'unit_price' => Tools::displayPrice($product_price, 1, false),
                        'price' => Tools::displayPrice($product_price * $product['product_quantity'], 1, false),
                        'quantity' => $product['product_quantity']
                    );

                    $product_var_tpl_list[] = $product_var_tpl;
                    // Check if is not a virutal product for the displaying of shipping
                    
                    if (!$product['is_virtual']) {
                        $virtual_product &= false;
                    }
                } // end foreach ($products)

                $product_list_txt = '';
                $product_list_html = '';
                if (count($product_var_tpl_list) > 0) {
                    $product_list_txt = Order::getEmailTemplateContent('order_conf_product_list.txt', Mail::TYPE_TEXT, $product_var_tpl_list);
                    $product_list_html = Order::getEmailTemplateContent('order_conf_product_list.tpl', Mail::TYPE_HTML, $product_var_tpl_list);
                }
                
                foreach ($order->getProducts() as &$product_cart){
                            
                    $point_p = floor($product_cart['points']);
                    $point_product .=  "<label>".$point_p."</label><br>";
                    $name_product .= "<label>".$product_cart['product_name']."</label><br>";
                    $expiration_product .= "<label>".$product_cart['expiration']."</label><br>";
                }
                
                $data = array(
                '{username}' => $customer->username,
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
                '{delivery_company}' => $delivery->company,
                '{delivery_firstname}' => $delivery->firstname,
                '{delivery_lastname}' => $delivery->lastname,
                '{delivery_address1}' => $delivery->address1,
                '{delivery_address2}' => $delivery->address2,
                '{delivery_city}' => $delivery->city,
                '{delivery_postal_code}' => $delivery->postcode,
                '{delivery_country}' => $delivery->country,
                '{delivery_state}' => $delivery->id_state ? $delivery_state->name : '',
                '{delivery_phone}' => ($delivery->phone) ? $delivery->phone : $delivery->phone_mobile,
                '{delivery_other}' => $delivery->other,
                '{invoice_company}' => $invoice->company,
                '{invoice_vat_number}' => $invoice->vat_number,
                '{invoice_firstname}' => $invoice->firstname,
                '{invoice_lastname}' => $invoice->lastname,
                '{invoice_address2}' => $invoice->address2,
                '{invoice_address1}' => $invoice->address1,
                '{invoice_city}' => $invoice->city,
                '{invoice_postal_code}' => $invoice->postcode,
                '{invoice_country}' => $invoice->country,
                '{invoice_state}' => $invoice->id_state ? $invoice_state->name : '',
                '{invoice_phone}' => ($invoice->phone) ? $invoice->phone : $invoice->phone_mobile,
                '{delivery_block_txt}' => PaymentModule::_getFormatedAddress2($delivery, "\n"),
                '{invoice_block_txt}' => PaymentModule::_getFormatedAddress2($invoice, "\n"),
                '{delivery_block_html}' => PaymentModule::_getFormatedAddress2($delivery, '<br />', array(
                    'firstname'    => '<span style="font-weight:bold;">%s</span>',
                    'lastname'    => '<span style="font-weight:bold;">%s</span>'
                )),    

                '{invoice_block_html}' => PaymentModule::_getFormatedAddress2($invoice, '<br />', array(
                'firstname'    => '<span style="font-weight:bold;">%s</span>',
                'lastname'    => '<span style="font-weight:bold;">%s</span>'
                )),
                '{delivery_company}' => $delivery->company,
                '{delivery_firstname}' => $delivery->firstname,
                '{delivery_lastname}' => $delivery->lastname,
                '{delivery_address1}' => $delivery->address1,
                '{delivery_address2}' => $delivery->address2,
                '{delivery_city}' => $delivery->city,
                '{delivery_postal_code}' => $delivery->postcode,
                '{delivery_country}' => $delivery->country,
                '{delivery_state}' => $delivery->id_state ? $delivery_state->name : '',
                '{delivery_phone}' => ($delivery->phone) ? $delivery->phone : $delivery->phone_mobile,
                '{delivery_other}' => $delivery->other,
                '{invoice_company}' => $invoice->company,
                '{invoice_vat_number}' => $invoice->vat_number,
                '{invoice_firstname}' => $invoice->firstname,
                '{invoice_lastname}' => $invoice->lastname,
                '{invoice_address2}' => $invoice->address2,
                '{invoice_address1}' => $invoice->address1,
                '{invoice_city}' => $invoice->city,
                '{invoice_postal_code}' => $invoice->postcode,
                '{invoice_country}' => $invoice->country,
                '{invoice_state}' => $invoice->id_state ? $invoice_state->name : '',
                '{invoice_phone}' => ($invoice->phone) ? $invoice->phone : $invoice->phone_mobile,
                '{invoice_other}' => $invoice->other,    
                '{date}' => Tools::displayDate(date('Y-m-d H:i:s'), null, 1),
                '{payment}' => Tools::substr($order->payment, 0, 32), 
                '{point_discount}' => Tools::displayPrice($order->total_discounts, 1, false),
                '{products}' => $product_list_html,
                '{points}' => $point_product,   
                '{name_product}' => $name_product, 
                '{expiration}' => $expiration_product,     
                //'{image}'=> $image_url,    
                '{products_txt}' => $product_list_txt,
                '{total_value}' => Tools::displayPrice($total_value),    
                '{total_paid}' => Tools::displayPrice($order->total_paid, 1, false),
                '{total_products}' => Tools::displayPrice(Product::getTaxCalculationMethod() == PS_TAX_EXC ? $order->total_products : $order->total_products_wt, 1, false),
                '{total_discounts}' => Tools::displayPrice($order->total_discounts, 1, false),
                '{total_shipping}' => Tools::displayPrice($order->total_shipping, 1, false),
                '{total_wrapping}' => Tools::displayPrice($order->total_wrapping, 1, false),
                '{total_tax_paid}' => Tools::displayPrice(($order->total_products_wt - $order->total_products) + ($order->total_shipping_tax_incl - $order->total_shipping_tax_excl), 1, false));
                
                
                if ((int)Configuration::get('PS_INVOICE') && $order->invoice_number) {
                $order_invoice_list = $order->getInvoicesCollection();
                Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => $order_invoice_list));
                $pdf = new PDF($order_invoice_list, PDF::TEMPLATE_INVOICE, $context->smarty);
                $file_attachement[0]['content'] = $pdf->render(false);
                $file_attachement[0]['name'] = Configuration::get('PS_INVOICE_PREFIX', (int)$order->id_lang, null, $order->id_shop).sprintf('%06d', $order->invoice_number).'.pdf';
                $file_attachement[0]['mime'] = 'application/pdf';
                } else {
                    $file_attachement = null;
                }
                
                $file = _PS_ROOT_DIR_ . '/procedimiento_en_datafono.pdf';
                $file_attachement[1]['content'] = file_get_contents($file);
                $file_attachement[1]['name'] = 'Procedimiento en datafono.pdf';
                $file_attachement[1]['mime'] = 'application/pdf';
                
                foreach ($order->getProducts() as &$product_name){
                    $name_product_subject .= " ".$product_name['product_name'].", ";
                }
                
                $template = 'order_conf';
                $prefix_template = '16-order_conf';
                
                $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'subject_mail WHERE name_template_mail ="'.$prefix_template.'"';
                $row_subject = Db::getInstance()->getRow($query_subject);
                $message_subject = $row_subject['subject_mail'].' '.''.$name_product_subject.'';
                
                $allinone_rewards = new allinone_rewards();
                $allinone_rewards->sendMail((int)$order->id_lang, $template, $allinone_rewards->getL($message_subject), $data, $customer->email, $customer->firstname.' '.$customer->lastname,$file_attachement);
        
                /*if (Validate::isEmail($customer->email)) {
                            Mail::Send(
                                (int)$order->id_lang,
                                'order_conf',
                                Mail::l('Order confirmation', (int)$order->id_lang),
                                $data,
                                $customer->email,
                                $customer->firstname.' '.$customer->lastname,
                                null,
                                null,
                                $file_attachement,
                                null, _PS_MAIL_DIR_, false, (int)$order->id_shop
                            );
                }*/
    }
    
    public function setCurrentState($id_order_state, $id_employee = 0)
    {
        if (empty($id_order_state)) {
            return false;
        }
        $history = new OrderHistory();
        $history->id_order = (int)$this->id;
        $history->id_employee = (int)$id_employee;
        $history->changeIdOrderState((int)$id_order_state, $this);
        $res = Db::getInstance()->getRow('
			SELECT `invoice_number`, `invoice_date`, `delivery_number`, `delivery_date`
			FROM `'._DB_PREFIX_.'orders`
			WHERE `id_order` = '.(int)$this->id);
        $this->invoice_date = $res['invoice_date'];
        $this->invoice_number = $res['invoice_number'];
        $this->delivery_date = $res['delivery_date'];
        $this->delivery_number = $res['delivery_number'];
        $this->update();
        
        if ( $id_order_state == 2 && $id_employee == 0 ) {
            $ordercodes = new Order((int)$this->id);
            $this->updateCodes($ordercodes);
        }

        $history->addWithemail();
    }
    
    public static function getEmailTemplateContent($template_name, $mail_type, $var)
    {
        $context = Context::getContext();
        $email_configuration = Configuration::get('PS_MAIL_TYPE');
        if ($email_configuration != $mail_type && $email_configuration != Mail::TYPE_BOTH) {
            return '';
        }

        $theme_template_path = _PS_THEME_DIR_.'mails'.DIRECTORY_SEPARATOR.$context->language->iso_code.DIRECTORY_SEPARATOR.$template_name;
        $default_mail_template_path = _PS_MAIL_DIR_.$context->language->iso_code.DIRECTORY_SEPARATOR.$template_name;

        if (Tools::file_exists_cache($theme_template_path)) {
            $default_mail_template_path = $theme_template_path;
        }

        if (Tools::file_exists_cache($default_mail_template_path)) {
            $context->smarty->assign('list', $var);
            return $context->smarty->fetch($default_mail_template_path);
        }
        return '';
    }
    
    public static function exportOrders( $date_from = "", $date_to = "" )
    {
        $sql = "SELECT * FROM "._DB_PREFIX_."report_orders";
        if ( $date_from != "" && $date_to != "" ) {
            $sql .= " WHERE fecha BETWEEN '".$date_from." 00:00:00' and '".$date_to." 23:59:59'";
        }
        $sql .= " ORDER BY orden DESC";
        
        $orders = Db::getInstance()->executeS($sql);
        
        $report = "<html>
                        <head>
                            <meta http-equiv=?Content-Type? content=?text/html; charset=utf-8? />
                        </head>
                            <body>
                                <table>
                                    <tr>
                                        <th>orden</th>
                                        <th>referencia</th> 
                                        <th>fecha</th>
                                        <!--th>cliente</th-->
                                        <th>usuario</th>
                                        <th>email</th>
                                        <th>nivel</th>
                                        <th>estado</th>
                                        <th>pago</th>
                                        <th>total</th>
                                        <th>pago_pesos</th>
                                        <th>pago_puntos</th>
                                        <th>puntos_utilizados</th>
                                        <th>nombre_producto</th>
                                        <th>estado_tarjeta</th>
                                        <th>valor_utilizado</th>
                                        <th>referencia_producto</th>
                                        <th>precio_producto</th>
                                        <th>costo_producto</th>
                                        <th>cantidad</th>
                                        <th>codigos_producto</th>
                                        <th>recompensa_porcentaje_producto</th>
                                        <th>recompensa_pesos_compra</th>
                                        <th>recompensa_puntos_compra</th>
                                        <th>recompensa_pesos_red</th>
                                        <th>recompensa_puntos_red</th>
                                        <th>recompensa_total_pesos</th>
                                        <th>recompensa_total_puntos</th>";
        for ($index = 0; $index <= 15; $index++) {
            $report .= "<th>usuario_nivel_".$index."</th>
                        <th>recompensa_pesos_nivel_".$index."</th>
                        <th>recompensa_puntos_nivel_".$index."</th>";
        }
        $report .= "</tr>";
        
        foreach ( $orders as $order ) {
            // CODIGOS PRODUCTO
            $sql = 'SELECT GROUP_CONCAT( CONCAT("**********",pc.last_digits) ) codigos_producto, pc.state AS estado_tarjeta, pc.price_card_used AS valor_utilizado
                    FROM '._DB_PREFIX_.'product_code pc
                    INNER JOIN '._DB_PREFIX_.'product p ON ( pc.id_product = p.id_product )
                    WHERE pc.id_order = '.$order['orden'].'
                    AND p.reference = "'.$order['referencia_producto'].'"';
            $codes_order = Db::getInstance()->executeS($sql);
            
            
            $report .= "<tr>
                            <td>".$order['orden']."</td>
                            <td>".$order['referencia']."</td>
                            <td>".$order['fecha']."</td>
                            <td>".$order['usuario']."</td>
                            <td>".$order['email']."</td>
                            <td>".$order['nivel']."</td>
                            <td>".$order['estado']."</td>
                            <td>".$order['pago']."</td>
                            <td>".$order['total']."</td>
                            <td>".$order['pago_pesos']."</td>
                            <td>".$order['pago_puntos']."</td>
                            <td>".$order['puntos_utilizados']."</td>
                            <td>".$order['nombre_producto']."</td>
                            <td>".$codes_order[0]['estado_tarjeta']."</td>
                            <td>".number_format($codes_order[0]['valor_utilizado'], 6, '.', '')."</td>
                            <td>".$order['referencia_producto']."</td>
                            <td>".$order['precio_producto']."</td>
                            <td>".$order['costo_producto']."</td>
                            <td>".$order['cantidad']."</td>
                            <td>".$codes_order[0]['codigos_producto']."</td>
                            <td>".$order['recompensa_porcentaje_producto']."</td>
                            <td>".$order['recompensa_pesos_compra']."</td>
                            <td>".$order['recompensa_puntos_compra']."</td>
                            <td>".$order['recompensa_pesos_red']."</td>
                            <td>".$order['recompensa_puntos_red']."</td>
                            <td>".$order['recompensa_total_pesos']."</td>
                            <td>".$order['recompensa_total_puntos']."</td>
                            <td>".$order['usuario_nivel_0']."</td>
                            <td>".$order['recompensa_pesos_nivel_0']."</td>
                            <td>".$order['recompensa_puntos_nivel_0']."</td>
                            <td>".$order['usuario_nivel_1']."</td>
                            <td>".$order['recompensa_pesos_nivel_1']."</td>
                            <td>".$order['recompensa_puntos_nivel_1']."</td>
                            <td>".$order['usuario_nivel_2']."</td>
                            <td>".$order['recompensa_pesos_nivel_2']."</td>
                            <td>".$order['recompensa_puntos_nivel_2']."</td>
                            <td>".$order['usuario_nivel_3']."</td>
                            <td>".$order['recompensa_pesos_nivel_3']."</td>
                            <td>".$order['recompensa_puntos_nivel_3']."</td>
                            <td>".$order['usuario_nivel_4']."</td>
                            <td>".$order['recompensa_pesos_nivel_4']."</td>
                            <td>".$order['recompensa_puntos_nivel_4']."</td>
                            <td>".$order['usuario_nivel_5']."</td>
                            <td>".$order['recompensa_pesos_nivel_5']."</td>
                            <td>".$order['recompensa_puntos_nivel_5']."</td>
                            <td>".$order['usuario_nivel_6']."</td>
                            <td>".$order['recompensa_pesos_nivel_6']."</td>
                            <td>".$order['recompensa_puntos_nivel_6']."</td>
                            <td>".$order['usuario_nivel_7']."</td>
                            <td>".$order['recompensa_pesos_nivel_7']."</td>
                            <td>".$order['recompensa_puntos_nivel_7']."</td>
                            <td>".$order['usuario_nivel_8']."</td>
                            <td>".$order['recompensa_pesos_nivel_8']."</td>
                            <td>".$order['recompensa_puntos_nivel_8']."</td>
                            <td>".$order['usuario_nivel_9']."</td>
                            <td>".$order['recompensa_pesos_nivel_9']."</td>
                            <td>".$order['recompensa_puntos_nivel_9']."</td>
                            <td>".$order['usuario_nivel_10']."</td>
                            <td>".$order['recompensa_pesos_nivel_10']."</td>
                            <td>".$order['recompensa_puntos_nivel_10']."</td>
                            <td>".$order['usuario_nivel_11']."</td>
                            <td>".$order['recompensa_pesos_nivel_11']."</td>
                            <td>".$order['recompensa_puntos_nivel_11']."</td>
                            <td>".$order['usuario_nivel_12']."</td>
                            <td>".$order['recompensa_pesos_nivel_12']."</td>
                            <td>".$order['recompensa_puntos_nivel_12']."</td>
                            <td>".$order['usuario_nivel_13']."</td>
                            <td>".$order['recompensa_pesos_nivel_13']."</td>
                            <td>".$order['recompensa_puntos_nivel_13']."</td>
                            <td>".$order['usuario_nivel_14']."</td>
                            <td>".$order['recompensa_pesos_nivel_14']."</td>
                            <td>".$order['recompensa_puntos_nivel_14']."</td>
                            <td>".$order['usuario_nivel_15']."</td>
                            <td>".$order['recompensa_pesos_nivel_15']."</td>
                            <td>".$order['recompensa_puntos_nivel_15']."</td>
                        </tr>";
        }
        $report .= "         </table>
                        </body>
                    </html>";
        header("Content-Type: application/vnd.ms-excel");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("content-disposition: attachment;filename=report_orders.xls");
        die($report);
    }
    
    public static function saveExportOrders()
    {
        $sql = "SELECT
                        o.id_order orden,
                        o.reference referencia,
                        c.id_customer,
                        CONCAT(c.firstname,' ',c.lastname) cliente,
                        c.username,
                        c.email,
                        osl.name estado,
                        o.payment pago,
                        od.total_price_tax_incl total,
                        IFNULL(ocr.value ,0)  / (SELECT COUNT(*) FROM "._DB_PREFIX_."order_detail WHERE id_order = o.id_order) pago_puntos,
                        o.date_add fecha,
                        od.product_name nombre_producto,
                        od.product_id id_product,
                        od.product_reference referencia_producto,
                        od.unit_price_tax_incl precio_producto,
                        od.product_quantity cantidad,
                        od.total_price_tax_incl total_producto,
                        od.porcentaje porcentaje_producto,
                        od.points puntos_producto,
                        ps.product_supplier_price_te costo_producto
                FROM ps_orders o
                INNER JOIN "._DB_PREFIX_."customer c ON ( o.id_customer = c.id_customer )
                INNER JOIN "._DB_PREFIX_."order_state_lang osl ON ( o.current_state = osl.id_order_state AND osl.id_lang = 1 )
                INNER JOIN "._DB_PREFIX_."order_detail od ON ( o.id_order = od.id_order )
                LEFT JOIN "._DB_PREFIX_."order_cart_rule ocr ON ( o.id_order = ocr.id_order )
                LEFT JOIN "._DB_PREFIX_."rewards_product rp ON ( od.product_id = rp.id_product )
                LEFT JOIN "._DB_PREFIX_."report_orders ro ON ( o.id_order = ro.orden )
                LEFT JOIN "._DB_PREFIX_."product_supplier ps ON ( od.product_id = ps.id_product )
                WHERE ro.orden IS NULL
                ORDER BY o.id_order DESC";

        $orders = Db::getInstance()->executeS($sql);

        foreach ( $orders as $order ) {
            // NIVEL USUARIO
            $sponsorships = RewardsSponsorshipModel::getSponsorshipAscendants($order['id_customer']);
            $sponsorships2 = array_slice($sponsorships, 1, 15);
            $nivel = count($sponsorships2);

            // CODIGOS PRODUCTO
            $sql = 'SELECT GROUP_CONCAT( CONCAT("**********",SUBSTRING(code,-4)) ) codigos_producto, 
                    state AS estado_tarjeta, price_card_used AS valor_utilizado
                    FROM '._DB_PREFIX_.'product_code
                    WHERE id_order = '.$order['orden'].'
                    AND id_product = '.$order['id_product'];
            $codes_order = Db::getInstance()->executeS($sql);
            
            // RECOMPENSA USUARIO
            $usuariopuntospesos = $order['puntos_producto'] * Configuration::get('REWARDS_VIRTUAL_VALUE_1');
            $usuariopuntos = $order['puntos_producto'];

            // RECOMPENSA RED
            if ( $order['porcentaje_producto'] != "1" ) {
                $redpuntospesos = $usuariopuntospesos * count($sponsorships2);
                $redpuntos = $usuariopuntos * count($sponsorships2);
            } else {
                $redpuntospesos = 0;
                $redpuntos = 0;
            }

            // USUARIOS RED
            $sql = 'SELECT c.id_customer, c.username
                    FROM '._DB_PREFIX_.'rewards r
                    INNER JOIN '._DB_PREFIX_.'customer c ON ( r.id_customer = c.id_customer )
                    WHERE r.id_order = '.$order['orden'];
            $sponsors_order = Db::getInstance()->executeS($sql);
            foreach ( $sponsors_order as &$sponsor_order ) {
                $sponsorships_order = RewardsSponsorshipModel::getSponsorshipAscendants($sponsor_order['id_customer']);
                $sponsorships_order_2 = array_slice($sponsorships_order, 1, 15);
                $sponsor_order['nivel'] = count($sponsorships_order_2);
            }
            usort($sponsors_order, function($a, $b) {
                return $a['nivel'] - $b['nivel'];
            });

            $queryInsertReport = "";
            $queryInsertReport = "INSERT INTO "._DB_PREFIX_."report_orders (orden, referencia, fecha, usuario, email, nivel, estado, pago, total, pago_pesos, pago_puntos, puntos_utilizados, nombre_producto, estado_tarjeta, valor_utilizado, referencia_producto, precio_producto, costo_producto, cantidad, codigos_producto, recompensa_porcentaje_producto, recompensa_pesos_compra, recompensa_puntos_compra, recompensa_pesos_red, recompensa_puntos_red, recompensa_total_pesos, recompensa_total_puntos, usuario_nivel_0, recompensa_pesos_nivel_0, recompensa_puntos_nivel_0, usuario_nivel_1, recompensa_pesos_nivel_1, recompensa_puntos_nivel_1, usuario_nivel_2, recompensa_pesos_nivel_2, recompensa_puntos_nivel_2, usuario_nivel_3, recompensa_pesos_nivel_3, recompensa_puntos_nivel_3, usuario_nivel_4, recompensa_pesos_nivel_4, recompensa_puntos_nivel_4, usuario_nivel_5, recompensa_pesos_nivel_5, recompensa_puntos_nivel_5, usuario_nivel_6, recompensa_pesos_nivel_6, recompensa_puntos_nivel_6, usuario_nivel_7, recompensa_pesos_nivel_7, recompensa_puntos_nivel_7, usuario_nivel_8, recompensa_pesos_nivel_8, recompensa_puntos_nivel_8, usuario_nivel_9, recompensa_pesos_nivel_9, recompensa_puntos_nivel_9, usuario_nivel_10, recompensa_pesos_nivel_10, recompensa_puntos_nivel_10, usuario_nivel_11, recompensa_pesos_nivel_11, recompensa_puntos_nivel_11, usuario_nivel_12, recompensa_pesos_nivel_12, recompensa_puntos_nivel_12, usuario_nivel_13, recompensa_pesos_nivel_13, recompensa_puntos_nivel_13, usuario_nivel_14, recompensa_pesos_nivel_14, recompensa_puntos_nivel_14, usuario_nivel_15, recompensa_pesos_nivel_15, recompensa_puntos_nivel_15)
                                  VALUES (
                                    ".$order['orden'].",
                                    '".$order['referencia']."',
                                    '".$order['fecha']."',
                                    '".$order['username']."',
                                    '".$order['email']."',
                                    ".$nivel.",
                                    '".$order['estado']."',
                                    '".$order['pago']."',
                                    ".number_format($order['total'], 6, '.', '').",
                                    ".number_format(($order['total'] - $order['pago_puntos']), 6, '.', '').",
                                    ".number_format($order['pago_puntos'], 6, '.', '').",
                                    ".number_format(($order['pago_puntos'] / Configuration::get('REWARDS_VIRTUAL_VALUE_1') ), 6, '.', '').",
                                    '".$order['nombre_producto']."',
                                    '".$codes_order[0]['estado_tarjeta']."', 
                                    ".number_format($codes_order[0]['valor_utilizado'], 6, '.', '').",
                                    '".$order['referencia_producto']."',
                                    ".number_format($order['precio_producto'], 6, '.', '').",
                                    ".number_format($order['costo_producto'], 6, '.', '').",
                                    ".number_format($order['cantidad'], 6, '.', '').",
                                    '".$codes_order[0]['codigos_producto']."'   ,
                                    ".number_format($order['porcentaje_producto'], 6, '.', '').",
                                    ".number_format($usuariopuntospesos, 6, '.', '').",
                                    ".number_format($usuariopuntos, 6, '.', '').",
                                    ".number_format($redpuntospesos, 6, '.', '').",
                                    ".number_format($redpuntos, 6, '.', '').",
                                    ".number_format(($usuariopuntospesos+$redpuntospesos), 6, '.', '').",
                                    ".number_format(($usuariopuntos+$redpuntos), 6, '.', '').",";
            
            $emptys = 15;
            foreach ($sponsors_order as $sponsor_order) {
                if ( $order['id_customer'] != $sponsor_order['id_customer'] ) {
                    $queryInsertReport .= "'".$sponsor_order['username']."',
                                          ".number_format($usuariopuntospesos, 6, '.', '').",
                                          ".number_format($usuariopuntos, 6, '.', '').",";
                    $emptys--;
                }
            }

            for ($i = 0; $i <= $emptys; $i++) {
                $queryInsertReport .= "null,null,null,";
            }

            $queryInsertReport = substr($queryInsertReport, 0, -1).")";

            Db::getInstance()->execute($queryInsertReport);
            
        }

            // ACTUALIZAR ESTADO DE ORDEN EN TABLA DE REPORTE
            Db::getInstance()->execute("UPDATE "._DB_PREFIX_."report_orders ro
                                    INNER JOIN "._DB_PREFIX_."orders o ON ( ro.orden = o.id_order )
                                    INNER JOIN "._DB_PREFIX_."order_state_lang osl ON ( o.current_state = osl.id_order_state AND osl.id_lang = 1 )
                                    SET ro.estado = osl.name");
            
            //ACTUALIZA EL ESTADO DE TARJETA Y VALOR UTILIZADO
            
            Db::getInstance()->execute("UPDATE "._DB_PREFIX_."report_orders ro
                                LEFT JOIN "._DB_PREFIX_."product_code pc ON (ro.orden = pc.id_order)
                                LEFT JOIN "._DB_PREFIX_."product p ON (pc.id_product = p.id_product)
                                SET ro.valor_utilizado = pc.price_card_used, ro.estado_tarjeta = pc.state
                                WHERE ro.orden = pc.id_order AND ro.referencia_producto = p.reference");
    }
}

?>
