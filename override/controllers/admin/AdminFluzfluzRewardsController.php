<?php

class AdminFluzfluzRewardsControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();

        AdminController::__construct();
    }

    public function renderForm()
    {
        $list_values[0] = array(id_state => 0, stateReward => 'Desactivar');
        $list_values[1] = array(id_state => 1, stateReward => 'Activar');
        
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('FluzFluz Customer Rewards'),
                'icon' => 'icon-download'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Estado de la Recompensa'),
                    'name' => 'stateReward',
                    'required' => true,
                    'desc' => $this->l('Activado / Desactivado'),
                    'options' => array(
                        'query' => $list_values,
                        'id' => 'id_state',
                        'name' => 'stateReward'
                    ),
                    'hint' => $this->l('Escoge estado. Activado / Desactivado')
                ), 
                array(
                    'type' => 'text',
                    'label' => $this->l('Fluz a Distribuir'),
                    'name' => 'rewardsCustomer',
                    'required' => true,
                    'col' => '1',
                    'hint' => $this->l('Invalid characters:').'!&lt;&gt;,;?=+()@#"°{}_$%:'
                ),
                array(
                    'type' => 'date',
                    'label' => $this->l('From'),
                    'name' => 'date_from',
                    'maxlength' => 10,
                    'required' => true,
                    'hint' => $this->l('Format: 2011-12-31 (inclusive).')
                ),
                array(
                    'type' => 'date',
                    'label' => $this->l('To'),
                    'name' => 'date_to',
                    'maxlength' => 10,
                    'required' => true,
                    'hint' => $this->l('Format: 2012-12-31 (inclusive).')
                )
            ),
            'submit' => array(
                'title' => $this->l('Guardar'),
                'name' => 'saveRewards',
                'type' => 'submit',
                'icon' => 'process-icon-save'
            )
        );

        $this->fields_value = array(
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d')
        );

        return AdminController::renderForm();
    }

    public function postProcess()
    {
        switch ( Tools::getValue('action') ) {
        
            case 'modifyState':
                $state_reward = Tools::getValue('state_value');
                $id_reward = Tools::getValue('id_reward');
                
                if($state_reward == 0 || $state_reward == 1){
                    
                    $update = Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'rewards_distribute SET active = '.$state_reward.', date_add = NOW()
                                            WHERE id_rewards_distribute = '.$id_reward.' AND method_add = "Backoffice"');
                    
                
                    die($update);
                }
                else{
                    
                    $delete = Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'rewards_distribute WHERE id_rewards_distribute = '.$id_reward.' AND method_add = "Backoffice"');
                    die($delete);
                }
                
                break;
            default:
                break;
                
        }
        
        if (Tools::isSubmit('saveRewards')) {
            $this->saveCustomerRewards(Tools::getValue('stateReward'),Tools::getValue('rewardsCustomer'),Tools::getValue('date_from'), Tools::getValue('date_to'), 'Backoffice' );
        }
    }

    public function initContent()
    {
        $this->initTabModuleList();
        $this->initPageHeaderToolbar();
        $this->show_toolbar = false;
        $this->content .= $this->renderForm();
        $this->content .= $this->renderOptions();
        
        $reward_fluz = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'rewards_distribute WHERE method_add = "Backoffice"');
        
        $this->context->smarty->assign(array(
            'content' => $this->content,
            'reward_fluz' => $reward_fluz,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
        
        AdminController::initContent();       
        $this->setTemplate('controllers/rewards_fluz/content.tpl');   
    }
    
    public function saveCustomerRewards($state, $rewards, $date_from = "", $date_to = "", $method){
        
        Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_distribute (credits, active, id_employee, id_customer, name, date_from, date_to, date_add, method_add)
                                   VALUES ('.$rewards.', '.$state.', '.$this->context->employee->id.',NULL,"'.$this->context->employee->firstname.'", "'.$date_from.'", "'.$date_to.'",  NOW(), "'.$method.'")');
        
        //insertar datos.
        
    }
}
