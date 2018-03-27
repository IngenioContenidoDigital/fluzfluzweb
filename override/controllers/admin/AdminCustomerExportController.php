<?php

include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsSponsorshipModel.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsModel.php');

class AdminCustomerExportControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;

        $this->context = Context::getContext();

        AdminController::__construct();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Exportar Listado de Usuarios'),
                'icon' => 'icon-download'
            ),
            'input' => array(
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
                'title' => $this->l('Exportar'),
                'name' => 'exportcustomer',
                'type' => 'submit',
                'icon' => 'process-icon-download-alt'
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
        if (Tools::isSubmit('exportcustomer')) {
            Customer::CustomerExport( Tools::getValue('date_from'), Tools::getValue('date_to') );
        }
        
        switch ( Tools::getValue('action') ) {
        
            case 'clickSearch':
                
                $id_customer = Tools::getValue('id_customer');
                $tree = RewardsSponsorshipModel::_getTree($id_customer);
                
                foreach ($tree as &$network){
                    $sql = "SELECT c.method_add, c.firstname, c.email, c.date_add, rs.id_sponsor, (SELECT cc.firstname  FROM "._DB_PREFIX_."customer cc WHERE cc.id_customer = rs.id_sponsor)  as Nombre_sponsor,
                            (SELECT cc.email  FROM "._DB_PREFIX_."customer cc WHERE cc.id_customer = rs.id_sponsor)  as email_sponsor
                            FROM "._DB_PREFIX_."customer c
                            LEFT JOIN "._DB_PREFIX_."rewards_sponsorship rs ON (rs.id_customer = c.id_customer)
                            WHERE c.id_customer = ".$network['id'];
                    $row_sql = Db::getInstance()->getRow($sql);

                    $network['method_add'] = $row_sql['method_add'];
                    $network['email'] = $row_sql['email'];
                    $network['firstname'] = $row_sql['firstname'];
                    $network['email_sponsor'] = $row_sql['email_sponsor'];
                    $network['Nombre_sponsor'] = $row_sql['Nombre_sponsor'];
                    $network['date_add'] = $row_sql['date_add'];
                }
                
                die (json_encode($tree));
                break;
            case 'searchUser':
                $username_search = strtolower($_POST['username']);

                $tree = Db::getInstance()->executeS('SELECT c.id_customer as id FROM '._DB_PREFIX_.'customer  c
                        LEFT JOIN '._DB_PREFIX_.'customer_group cg ON (c.id_customer = cg.id_customer)
                        WHERE c.active = 1 AND cg.id_group = 4 AND c.kick_out!=1');
                
                foreach ($tree as &$network){
                    $sql = 'SELECT username, email, dni FROM '._DB_PREFIX_.'customer 
                            WHERE id_customer='.$network['id'];
                    $row_sql = Db::getInstance()->getRow($sql);

                    $network['username'] = $row_sql['username'];
                    $network['email'] = $row_sql['email'];
                    $network['dni'] = $row_sql['dni'];
                }

                if (!empty($username_search)){
                    $usersFind = array();
                    foreach ($tree as &$usertree){
                        $username = strtolower($usertree['username']);
                        $email = strtolower($usertree['email']);
                        $dni = $usertree['dni'];

                        $coincidenceusername = strpos($username,$username_search);
                        $coincidenceemail = strpos($email,$username_search);
                        $coincidendni = strpos($dni,$username_search);

                        if ( $coincidenceusername !== false || $coincidenceemail !== false || $coincidendni !== false) {
                            $usersFind[] = $usertree;
                        }
                    }
                    die (json_encode($usersFind));
                }
                break;
            default:
                break;
                
        }
    }

    public function initContent()
    {
        $this->initTabModuleList();
        $this->initPageHeaderToolbar();
        $this->show_toolbar = false;
        $this->content .= $this->renderForm();
        $this->content .= $this->renderOptions();
        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
        
        AdminController::initContent();       
        $this->setTemplate('controllers/customer_export/content.tpl');   
    }
}
