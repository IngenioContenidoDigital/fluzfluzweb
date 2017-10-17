<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once ('.override/controllers/admin/AdminCartsController.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/allinone_rewards.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsSponsorshipModel.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsModel.php');

$context = Context::getContext();

/* Red Usuario hacia abajo */
$tree = RewardsSponsorshipModel::_getTree($context->customer->id);

/*Tamaño de la red*/
$tam_network = (count($tree)-1);

/* Numero de ordenes en el ultimo mes*/
$orders_month = stateaccountController::numOrders($context->customer->id);

/*Nuevos miembros ultimos 30 dias*/
$result_month_user = stateaccountController::newMembersLastMonth($context->customer->id);

/* Puntos Ganados ultimo mes*/
$fluz_ganadas_month = stateaccountController::getPointsLastDays($context->customer->id);
/* Dinero en COP Ganados en el ultimo mes*/
$val_fluz_net = round(RewardsModel::getMoneyReadyForDisplay($fluz_ganadas_month['points'], 1));

/* MEJOR RENDIMIENTO EN LA NETWORK */
$top_member = stateaccountController::TopNetworkUnique($context->customer->id);
$val_fluz_top = round(RewardsModel::getMoneyReadyForDisplay($top_member[0]['points'], 1));

$mail_vars = array(
          
            '{username}' => $context->customer->username,
            '{id_customer}' => $context->customer->id,
            '{numero_de_cuenta}'=> $result_month_user,
            '{tamano_de_network}' => $tam_network,
            '{fluz_ganada}' => $fluz_ganadas_month['points'],
            '{cop_valor_de_fluz}' =>Tools::displayPrice($val_fluz_net,1, false),
            '{nombre_de_pedidos}' => $orders_month['orders'],
            '{bonos_compradas}' => $orders_month['num_bonos'],
            '{valor_tienda_bonos}' => Tools::displayPrice($orders_month['price'], 1, false),
            '{fech_estado_cuenta}' => $orders_month['fecha_actual'],
            '{mejor_fluz_ganada}' => round($top_member[0]['points']),
            '{mejor_cop_valor_de_fluz}' => Tools::displayPrice($val_fluz_top, 1, false),
        );
        
            $template = 'monthly-balance-summary';
            $prefix_template = '16-monthly-balance-summary';
            
            $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
            $row_subject = Db::getInstance()->getRow($query_subject);
            $message_subject = $row_subject['subject_mail'];
            
            $allinone_rewards = new allinone_rewards();
            $allinone_rewards->sendMail(1, $template, $allinone_rewards->getL($message_subject), $mail_vars, $context->customer->email, $context->customer->firstname.' '.$context->customer->lastname);

?>