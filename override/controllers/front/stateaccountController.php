<?php

include_once('./modules/allinone_rewards/allinone_rewards.php');
//require_once(_PS_MODULE_DIR_.'/allinone_rewards/allinone_rewards.php');

class stateaccountController extends FrontController {

    public function setMedia()
    {
        FrontController::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'my-account-state.css');
    }
    
    public function initContent() {
        parent::initContent();
        
        $username = $this->context->customer->username;
        $email = $this->context->customer->email;
        $id_customer = $this->context->customer->id;
        $tree = RewardsSponsorshipModel::_getTree($id_customer);
        $tam_network = (count($tree)-1);
        $lastPoint = $this->getPointsLastDays($id_customer);
        $num_orders = $this->numOrders($id_customer);
        $topPoint = $this->TopNetworkUnique();
        $val_fluz_net = round(RewardsModel::getMoneyReadyForDisplay($lastPoint['points'], (int)$this->context->currency->id));
        $val_fluz_user = round(RewardsModel::getMoneyReadyForDisplay($num_orders['points'], (int)$this->context->currency->id));
        $val_fluz_top = round(RewardsModel::getMoneyReadyForDisplay($topPoint[0]['points'], (int)$this->context->currency->id));
        $last_num_account = $this->newMembersLastMonth();
        
        $this->context->smarty->assign(array(
           
            'username' => $username,
            'numero_de_cuenta'=> $last_num_account,
            'id_customer' => $id_customer,
            'tam_network' => $tam_network,
            'lastPoint' => $lastPoint,
            'num_orders' => $num_orders,
            'topPoint'=> $topPoint,
             
         ));
        
        $mail_vars = array(
          
            '{username}' => $username,
            '{id_customer}' => $id_customer,
            '{numero_de_cuenta}'=> $last_num_account,
            '{tamano_de_network}' => $tam_network,
            '{fluz_ganada}' => $lastPoint['points'],
            '{cop_valor_de_fluz}' =>Tools::displayPrice($val_fluz_net, $this->context->currency, false),
            '{nombre_de_pedidos}' => $num_orders['orders'],
            '{bonos_compradas}' => $num_orders['num_bonos'],
            '{valor_tienda_bonos}' => Tools::displayPrice($num_orders['price'], $this->context->currency, false),
            '{fech_estado_cuenta}' => $num_orders['fecha_actual'],
            '{fluz_ganada_user}' => round($num_orders['points']),
            '{cop_valor_de_fluz_user}' =>Tools::displayPrice($val_fluz_user, $this->context->currency, false),
            '{mejor_fluz_ganada}' => round($topPoint[0]['points']),
            '{mejor_cop_valor_de_fluz}' => Tools::displayPrice($val_fluz_top, $this->context->currency, false),
        );
        
        if (Tools::isSubmit('submitMailAccount')) {
            
            $template = 'monthly-balance-summary';
            $prefix_template = '16-monthly-balance-summary';
            
            $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
            $row_subject = Db::getInstance()->getRow($query_subject);
            $message_subject = $row_subject['subject_mail'];
            
            $allinone_rewards = new allinone_rewards();
            $allinone_rewards->sendMail(1, $template, $allinone_rewards->getL($message_subject), $mail_vars, $email, $this->context->customer->firstname.' '.$this->context->customer->lastname);
        
        }
        $this->setTemplate(_PS_THEME_DIR_.'stateAccount.tpl');
    }
    
    public function getPointsLastDays($id_customer){
     
                $queryTop = 'SELECT ROUND(SUM(n.credits)) AS points
                            FROM ps_rewards n 
                            LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = n.id_customer) 
                            LEFT JOIN '._DB_PREFIX_.'order_detail s ON (s.id_order = n.id_order) 
                            WHERE n.id_customer='.$id_customer.'    
                            AND s.product_reference NOT LIKE "MFLUZ%" AND n.date_add >= curdate() + interval -30 day
                            AND n.id_reward_state = 2 AND n.credits > 0';
                
                $result = Db::getInstance()->getRow($queryTop);
                
            return $result;    
        }
    
    public function numOrders($id_customer){
     
                $queryTop = 'SELECT COUNT(o.id_order) AS orders, 
                            SUM(od.product_quantity) AS num_bonos,
                            SUM(r.credits) AS points,
                            SUM(od.product_price) AS price,
                            CURDATE() as fecha_actual
                            FROM '._DB_PREFIX_.'orders o 
                            LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            LEFT JOIN '._DB_PREFIX_.'rewards r ON (o.id_order = r.id_order)
                            WHERE o.id_customer = '.$id_customer.' 
                            AND o.date_add >= curdate() + interval -30 day
                            AND o.current_state = 2
                            AND r.credits > 0
                            AND r.plugin = "loyalty" ';
                
                $result = Db::getInstance()->getRow($queryTop);
                
            return $result;    
        }
    public function TopNetworkUnique() {
            
            $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
            foreach ($tree as $valor){
                $queryTop = 'SELECT c.username AS username, s.product_reference AS reference, c.firstname AS name, c.lastname AS lastname, SUM(n.credits) AS points
                            FROM '._DB_PREFIX_.'rewards n 
                            LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = n.id_customer) 
                            LEFT JOIN '._DB_PREFIX_.'order_detail s ON (s.id_order = n.id_order) WHERE n.id_customer='.$valor['id'].'
                            AND s.product_reference NOT LIKE "MFLUZ%" AND n.credits > 0 AND '.$valor['level'].'!=0';
                $result = Db::getInstance()->executeS($queryTop);
                
                if ($result[0]['points'] != "" ) {
                    $top[] = $result[0];
                }                
            }
            usort($top, function($a, $b) {
                return $b['points'] - $a['points'];
            });
            
            return array_slice($top, 0, 1);    
            
    }

    public function newMembersLastMonth() {
        $ids = "";
        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
        foreach ($tree as $valor){
            $ids .= $valor['id'].",";
        }

        $queryTop = 'SELECT COUNT(*)
                     FROM '._DB_PREFIX_.'rewards_sponsorship rs 
                     WHERE rs.date_add >= curdate() + interval -30 day  
                     AND rs.id_customer IN ('.substr($ids, 0, -1).')';
        $result = Db::getInstance()->getValue($queryTop);
    
        return $result;
    }
    
}
