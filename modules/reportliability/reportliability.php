<?php

if (!defined('_PS_VERSION_'))
    exit;

class reportliability extends ModuleGrid
{
    private $html;
    private $query;
    private $columns;

    public function __construct()
    {
        $this->name = 'reportliability';
        $this->tab = 'analytics_stats';
        $this->version = '1.0';
        $this->author = 'Ingenio Contenido Digital SAS';

        parent::__construct();

        $this->columns = array(
            array(
                'id' => 'id_customer',
                'header' => $this->l('Id Customer'),
                'dataIndex' => 'id_customer',
                'align' => 'center'
            ),
            array(
                'id' => 'username',
                'header' => $this->l('Usuario'),
                'dataIndex' => 'username',
                'align' => 'center'
            ),
            array(
                'id' => 'firstname',
                'header' => $this->l('Nombre'),
                'dataIndex' => 'firstname',
                'align' => 'center'
            ),
            array(
                'id' => 'lastname',
                'header' => $this->l('Apellido'),
                'dataIndex' => 'lastname',
                'align' => 'center'
            ),
            array(
                'id' => 'email',
                'header' => $this->l('Email'),
                'dataIndex' => 'email',
                'align' => 'center'
            ),
            array(
                'id' => 'dni',
                'header' => $this->l('DNI'),
                'dataIndex' => 'dni',
                'align' => 'center'
            ),
            array(
                'id' => 'fluz_granted_COP',
                'header' => $this->l('Fluz Granted COP'),
                'dataIndex' => 'fluz_granted_COP',
                'align' => 'center'
            ),
            array(
                'id' => 'fluz_granted',
                'header' => $this->l('Fluz Granted'),
                'dataIndex' => 'fluz_granted',
                'align' => 'center'
            ),
            array(
                'id' => 'fluz_sponsorship_COP',
                'header' => $this->l('Fluz Sponsorship COP'),
                'dataIndex' => 'fluz_sponsorship_COP',
                'align' => 'center'
            ),
            array(
                'id' => 'fluz_sponsorship',
                'header' => $this->l('Fluz Sponsorship'),
                'dataIndex' => 'fluz_sponsorship',
                'align' => 'center'
            ),
            array(
                'id' => 'transfer_in_COP',
                'header' => $this->l('Transfer In COP'),
                'dataIndex' => 'transfer_in_COP',
                'align' => 'center'
            ),
            array(
                'id' => 'transfer_in',
                'header' => $this->l('Transfer In'),
                'dataIndex' => 'transfer_in',
                'align' => 'center'
            ),
            array(
                'id' => 'transfer_out_COP',
                'header' => $this->l('Transfer Out COP'),
                'dataIndex' => 'transfer_out_COP',
                'align' => 'center'
            ),
            array(
                'id' => 'transfer_out',
                'header' => $this->l('Transfer Out'),
                'dataIndex' => 'transfer_out',
                'align' => 'center'
            ),
            array(
                'id' => 'cash_out_fluz_COP',
                'header' => $this->l('Cash Out Fluz COP'),
                'dataIndex' => 'cash_out_fluz_COP',
                'align' => 'center'
            ),
            array(
                'id' => 'cash_out_fluz',
                'header' => $this->l('Cash Out Fluz'),
                'dataIndex' => 'cash_out_fluz',
                'align' => 'center'
            ),
            array(
                'id' => 'fluz_spent_COP',
                'header' => $this->l('Fluz Spent COP'),
                'dataIndex' => 'fluz_spent_COP',
                'align' => 'center'
            ),
            array(
                'id' => 'fluz_spent',
                'header' => $this->l('Fluz Spent'),
                'dataIndex' => 'fluz_spent',
                'align' => 'center'
            ),
            array(
                'id' => 'fluz_last_month_COP',
                'header' => $this->l('Fluz Last Month COP'),
                'dataIndex' => 'fluz_last_month_COP',
                'align' => 'center'
            ),
            array(
                'id' => 'fluz_last_month',
                'header' => $this->l('Fluz Last Month'),
                'dataIndex' => 'fluz_last_month',
                'align' => 'center'
            ),
            array(
                'id' => 'fluz_current_COP',
                'header' => $this->l('Fluz Current COP'),
                'dataIndex' => 'fluz_current_COP',
                'align' => 'center'
            ),
            array(
                'id' => 'fluz_current',
                'header' => $this->l('Fluz Current'),
                'dataIndex' => 'fluz_current',
                'align' => 'center'
            ),
        );

        $this->displayName = $this->l('Reporte Liability');
        $this->description = $this->l('Reporte Liability');
    }

    public function install()
    {
        return (parent::install() &&
            $this->registerHook('AdminStatsModules'));
    }

