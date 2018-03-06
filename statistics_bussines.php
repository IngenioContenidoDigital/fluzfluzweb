<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once('./modules/allinone_rewards/allinone_rewards.php');

$query = "SELECT id_customer, username, firstname, lastname, email, date_add, field_work, group_business
            FROM "._DB_PREFIX_."customer
            WHERE id_default_group = 5
            AND active = 1
            AND kick_out = 0";

$business = Db::getInstance()->executeS($query);

// echo '<pre>'; print_r($business); die();

foreach ( $business as $key => $company ) {
    $tree = array();
    $list_business = array();
    $net_business = array();
    $network2 = array();
    $statistics = array();
    $customer_qty = array();
    $fluz = array();
    $orders_qty = array();
    $categories = array();
    $manufacturers = array();
    $customers_qty_acu = 0;
    $ids = "";
    
    
    
    // NETWORK
    $tree = RewardsSponsorshipModel::_getTree($company['id_customer']);
    array_shift($tree);

    foreach ($tree as &$network) {
        $sql = 'SELECT id_customer, firstname, lastname, phone, username, email, dni, field_work, group_business
                FROM '._DB_PREFIX_.'customer 
                WHERE id_customer =' . $network['id'];
        $row_sql = Db::getInstance()->getRow($sql);

        $network['id_customer'] = $row_sql['id_customer'];
        $network['firstname'] = $row_sql['firstname'];
        $network['lastname'] = $row_sql['lastname'];
        $network['email'] = $row_sql['email'];
        $network['phone'] = $row_sql['phone'];
        $network['dni'] = $row_sql['dni'];
        $network['username'] = $row_sql['username'];
        $network['field_work'] = $row_sql['field_work'];
        $network['group_business'] = $row_sql['group_business'];
    }

    $employee_b = Db::getInstance()->executeS('SELECT id_customer, firstname, lastname, phone, email, dni, username, field_work FROM ps_customer WHERE field_work = "' . $company['field_work'] . '" AND id_customer !=' . $company['id_customer']);
    $net_business = array_merge($tree, $employee_b);

    foreach ($net_business as &$val) {
        if ($val['username'] != "" && $val['group_business'] != "" && $val['group_business'] == $company['group_business']) {
                $list_business[$val['id_customer']] = $val;
        }      
        else if ($val['username'] != "" && $val['field_work'] != "" && $val['field_work'] == $company['field_work']) {
                    $list_business[$val['id_customer']] = $val;
                }
    }
    
    $network2 = array_values($list_business);
    
    
    
    // STATISTICS
    foreach ($network2 as $customer) {
        $ids = $ids.$customer['id_customer'].",";
    }
    $ids = substr( $ids, 0, -1 );

    $fechaingreso = new DateTime($company['date_add']);
    $fechaactual = new DateTime();
    $diferencia = $fechaingreso->diff($fechaactual);
    $meses = ( $diferencia->y * 12 ) + $diferencia->m + 1;
    $meses = $meses >= 12 ? 12 : $meses;

    for ($i = 0 ; $i <= $meses ; $i++) {
        $date_sta = date("Y-m",strtotime("-".$i." month"));

        $query = "SELECT DATE_FORMAT(date_add,'%Y-%m') date, COUNT(id_customer) customers
                    FROM "._DB_PREFIX_."customer
                    WHERE id_customer IN (".$ids.")
                    GROUP BY date
                    HAVING date = '".$date_sta."'";
        $customer_qty = Db::getInstance()->getRow($query);

        $query = "SELECT DATE_FORMAT(o.date_add,'%Y-%m') date, SUM(r.credits) fluz, SUM(r.credits)*c.value fluzcop
                    FROM "._DB_PREFIX_."orders o
                    INNER JOIN "._DB_PREFIX_."rewards r ON ( o.id_order = r.id_order AND r.id_reward_state = 2 AND r.credits > 0 )
                    LEFT JOIN "._DB_PREFIX_."configuration c ON ( c.name = 'REWARDS_VIRTUAL_VALUE_1' )
                    WHERE o.id_customer IN (".$ids.")
                    AND o.current_state = 2
                    GROUP BY date
                    HAVING date = '".$date_sta."'";
        $fluz = Db::getInstance()->getRow($query);

        $query = "SELECT DATE_FORMAT(date_add,'%Y-%m') date, COUNT(id_order) orders
                    FROM "._DB_PREFIX_."orders
                    WHERE id_customer IN (".$ids.")
                    AND current_state = 2
                    GROUP BY date
                    HAVING date = '".$date_sta."'";
        $orders_qty = Db::getInstance()->getRow($query);

        $query = "SELECT SUM(od.product_price) total, COUNT(DISTINCT o.id_order) orders, c.name category
                    FROM "._DB_PREFIX_."orders o
                    INNER JOIN "._DB_PREFIX_."order_detail od ON ( o.id_order = od.id_order )
                    INNER JOIN "._DB_PREFIX_."product p ON ( od.product_id = p.id_product )
                    INNER JOIN "._DB_PREFIX_."category_lang c ON ( p.id_category_default = c.id_category AND c.id_lang = 1 )
                    WHERE o.id_customer IN (".$ids.")
                    AND o.current_state = 2
                    AND DATE_FORMAT(o.date_add,'%Y-%m') = '".$date_sta."'
                    GROUP BY category
                    ORDER BY orders DESC
                    LIMIT 10";
        $categories = Db::getInstance()->executeS($query);

        $query = "SELECT SUM(od.product_quantity) qty, m.name manufacturer, m.id_manufacturer, c.id_category, c.link_rewrite
                    FROM "._DB_PREFIX_."orders o
                    INNER JOIN "._DB_PREFIX_."order_detail od ON ( o.id_order = od.id_order )
                    INNER JOIN "._DB_PREFIX_."product p ON ( od.product_id = p.id_product )
                    LEFT JOIN "._DB_PREFIX_."manufacturer m ON ( p.id_manufacturer = m.id_manufacturer )
                    LEFT JOIN "._DB_PREFIX_."category_lang c ON ( m.category = c.id_category AND c.id_lang = 1 )
                    WHERE o.id_customer IN (".$ids.")
                    AND o.current_state = 2
                    AND DATE_FORMAT(o.date_add,'%Y-%m') = '".$date_sta."'
                    GROUP BY manufacturer
                    ORDER BY qty DESC
                    LIMIT 5";
        $manufacturers = Db::getInstance()->executeS($query);

        $date_arr = explode("-",$date_sta);
        $month = $date_arr[1];
        $year = $date_arr[0];
        switch ($date_arr[1]) {
            case "01": $month = "Enero"; break;
            case "02": $month = "Febrero"; break;
            case "03": $month = "Marzo"; break;
            case "04": $month = "Abril"; break;
            case "05": $month = "Mayo"; break;
            case "06": $month = "Junio"; break;
            case "07": $month = "Julio"; break;
            case "08": $month = "Agosto"; break;
            case "09": $month = "Septiembre"; break;
            case "10": $month = "Octubre"; break;
            case "11": $month = "Noviembre"; break;
            case "12": $month = "Diciembre"; break;
        }

        $statistics[$date_sta]['year'] = $year;
        $statistics[$date_sta]['month'] = $month;
        $statistics[$date_sta]['customers'] = $customer_qty['customers']=="" ? 0 : $customer_qty['customers'];
        $statistics[$date_sta]['fluz'] = $fluz['fluz']=="" ? 0 : $fluz['fluz'];
        $statistics[$date_sta]['fluzcop'] = $fluz['fluzcop']=="" ? 0 : $fluz['fluzcop'];
        $statistics[$date_sta]['orders'] = $orders_qty['orders']=="" ? 0 : $orders_qty['orders'];
        $statistics[$date_sta]['categories'] = $categories;
        $statistics[$date_sta]['manufacturers'] = $manufacturers;
    }

    $customers_qty_acu = 0;
    $reversed = array_reverse($statistics);
    foreach ($reversed as &$data) {
        $data['customers'] = $customers_qty_acu += $data['customers'];
    }

    $statistics = array_reverse($reversed);
    $statistics = reset($statistics);
    
    if ( !empty($statistics['manufacturers']) ) {
        $featured_merchants = "<table style='text-align: center;'><tr>";
        foreach ( $statistics['manufacturers'] as $manufacturer ) {
            $featured_merchants .= "<td>
                                        <a href=".Context::getContext()->link->getCategoryLink($manufacturer['id_category'], $manufacturer['link_rewrite'])." title=".$manufacturer['manufacturer'].">
                                            <img src="._S3_PATH_."m/".$manufacturer['id_manufacturer'].".jpg alt=".$manufacturer['manufacturer']." title=".$manufacturer['manufacturer']." style='width: 100%; max-width: 150px;' />
                                        </a>
                                    </td>";
        }
        $featured_merchants .= "</tr></table>";
    } else {
        $featured_merchants = "Ninguno";
    }
    
    if ( !empty($statistics['categories']) ) {
        $featured_categories = "<table style='text-align: center;'>
                                    <tr style='font-weight: bold;'>
                                        <td style='border-bottom: 1px solid #C9B197; padding: 5px 0;'>Categoria</td>
                                        <td style='border-bottom: 1px solid #C9B197; padding: 5px 0;'># Ordenes</td>
                                        <td style='border-bottom: 1px solid #C9B197; padding: 5px 0;'>Total</td>
                                    </tr>";
        foreach ( $statistics['categories'] as $category ) {
            $featured_categories .= "<tr>
                                        <td style='border-bottom: 1px solid #C9B197; padding: 5px 0;'>".$category['category']."</td>
                                        <td style='border-bottom: 1px solid #C9B197; padding: 5px 0;'>".$category['orders']."</td>
                                        <td style='border-bottom: 1px solid #C9B197; padding: 5px 0;'>COP $ ".round($category['total'])."</td>
                                    </tr>"; 
        }
        $featured_categories .= "</table>";
    } else {
        $featured_categories = "Ninguno";
    }
    
    $vars = array(
        '{username}' => $company['username'],
        '{year}' => $statistics['year'],
        '{month}' => $statistics['month'],
        '{customers}' => $statistics['customers'],
        '{fluz}' => round($statistics['fluz']),
        '{fluzcop}' => "COP $ ".$statistics['fluzcop'],
        '{orders}' => $statistics['orders'],
        '{featured_merchants}' => $featured_merchants,
        '{featured_categories}' => $featured_categories,
        '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
        '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id)
    );
    
    $file_attachement = array();
    $template = 'statistics_bussines';
    $prefix_template = '16-statistics_bussines';

    $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
    $row_subject = Db::getInstance()->getRow($query_subject);
    $message_subject = $row_subject['subject_mail'];
    
    $allinone_rewards = new allinone_rewards();
    $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject), $vars, $company['email'], $company['username'], $file_attachement);
}