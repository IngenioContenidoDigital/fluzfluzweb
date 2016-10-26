<?php

class AdminCashOutControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = false;
        $this->context = Context::getContext();
        $this->table = 'rewards_payment';
        $this->addRowAction('view');
        
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->deleted = false;
        
        $this->_select = 'a.id_rewards_payment, a.nombre, a.apellido, a.numero_tarjeta, a.banco, a.credits, a.date_add, a.id_status, b.color, b.name AS name, b.id_status';
        $this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'rewards_payment_state` b ON (b.`id_status` = a.`id_status`)
                ';
        $this->_orderBy = 'a.id_rewards_payment';
        $this->_use_found_rows = true;
        
        $this->fields_list = array(
            'id_rewards_payment' => array('title' => $this->l('ID Pago'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'nombre' => array('title' => $this->l('Nombre')),
            'apellido' => array('title' => $this->l('Apellido')),
            'numero_tarjeta' => array('title' => $this->l('Numero de Tarjeta')),
            'banco' => array('title' => $this->l('Banco')),
            'name' => array(
                'title' => $this->l('Estado'),
                'type' => 'select',
                'color' => 'color',
                'list' => $this->status(),
                'filter_key' => 'b!id_status',
                'filter_type' => 'int',
                'order_key' => 'name'
            ),
            'credits' => array('title' => $this->l('Pago')),
            'date_add' => array(
                'title' => $this->l('Fecha'),
                'type' => 'datetime',
                'align' => 'text-center',
                'filter_key' => 'a!date_add'
            )
            
            /*'active' => array('title' => $this->l('Active'), 'align' => 'center', 'active' => 'status',
                'type' => 'bool', 'class' => 'fixed-width-sm'),*/
        );
        
        
        /*$this->fields_options = array(
            'general' => array(
                'title' =>    $this->l('Employee options'),
                'fields' =>    array(
                    'PS_PASSWD_TIME_BACK' => array(
                        'title' => $this->l('Password regeneration'),
                        'hint' => $this->l('Security: Minimum time to wait between two password changes.'),
                        'cast' => 'intval',
                        'type' => 'text',
                        'suffix' => ' '.$this->l('minutes'),
                        'visibility' => Shop::CONTEXT_ALL
                    ),
                    'PS_BO_ALLOW_EMPLOYEE_FORM_LANG' => array(
                        'title' => $this->l('Memorize the language used in Admin panel forms'),
                        'hint' => $this->l('Allow employees to select a specific language for the Admin panel form.'),
                        'cast' => 'intval',
                        'type' => 'select',
                        'identifier' => 'value',
                        'list' => array(
                            '0' => array('value' => 0, 'name' => $this->l('No')),
                            '1' => array('value' => 1, 'name' => $this->l('Yes')
                        )
                    ), 'visibility' => Shop::CONTEXT_ALL)
                ),
                'submit' => array('title' => $this->l('Save'))
            )
        );*/

        parent::__construct();
    }
   
    
    public function status(){
        
        
        $select = array('',);
        $query = 'SELECT name, id_status FROM '._DB_PREFIX_.'rewards_payment_state';
        $array = DB::getInstance()->executeS($query);
        
        $list = array_map('current', $array);
        
        return array_merge($select, $list);
        
    }
    
    public function change_status(){
        
        $query = 'SELECT name, id_status FROM '._DB_PREFIX_.'rewards_payment_state';
        $array = DB::getInstance()->executeS($query);
        
        return $array;
        
    }
    
    public function renderView()
    {
        $tpl = $this->context->smarty->createTemplate(_PS_BO_ALL_THEMES_DIR_.'default/template/controllers/cash_out/views/payment_cash.tpl', $this->context->smarty);
        $tpl->assign(array( 
            'datos'=>$this->datos_cash(Tools::getValue('id_rewards_payment'))
            ));
        $id = Tools::getValue('id_rewards_payment');
        $this->context->smarty->assign('id', $id);
        
        return $tpl->fetch();
    }
    
    public function initContent()
    {   
        
        $this->context->smarty->assign(array( 
            'state' => $this->change_status(),
        ));
        
        return parent::initContent();
    }
    
    public function datos_cash($id_payment){
        $query = 'SELECT a.id_rewards_payment, a.nombre, a.apellido, a.numero_tarjeta, a.banco, a.credits, a.date_add, a.id_status, b.name AS name, b.id_status
                  FROM '._DB_PREFIX_.'rewards_payment a  
                  LEFT JOIN `'._DB_PREFIX_.'rewards_payment_state` b ON (b.`id_status` = a.`id_status`) WHERE id_rewards_payment='.$id_payment;
        
        $list_tpl = DB::getInstance()->executeS($query);
        
        return $list_tpl;
    }
    
    public function postProcess()
    {   
        if (Tools::isSubmit('submitState')) {
            
            $qstate="UPDATE "._DB_PREFIX_."rewards_payment SET id_status= ".Tools::getValue('id_status')." WHERE id_rewards_payment=".Tools::getValue('id_payment');
                            Db::getInstance()->execute($qstate);
            
            $query = 'SELECT firstname, lastname, id_employee FROM '._DB_PREFIX_.'employee WHERE id_employee='.$this->context->employee->id;
            $row = DB::getInstance()->getRow($query);
            $name_employee = $row['firstname'];
            $lastname_employee = $row['lastname'];
            
            $paid = Tools::getValue('paid');
            
            $query_insert = "INSERT INTO "._DB_PREFIX_."rewards_payment_employee(id_rewards_payment, id_employee, name, lastname, credits, id_status, date_add)"
                            . "                          VALUES (".Tools::getValue('id_payment').", ".(int)$this->context->employee->id.",'".$name_employee."','".$lastname_employee."',".$paid.",".Tools::getValue('id_status').",'".date("Y-m-d H:i:s")."')";
                        Db::getInstance()->execute($query_insert);
                        
            $qstate_employee="UPDATE "._DB_PREFIX_."rewards_payment_employee SET id_status= ".Tools::getValue('id_status')." WHERE id_rewards_payment=".Tools::getValue('id_payment');
                            Db::getInstance()->execute($qstate_employee);            
                        
            Tools::redirectAdmin(self::$currentIndex.'&id_rewards_payment='.Tools::getValue('id_payment').'&viewrewards_payment&token='.$this->token);
        }
        
        parent::postProcess();
    }
    
    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'cashout.css');
    }
}
