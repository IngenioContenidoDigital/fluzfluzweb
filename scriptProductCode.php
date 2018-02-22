<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once ('.override/controllers/admin/AdminCartsController.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/allinone_rewards.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsSponsorshipModel.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsModel.php');

$query_list_product = "SELECT  pc.id_product_code, pc.id_product, pl.`name`, pc.`code`, pc.last_digits, pc.state, pc.date_expiration, pc.no_lote , DATEDIFF(pc.date_expiration, NOW()) as Dias_Vencimiento, 
    ROUND(((DATEDIFF(pc.date_expiration, NOW()))/30), 1) as Mes_Vencimiento,
    CASE 1 WHEN (DATEDIFF(pc.date_expiration, NOW()) < 91 AND DATEDIFF(pc.date_expiration, NOW()) > 16) THEN 'Proximo a vencer'
    WHEN (DATEDIFF(pc.date_expiration, NOW()) < 16 ) THEN 'Vencimiento en menos de 16 dias' 
    ELSE 'Estado bien' END AS Estado
    FROM ps_product_code pc
    INNER JOIN ps_product p ON ( pc.id_product = p.id_product )
    LEFT JOIN ps_product_lang pl ON (pl.id_product = pc.id_product)
    WHERE pl.id_lang = 1 AND pc.state = 'Disponible' AND pc.id_order = 0 AND p.active = 1 AND (DATEDIFF(pc.date_expiration, NOW()) < 91 )";

$list_product = DB::getInstance()->executeS($query_list_product);

$report = "<html>
                <head>
                    <meta http-equiv=?Content-Type? content=?text/html; charset=utf-8? />
                </head>
                    <body>
                        <table>
                            <tr>
                                <th>Id Product Code</th>
                                <th>Id Product</th>
                                <th>Nombre Producto</th>
                                <th>Codigo Producto</th>
                                <th>Ultimos Digitos Codigo</th>
                                <th>Estado Codigo</th>
                                <th>Fecha Vencimiento Producto</th>
                                <th>Numero de Lote</th>
                                <th>Dias para Vencimiento</th>
                                <th>Meses para Vencimiento</th>
                                <th>Estado Codigo de Producto</th>";

    $report .= "</tr>";

    foreach ($list_product as $data)
        {
            $report .= "<tr>
                    <td>".$data['id_product_code']."</td>
                    <td>".$data['id_product']."</td>
                    <td>".$data['name']."</td>
                    <td>".$data['code']."</td>
                    <td>".$data['last_digits']."</td>
                    <td>".$data['state']."</td>
                    <td>".$data['date_expiration']."</td>
                    <td>".$data['no_lote']."</td>    
                    <td>".$data['Dias_Vencimiento']."</td>
                    <td>".$data['Mes_Vencimiento']."</td>
                    <td>".$data['Estado']."</td>";
        }
    $report .= "         </table>
                </body>
            </html>";    
    
$file_attachement['content'] = $report;
$file_attachement['name'] = 'product_code.xls';
$file_attachement['mime'] = 'application/vnd.ms-excel';
 
$template = 'product_code';

$list_mail[0] = array(firstname => 'Ricardo', lastname => '', email => 'ricardo@fluzfluz.com');
$list_mail[1] = array(firstname => 'Eric', lastname => 'Johnson', email => 'info@fluzfluz.com');

foreach ($list_mail as $customer){
                
        $vars = array(
                    '{firstname}' => $customer['firstname'],
                    '{lastname}' => $customer['lastname'],
                    '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                    '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                    '{shop_url_personal}' => Context::getContext()->link->getPageLink('identity', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                    '{learn_more_url}' => "http://reglas.fluzfluz.co",
                    );

        $allinone_rewards = new allinone_rewards();
        $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL('Archivo Excel Vencimiento Codigos de Producto'),$vars, $customer['email'], $customer['firstname'].' '.$customer['lastname'],$file_attachement);
    }
?>
