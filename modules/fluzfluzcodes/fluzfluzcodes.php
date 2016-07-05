<?php

if (!defined('_PS_VERSION_'))
  exit;

class fluzfluzCodes extends Module{
   public $nuevo_archivo;
   public $folder="fluzfluzcodes/upload/";
   public $location=_PS_MODULE_DIR_;
    
    public function __construct(){
        $this->name = 'fluzfluzcodes';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'Ingenio Contenido Digital';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Fluz Fluz Product Codes');
        $this->description = $this->l('This module enables a new product tab in order to upload a CSV file with codes needed to be used by customers after purchase in a BarCode format.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('FLUZFLUZCODES'))      
          $this->warning = $this->l('No name provided');
    }
    
    public function install(){
        
        if (!parent::install() || !$this->registerHook('displayAdminProductsExtra') || !Configuration::updateValue('FLUZFLUZCODES', '1'))
                return false;
        
        $sql="CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."product_code (id_product_code int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
            id_product int(10) NOT NULL ,  
            code  varchar(255) NOT NULL, id_order  int(10) NOT NULL ) 
            ENGINE="._MYSQL_ENGINE_." DEFAULT CHARACTER SET=latin1 COLLATE=latin1_spanish_ci";
        
        Db::getInstance()->Execute($sql);
        return true;
    }
    
    public function uninstall(){
        
        if (!parent::uninstall() || !Configuration::deleteByName('FLUZFLUZCODES'))
            return false;
        Db::getInstance()->Execute("DROP TABLE IF EXISTS "._DB_PREFIX_."product_code");
        return true;
    }
    
    public function hookdisplayAdminProductsExtra($params) {
        $query = "SELECT code
                    FROM ps_product_code
                    WHERE id_product = ".Tools::getValue('id_product')."
                    AND id_order = 0";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
        $this->context->smarty->assign('codes', $result );
        return $this->display(__FILE__, 'views/fluzfluzcodes_admin.tpl');
    }
    
    public function getContent(){
    $output = null;
 
    if (Tools::isSubmit('submit'.$this->name)){
        $file = $_FILES['archivo'];
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = array('txt', 'csv');

        if ( file_exists($file['tmp_name']) && in_array($extension, $allowed) ){
            $filename = uniqid()."-".basename($file['name']);
            $filename = str_replace(' ', '-', $filename);
            $filename = strtolower($filename);
            $filename = filter_var($filename, FILTER_SANITIZE_STRING);
            $file['name'] = $filename;
            
            $uploader = new UploaderCore();
            $uploader->setSavePath($this->location.$this->folder);
            $uploader->upload($file);
            $this->nuevo_archivo=$file['name'];
            chmod($this->location.$this->folder.$this->nuevo_archivo, 0777);
        } 
        if (isset($this->nuevo_archivo)){
            $status=$this->uploadCodes();
        }
            if ($status) {
                    $output .= $this->displayConfirmation($this->l('Se realizo la carga de codigos..'));
            } else {
                    $output .= $this->displayConfirmation($this->l('Error al procesar el archivo.'));
            }
        } 
        return $output.$this->displayForm();
    }
    
    
    public function displayForm(){
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Card Codes'),
            ),
            'description' => $this->l('description'),
            'input' => array(
                array(
                    'type' => 'file',
                    'label' => $this->l('Pick a CSV File'),
                    'name' => 'archivo',
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        $helper->fields_value['FLUZFLUZCODES'] = Configuration::get('FLUZFLUZCODES');

        return $helper->generateForm($fields_form);
    }
    
    public function randString ($length = 4){  
      $string = "";
      $possible = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXY";
      $i = 0;
      while ($i < $length){
        $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
        $string .= $char;    
        $i++;  
      }
      return date("dmY_Hi_").$string;
    }    
    
    public function saveFile($arrayDoc,$documento,$dataUser){  
        $archivo_usuario = str_replace(' ','-',$arrayDoc[$documento]['name']); 
        $tipo_archivo = $arrayDoc[$documento]['type']; 
        $tamano_archivo = $arrayDoc[$documento]['size'];
        $extension = strrchr($arrayDoc[$documento]['name'],'.');

        try {
            if (move_uploaded_file($arrayDoc[$documento]['tmp_name'],$this->location.$this->folder.$this->nuevo_archivo)){ 
                chmod($this->location.$this->folder.$this->nuevo_archivo, 0777);
                return $vector = array ( $this->nuevo_archivo, $archivo_usuario );
            } else { 
                return $vector = array (false, false );
                
            }
        } catch(Exception $e){
            echo 'Error en la Función sefeFile --> lib.php ', $e->getMessage(), "\n";
            exit;
        }
    }
    
    public function uploadCodes(){
        $state =false;
        $headers = array('Reference #','code');
        $handle = fopen($this->location.$this->folder.$this->nuevo_archivo, 'a+');

        while (($results = fgetcsv($handle, 1000, ";")) !== FALSE) {
            echo $results[0];
            $query="SELECT ps_product.id_product, ps_product.reference 
                FROM ps_product WHERE ps_product.reference = '".$results[0]."'";
            if($line=Db::getInstance()->getRow($query)){
                $query1="INSERT INTO "._DB_PREFIX_."product_code (id_product,code) VALUES ('".$line['id_product']."','".$results[1]."')";
                $run=Db::getInstance()->execute($query1);
            }
        }
        fclose($handle);
        $this->updateQuantities();
        if($run) $state=true;
        return $state;
    }
    
    public function updateQuantities(){
        $qr0 = "UPDATE "._DB_PREFIX_."stock_available AS st SET st.quantity=0";
        $qr = "UPDATE "._DB_PREFIX_."stock_available AS st SET st.quantity=(SELECT Count(pc.`code`) AS total FROM "._DB_PREFIX_."product_code AS pc WHERE pc.id_order = 0 AND st.id_product=pc.id_product)";
        Db::getInstance()->execute($qr0);
        Db::getInstance()->execute($qr);
    }
}
