<?php

if (!defined('_PS_VERSION_'))
  exit;

require_once(_PS_MODULE_DIR_.'fluzfluzapi/models/CSoap.php');

class fluzfluzapi extends Module{

   public $location=_PS_MODULE_DIR_;
   public $id_product = 0;
    
    public function __construct(){
        $this->name = 'fluzfluzapi';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'Ingenio Contenido Digital';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
        $this->bootstrap = true;
        $this->controllers = 'shoppingcart';

        parent::__construct();

        $this->displayName = $this->l('Fluz Fluz API');
        $this->description = $this->l('This module enables a new tab in product form to select among configured API with merchants via SOAP.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

    }
    
    public function install(){
        $sql1="DROP TABLE IF EXISTS "._DB_PREFIX_."webservice_external, "
                ._DB_PREFIX_."webservice_external_product, "
                ._DB_PREFIX_."webservice_external_telco, "
                ._DB_PREFIX_."webservice_external_telco_operator, "
                ._DB_PREFIX_."webservice_external_log";
        $sql2="CREATE TABLE "._DB_PREFIX_."webservice_external (
  id_webservice_external int(10) NOT NULL AUTO_INCREMENT,
  name varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  uri varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  login varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  password varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  request text COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id_webservice_external`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
        Db::getInstance()->execute($sql1);
        Db::getInstance()->execute($sql2);
        
        $sql3="CREATE TABLE "._DB_PREFIX_."webservice_external_product (
id_webservice_external_product int(10) NOT NULL AUTO_INCREMENT,
id_webservice_external int(10) NOT NULL,
id_product int(10) NOT NULL,
id_operator int(10) NULL,
PRIMARY KEY (`id_webservice_external_product`))";
        Db::getInstance()->execute($sql3);
        
        $sql4="CREATE TABLE "._DB_PREFIX_."webservice_external_telco (
id_webservice_external_telco int(10) NOT NULL AUTO_INCREMENT,
id_cart int(10) NOT NULL,
id_product int(10) NOT NULL,
phone_mobile double(10,0) NOT NULL,
PRIMARY KEY (`id_webservice_external_telco`))";
        Db::getInstance()->execute($sql4);
        
        $sql5="CREATE TABLE "._DB_PREFIX_."webservice_external_telco_operator (
        id_webservice_external_telco_operator int(10) NOT NULL AUTO_INCREMENT, 
        id_webservice_external int(10) NOT NULL, 
        id_operator int(10) NOT NULL, 
        name varchar(255) NOT NULL, 
        PRIMARY KEY (`id_webservice_external_telco_operator`))";
        Db::getInstance()->execute($sql5);
        
        
        $sql6 = "CREATE TABLE "._DB_PREFIX_."webservice_external_log (
id_webservice_external_log  int(10) NOT NULL AUTO_INCREMENT,
id_webservice_external int(10) NOT NULL,
id_order int(10) NOT NULL,
id_product int(10) NOT NULL,
mobile_phone double(10,0) NOT NULL,
action  text NOT NULL, 
request text NOT NULL,
response_code int(10) NOT NULL,
response_message text NOT NULL,
retries int(3) NOT NULL,
date_add datetime NOT NULL,
date_upd datetime NOT NULL,
PRIMARY KEY (`id_webservice_external_log`))";
         Db::getInstance()->execute($sql6);
         
/* Registrar un nuevo cronjob usando el resultado de $address asi:
 * $protocol = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://'; 
 * $address=$protocol.Configuration::get('PS_SHOP_DOMAIN')."/modules/fluzfluzapi/PendingRequests.php";
 * 
 * * * * * * curl {$address}
*/        
        if (!parent::install() || !$this->registerHook('actionOrderStatusUpdate') 
                || !$this->registerHook('ShoppingCartExtra') 
                || !$this->registerHook('displayAdminProductsExtra'))
                return false;
        return true;
    }
    
    public function uninstall(){
        $sql1="DROP TABLE IF EXISTS "._DB_PREFIX_."webservice_external, "
                ._DB_PREFIX_."webservice_external_product, "
                ._DB_PREFIX_."webservice_external_telco, "
                ._DB_PREFIX_."webservice_external_telco_operator, "
                ._DB_PREFIX_."webservice_external_log";
         Db::getInstance()->execute($sql1);
        if (!parent::uninstall())
            return false;
        return true;
    }
    
    public function hookactionOrderStatusUpdate($params){
       if($params['newOrderStatus']->id==2){
            $sql="SELECT od.id_order, wep.id_product, 
                od.product_quantity, od.product_price, wep.id_webservice_external 
                FROM
                ps_order_detail AS od 
                INNER JOIN ps_webservice_external_product AS wep 
                ON od.product_id = wep.id_product 
                WHERE od.id_order=".$params['id_order'];
            $products = Db::getInstance()->executeS($sql);
            
            $total=Db::getInstance()->numRows();
            
            $query_name = 'SELECT c.username as username, c.email as email FROM '._DB_PREFIX_.'orders o
                           LEFT JOIN '._DB_PREFIX_.'customer c ON (o.id_customer = c.id_customer)
                           WHERE o.id_order ='.$params['id_order'];
            $user = Db::getInstance()->executeS($query_name);
            
            if($total>0){
                foreach($products as $product){
                    for ($quantity = 1; $quantity <= $product['product_quantity']; $quantity++) {
                    
                        $sclient = new CSoap($product['id_webservice_external']);

                        $acproduct= new Product($product['id_product']);

                        $getnumber="SELECT wet.phone_mobile, wep.id_operator 
                            FROM ps_orders AS o 
                            INNER JOIN ps_webservice_external_telco AS wet ON o.id_cart = wet.id_cart 
                            INNER JOIN ps_order_detail AS od ON o.id_order = od.id_order 
                            INNER JOIN ps_webservice_external_product AS wep ON od.product_id = wep.id_product  
                            WHERE o.id_order =".$params['id_order'];

                        $number=Db::getInstance()->getRow($getnumber);
                        
                        $sclient->request=$sclient->setValue($sclient->request, "TopUpRequest", "DeviceType",3);
                        $sclient->request=$sclient->setValue($sclient->request, "TopUpRequest", "Platform",1);
                        $sclient->request=$sclient->setValue($sclient->request, "TopUpRequest", "Amount",intval($acproduct->price_shop));
                        $sclient->request=$sclient->setValue($sclient->request, "TopUpRequest", "ExternalTransactionReference",$params['id_order']."-".time()."-".$quantity);
                        $sclient->request=$sclient->setValue($sclient->request, "TopUpRequest", "MNO",$number['id_operator']);
                        $sclient->request=$sclient->setValue($sclient->request, "TopUpRequest", "Recipient",$number['phone_mobile']);
                        $sclient->request=$sclient->setValue($sclient->request, "TopUpRequest", "WalletType","Stock");

                        $response=$sclient->doRequest('http://api.movilway.net/schema/extended/IExtendedAPI/TopUp',$sclient->request);
                        if ($number['phone_mobile'] != ''){
                            if(!is_numeric($response)){
                                $xml = simplexml_load_string($response);
                                $xml->registerXPathNamespace('res', 'http://api.movilway.net/schema/extended');
                                $response=array();
                                foreach($xml->xpath('//res:*') as $item){
                                    if(((string)$item->getName())!=='responsemessage'){
                                        $response[strtolower((string)$item->getName())]=(string)$item;
                                    }
                                }
                                $insert ="INSERT INTO "._DB_PREFIX_."webservice_external_log (id_webservice_external, id_order, id_product, mobile_phone, action, request, response_code, response_message, date_add, date_upd) "
                                    . "VALUES ('".$product['id_webservice_external']."', '".$params['id_order']."', '".$product['id_product']."', '".$number['phone_mobile']."', 'http://api.movilway.net/schema/extended/IExtendedAPI/TopUp', '".$sclient->request."', '".(int)$response['responsecode']."', '".$response['responsemessage']."', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";
                                if((int)$response['responsecode']==0){
                                    $chainsql="SELECT "._DB_PREFIX_."customer.id_customer, "._DB_PREFIX_."customer.secure_key 
                            FROM "._DB_PREFIX_."webservice_external_log INNER JOIN "._DB_PREFIX_."orders ON "._DB_PREFIX_."webservice_external_log.id_order = "._DB_PREFIX_."orders.id_order INNER JOIN "._DB_PREFIX_."customer ON "._DB_PREFIX_."orders.id_customer = "._DB_PREFIX_."customer.id_customer 
                                WHERE "._DB_PREFIX_."webservice_external_log.id_order =".$params['id_order'];
                                    $chainrow= Db::getInstance()->getRow($chainsql);

                                    set_time_limit(5000);
                                    $chain = Encrypt::encrypt($chainrow['secure_key'] , $number['phone_mobile']);

                                    $code = "INSERT INTO "._DB_PREFIX_."product_code (id_product, code, id_order, used, date_add, encry) VALUES ('".$product['id_product']."', '".$chain."', '".$params['id_order']."', '2', '".date('Y-m-d H:i:s')."', 1)";
                                    Db::getInstance()->execute($code);

                                    $template = 'order_conf_telco_sucess';
                                    $prefix_template = 'order_conf_telco_sucess';

                                    $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
                                    $row_subject = Db::getInstance()->getRow($query_subject);
                                    $message_subject = $row_subject['subject_mail'];

                                    $vars = array(
                                            '{username}' => $user[0]['username'],
                                            '{Recharged}' => $number['phone_mobile']
                                        );

                                    Mail::Send(1, $template, $message_subject, $vars, $user[0]['email'], $user[0]['username'], Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'),NULL, NULL, dirname(__FILE__).'/mails/', false);
                                }
                            }else{
                                $insert ="INSERT INTO "._DB_PREFIX_."webservice_external_log (id_webservice_external, id_order, id_product, mobile_phone, action, request, response_code, response_message, date_add, date_upd) "
                                    . "VALUES ('".$product['id_webservice_external']."', '".$params['id_order']."', '".$product['id_product']."', '".$number['phone_mobile']."', 'http://api.movilway.net/schema/extended/IExtendedAPI/TopUp', '".$sclient->request."', '-1', 'Transaccion sin respuesta luego de 90s', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";

                                $template = 'order_conf_telco_failed';
                                $prefix_template = 'order_conf_telco_failed';

                                $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
                                $row_subject = Db::getInstance()->getRow($query_subject);
                                $message_subject = $row_subject['subject_mail'];

                                $vars = array(
                                        '{username}' => $user[0]['username'],
                                        '{Recharged}' => $number['phone_mobile']
                                    );

                                Mail::Send(1, $template, $message_subject, $vars, $user[0]['email'], $user[0]['username'], Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'),NULL, NULL, dirname(__FILE__).'/mails/', false);
                            }                    
                            Db::getInstance()->execute($insert);
                        }    
                    }
                }
            }
      }
    }
    
    public function getContent(){
        $output = null;
        if(Tools::isSubmit('process_ws')){$output=$this->processWS(Tools::getValue('option'),Tools::getValue('name'),Tools::getValue('uri'),Tools::getValue('login'),Tools::getValue('password'),Tools::getValue('request'),Tools::getValue('id_webservice_external'));}
        if (Tools::isSubmit('process_ws_add')){return $this->callWS('add');}
        if (Tools::isSubmit('updateconfiguration') && (Tools::getValue('option')!='cancel')){
            if($output==null || $output==''){
                return $this->callWS('update',Tools::getValue('id_webservice_external'));
            }
        }
        return $output.$this->displayForm().$this->display(__FILE__, 'views/templates/admin/fluzfluzapi.tpl');
    }
    
    private function displayForm(){
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        
        $qr="SELECT * FROM "._DB_PREFIX_."webservice_external";
        $result=Db::getInstance()->executeS($qr);
        
        $fields_list=array(
            'id_webservice_external'=> array(
                'title' =>$this->l('id'),
                'width'=>10,
                'align'=>'left'
            ),
            'name'=>array(
                'title' =>$this->l('name'),
                'width'=>50,
                'align'=>'left'
            ),
            'uri'=>array(
                'title' =>$this->l('uri'),
                'width'=>50,
                'align'=>'left'
            ),
            'login'=>array(
                'title' =>$this->l('login'),
                'width'=>50,
                'align'=>'left'
            ),
            'password'=>array(
                'title' =>$this->l('password'),
                'width'=>50,
                'align'=>'left'
            )
        );

        $helper = new HelperList();

        $helper->module = $this;
        $helper->shopLinkType = '';
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        
        $helper->identifier = 'id_webservice_external';
        $helper->show_toolbar = true;
        $helper->title = 'API List';
        //$helper->table = $this->name;
        
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->simple_header = true;
        //$helper->fields_list=$fields_list;
        $helper->actions=array('edit');

        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;


        //$helper->fields_value['FLUZFLUZCODES'] = Configuration::get('FLUZFLUZCODES');

        return $helper->generateList($result,$fields_list);
    }  
   
    private function callWS($option, $key=''){
        $this->context->smarty->assign('option', $option);
        if($option=='update'){
            $sql='SELECT * FROM '._DB_PREFIX_.'webservice_external WHERE id_webservice_external='.$key;
            $row=Db::getInstance()->getRow($sql);
            $this->context->smarty->assign('row', $row );
        }
        return $this->display(__FILE__, 'views/templates/admin/webservice.tpl');
    }
    
    private function processWS($option,$name,$uri,$login,$pass,$request,$key=''){
        $output='';
        switch ($option){
            case 'add':
                $sql="INSERT INTO "._DB_PREFIX_."webservice_external (name, uri, login, password, request) VALUES ('$name','$uri','$login','$pass','$request')";
                $output = $this->l('Se ha registrado el API con exito');
                break;
            case 'update':
                $sql="UPDATE "._DB_PREFIX_."webservice_external SET name='$name', uri='$uri', login='$login', password='$pass', request='$request' WHERE id_webservice_external=$key";
                $output = $this->l('Se ha actualizado el API con exito');
                break;
        }
        try{
            Db::getInstance()->execute($sql);
        }catch(Exception $e){
            $output = $e->getMessage();
        }
        return $output;
    }
    
    public function hookdisplayAdminProductsExtra($params) {
        $product=Tools::getValue('id_product');
        
        $sql="SELECT * FROM "._DB_PREFIX_."webservice_external";
        $result = Db::getInstance()->executeS($sql);
        
        $sql1="SELECT * FROM "._DB_PREFIX_."webservice_external_product AS wep WHERE wep.id_product=".$product;
        $selected = Db::getInstance()->getRow($sql1);

        $sql2 = "SELECT * FROM "._DB_PREFIX_."webservice_external_telco_operator AS weto";
        /*WHERE weto.id_webservice_external=".$selected['id_webservice_external']*/
        $operators=Db::getInstance()->executeS($sql2);
        
        $this->context->smarty->assign('product',$product);
        $this->context->smarty->assign('results', $result );
        $this->context->smarty->assign('webservice',$selected);
        $this->context->smarty->assign('operators',$operators);
        $this->context->smarty->assign('message',$message);
        return $this->display(__FILE__, 'views/templates/admin/product.tpl');
    }
    
    public function hookShoppingCartExtra($params){
        $flag=false;
        $productlist=array();
        foreach($params['products'] as $product){
            $isactiveproductws="SELECT * FROM "._DB_PREFIX_."webservice_external_product WHERE id_product=".$product['id_product'];
            Db::getInstance()->executeS($isactiveproductws);
            if(Db::getInstance()->numRows()>0){
                $flag=true;
                $productlist[]=$product;
            }
        }
        if($flag){
            $phone="SELECT phone FROM "._DB_PREFIX_."address WHERE id_customer=".$params['cart']->id_customer
                ." AND LENGTH(phone)=10";
        
            $phone_mobile="SELECT phone_mobile AS phone FROM "._DB_PREFIX_."address WHERE id_customer=".$params['cart']->id_customer
                    ." AND LENGTH(phone_mobile)=10";
            $lsphones=Db::getInstance()->executeS($phone);
            $lsphones1=Db::getInstance()->executeS($phone_mobile);
            $phones_list = array_merge($lsphones, $lsphones1);
            $phones_list=array_unique($phones_list,SORT_REGULAR);
            $this->context->controller->addCSS($this->_path.'/css/shopping-cart.css');
            $this->context->controller->addJS($this->_path.'/js/shopping-cart.js');
            $this->context->controller->addJS($this->_path.'/js/jquery.cookie.js');
            $this->context->smarty->assign(array(
                'productlist'=> $productlist,
                'phones' => $phones_list
            ));
            return $this->display(__FILE__,'views/templates/hook/shopping-cart.tpl');
        }
    }
    
}