    public function getData()
    {
        $date_between = $this->getDate();
        $date_last_month = substr($date_between, -22);
        
        $rewards_value = Configuration::get("REWARDS_VIRTUAL_VALUE_1");
        
        $this->query = "SELECT
                            c.id_customer,
                            c.username,
                            c.firstname,
                            c.lastname,
                            c.email,
                            c.dni,
                            IFNULL(fluz_granted*".$rewards_value.",0) AS fluz_granted_COP,
                            IFNULL(fluz_granted,0) AS fluz_granted,
                            IFNULL(fluz_sponsorship*".$rewards_value.",0) AS fluz_sponsorship_COP, 
                            IFNULL(fluz_sponsorship,0) AS fluz_sponsorship,
                            IFNULL(transfer_in*".$rewards_value.",0) AS transfer_in_COP,
                            IFNULL(transfer_in,0) AS transfer_in,
                            IFNULL(transfer_out*".$rewards_value.",0) AS transfer_out_COP,
                            IFNULL(transfer_out,0) AS transfer_out,
                            IFNULL(cash_out_fluz*".$rewards_value.",0) AS cash_out_fluz_COP,
                            IFNULL(cash_out_fluz,0) AS cash_out_fluz,
                            IFNULL(fluz_spent*".$rewards_value.",0) AS fluz_spent_COP,
                            IFNULL(fluz_spent,0) AS fluz_spent,
                            IFNULL(fluz_current*".$rewards_value.",0) AS fluz_current_COP,
                            IFNULL(fluz_current,0) AS fluz_current,
                            IFNULL(fluz_last_month*".$rewards_value.",0) AS fluz_last_month_COP,
                            IFNULL(fluz_last_month,0) AS fluz_last_month
                        FROM ps_customer AS c
                        LEFT JOIN (
                            SELECT r.id_customer, SUM(r.credits) AS fluz_granted
                            FROM ps_rewards AS r 
                            WHERE r.credits>=0 AND r.id_reward_state=2 AND r.`plugin`='loyalty' AND r.date_add BETWEEN ".$date_between." AND r.id_transfer_fluz IS NULL AND r.id_cashout IS NULL
                            GROUP BY r.id_customer 
                        ) AS fluz_granted ON fluz_granted.id_customer=c.id_customer
                        LEFT JOIN (
                            SELECT r1.id_customer, SUM(r1.credits) AS fluz_sponsorship 
                            FROM ps_rewards AS r1 
                            WHERE r1.credits>=0 AND r1.id_reward_state=2 AND r1.`plugin`='sponsorship' AND r1.date_add BETWEEN ".$date_between." AND r1.id_transfer_fluz IS NULL AND r1.id_cashout IS NULL
                            GROUP BY r1.id_customer
                        ) AS fluz_network ON fluz_network.id_customer=c.id_customer 
                        LEFT JOIN (
                            SELECT r2.id_customer, SUM(r2.credits) AS transfer_in
                            FROM ps_rewards AS r2 
                            WHERE r2.credits>=0 AND r2.id_reward_state=2 AND r2.date_add BETWEEN ".$date_between." AND r2.id_transfer_fluz > 0
                            GROUP BY r2.id_customer
                        ) AS transfer_in ON transfer_in.id_customer=c.id_customer 
                        LEFT JOIN (
                            SELECT r5.id_customer, ABS(SUM(r5.credits)) AS transfer_out
                            FROM ps_rewards AS r5 
                            WHERE r5.credits<0 AND r5.id_reward_state=2 AND r5.date_add BETWEEN ".$date_between." AND r5.id_transfer_fluz > 0
                            GROUP BY r5.id_customer
                        ) AS transfer_out ON transfer_out.id_customer=c.id_customer 
                        LEFT JOIN (
                            SELECT r6.id_customer, ABS(SUM(r6.credits)) AS cash_out_fluz
                            FROM ps_rewards AS r6 
                            WHERE r6.id_reward_state=2 AND r6.id_cashout IS NOT NULL AND r6.date_upd BETWEEN ".$date_between." 
                            GROUP BY r6.id_customer
                        ) AS cash_out ON cash_out.id_customer=c.id_customer 
                        LEFT JOIN (
                            SELECT r3.id_customer, SUM(r3.credits) AS fluz_last_month
                            FROM ps_rewards AS r3 
                            WHERE r3.id_reward_state=2 AND r3.date_add BETWEEN DATE_ADD(DATE_FORMAT(NOW(),'%Y-%m-%d'), INTERVAL -1 MONTH) AND DATE_FORMAT(NOW(),'%Y-%m-%d')
                            GROUP BY r3.id_customer
                        ) AS last_month ON last_month.id_customer=c.id_customer
                        LEFT JOIN (
                            SELECT r7.id_customer, SUM(r7.credits) AS fluz_current
                            FROM ps_rewards AS r7
                            WHERE r7.id_reward_state=2
                            GROUP BY r7.id_customer
                        ) AS fluz_current ON fluz_current.id_customer=c.id_customer
                        LEFT JOIN (
                            SELECT r4.id_customer, ABS(SUM(r4.credits)) AS fluz_spent
                            FROM ps_rewards AS r4
                            WHERE r4.credits<0 AND r4.id_reward_state=2 AND r4.`plugin`='loyalty' AND r4.date_add BETWEEN ".$date_between." AND r4.id_transfer_fluz IS NULL AND r4.id_cashout IS NULL 
                            GROUP BY r4.id_customer
                        ) AS fluz_spent ON fluz_spent.id_customer=c.id_customer";

        $list = Db::getInstance()->executeS($this->query);

        if ( $list[0]['id_customer'] != "" ) {
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