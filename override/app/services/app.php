<?php
require_once('classes/Rest.inc.php');
require_once('classes/Model.php');

class API extends REST {

    public $id_lang_default = 0;

    public function __construct() 
    {
        parent::__construct(); // Init parent contructor
        $this->id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
    }

    /**
     * Método público para el acceso a la API.
     * Este método llama dinámicamente el método basado en la cadena de consulta
     *
     */
    public function processApi()
    {
        $func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
        if((int)method_exists($this,$func) > 0)
            $this->$func();
        else
            $this->response('No funciona',404); // If the method not exist with in this class, response would be "Page not found".
    }
        
        
    /**
     * M�todo de pruebas
     */
    private function prueba() {
      $this->response($this->json(array(
          "success" => true, 
          "message" => "Se ejecut� el metodo de prueba correctamente."
        )), 200);    
    }
    
    public function formatPrice($number){
      return number_format($number, 0, '', '.');
    }
    
    
    private function myAccountData(){
      if ($this->get_request_method() != "GET") {
        $this->response('', 406);
      }
      
      $id_customer =  trim( $this->_request['userId']);
      $context = Context::getContext();
      $MyAccountController = new MyAccountController();
      $userData = $MyAccountController->getUserDataAccountApp( $id_customer );
      $userData['totalMoney'] = $this->formatPrice( $userData['fluzTotal'] * 25 );
      return $this->response($this->json($userData), 200);
    }

    

    /**
     * Recibe el id de cliente, el lenguaje y retorna los n�meros de tel�fono de ese cliente.
     * @param int $id_customer
     * @param int $id_lang
     * @return Array $phone
     */
    private function getTelephoneByCustomer($id_customer, $id_lang = 1) {
        $customer = new Customer($id_customer);
        $addresses = $customer->getAddresses($id_lang);
        $phone = array();
        foreach ($addreses as $key => $address) {
            $phone[$key] =  $address['phone_mobile'];
        }
        //error_log("\n\n\nEsto son las direcciones: ".print_r($phone,true),3,"/tmp/error.log");    
        return $phone;
    }

    /**
     * Codifica el array en un JSON
     */
    private function json($data)
    {
        if(is_array($data)){
            return json_encode($data);
        }
    }

  private function searchByMap() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    $position['lat'] =  round($this->_request['lat'], 6);;
    $position['lng'] =  round($this->_request['lng'], 6);;
    
//    error_log("\n\n\n\n\n Esto es lo que recibe: \n lat: ".print_r($position['lat'], true)."\n lng: ".print_r($position['lng'], true),3,"/tmp/error.log");
    $query = 'SELECT GROUP_CONCAT(DISTINCT id_manufacturer)
              FROM  '._DB_PREFIX_.'address
              WHERE latitude = '.$position['lat'].' and longitude = '.$position['lng']
            ;
//    error_log("\n\n\n\n\n Esto es el query: \n ".print_r($query, true),3,"/tmp/error.log");
    $manufacturers = Db::getInstance()->getValue($query);
    
//    error_log("\n\n\n\n\n Esto es manufacturers: \n ".print_r($manufacturers, true),3,"/tmp/error.log");
    
    $search = Search::findApp( $manufacturers, 4 );
    
//    error_log("\n\n\n\n\n Esto es el search: \n ".print_r($search, true),3,"/tmp/error.log");
    
    $link = new Link();
    
    foreach ($search['result'] as &$result){
      $result['image_manufacturer'] = $this->protocol . $link->getManufacturerImageLink($result['m_id']);
      $result['m_points'] = round($result['m_points']);
      $prices = explode(",", $result['m_prices']);
      $price_min = round($prices[0]);
      $price_max = round($prices[ count($prices) - 1 ]);
      $result['prices'] = $this->formatPrice($price_min)." - ".$this->formatPrice($price_max);
    }
    
    $this->response($this->json($search), 200);
  }
    
  private function search() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    $requestData = array(
      'param' => '',
      'option' => 0,
      'limit' => 0,
      'lastTotal' => 0
    );
    
    //llena las variables de busqueda.
    foreach ($requestData as $rqd => $value) {
      ${$rqd} = isset($this->_request[$rqd]) ? $this->_request[$rqd] : $value;
    }
//    error.log('\n\n param'.$param,3,'/tmp/error.log');
//    error.log('\n\n option'.$option,3,'/tmp/error.log');
//    error.log('\n\n limit'.$limit,3,'/tmp/error.log');
//    error.log('\n\n lastTotal'.$lastTotal,3,'/tmp/error.log');
    //Hace la busqueda
    $search = array();
    $search = Search::findApp( $param, $option );
    
//    error_log("\n\nsearch: ".print_r($search,true),3,"/tmp/error.log");
    
    //Valida el resultado de la busqueda
    if (!isset($search['result']) || empty($search['result'])) {
      $this->response($this->json(array(
      'success' => false,
      'message' => ':('
      )), 400);
    }
    
    //Instancia de objetos.
    $context = Context::getContext();
    $link = new Link();
    $model =  new Model();
    $manufacturer = array();
    $limit = count( $search['result'] ) < $limit ? count( $search['result'] ) : $limit ;
    //Si la busqueda es por tienda, Busqueda 1
    if( $option == 1 ){
      if ( $limit != 0 ){
        for ( $i = $lastTotal; $i < $limit; $i++ ) {
          $manufacturer[] = $search['result'][$i];
        }
      }
      for ($i = 0; $i < count($manufacturer); $i++) {
        $manufacturer[$i]['image_manufacturer'] = $this->protocol . $link->getManufacturerImageLink($manufacturer[$i]['m_id']);
        $manufacturer[$i]['m_points'] = round($manufacturer[$i]['m_points']);
        $prices = explode(",", $manufacturer[$i]['m_prices']);
        $price_min = round($prices[0]);
        $price_max = round($prices[ count($prices) - 1 ]);
        $manufacturer[$i]['prices'] = $this->formatPrice($price_min)." - ".$this->formatPrice($price_max);
      }
      $search['result'] = $manufacturer;
      $this->response($this->json($search), 200);
    }
    else if ( $option == 2 ){
      $productFather = $search['result'];
      for ($i = 0; $i < count($productFather); $i++){
        $productFather[$i]['points'] = round($productFather[$i]['points']);
        $prices = explode(",", $productFather[$i]['rango_precio']);
        $price_min = round($prices[0]);
        $price_max = round($prices[ count($prices) - 1 ]);
        $productFather[$i]['prices'] = $this->formatPrice($price_min)." - ".$this->formatPrice($price_max);
      }
      $search['result'] = $productFather;
      $search['total'] = count($productFather);
      $this->response($this->json($search), 200);
    }
    else if ( $option == 3 ){
      $productChild = $search['result'];
      for ($i = 0; $i < count($productChild); $i++){
        $productChild[$i]['c_price'] = round($productChild[$i]['c_price']);
        $productChild[$i]['c_percent_save'] = round( ( ( $productChild[$i]['c_price_shop'] - $productChild[$i]['c_price'] )/ $productChild[$i]['c_price_shop'] ) * 100 );
        $productChild[$i]['c_win_fluz'] = round( $model->getPoints( $productChild[$i]['c_id_product'], $productChild[$i]['c_price'] ) );
        $productChild[$i]['c_price_fluz'] = $this->formatPrice(round( $productChild[$i]['c_price'] / 25 ));
        $productChild[$i]['c_price'] = $this->formatPrice(round($productChild[$i]['c_price']));
        
      }
      $search['result'] = $productChild;
      $this->response($this->json($search), 200);
    }
    
  }
    
    /** 
     * Productos API
     * Consulta de los productos debe ser por método GET
     * expr : <Nombre del producto o referencia>
     * page_number : <Número de página>
     * page_size : <Filas por página>
     * order_by : <Ordenar por ascendente ó descendente>
     * order_way : <Ordenar por campo>
     */
//    private function search()
//    {
//        // Validación Cross si el método de la petición es GET de lo contrario volverá estado de "no aceptable"
//        if ($this->get_request_method() != "GET") {
//            $this->response('', 406);
//        }
//
//        $expr        = $this->_request['expr'];
//        $page_number = $this->_request['page_number'];
//        $page_size   = $this->_request['page_size'];
//        $order_by    = $this->_request['order_by'];
//        $order_way   = $this->_request['order_way'];
//
//        $model = new Model();
//        $result = $model->productSearch($this->id_lang_default, $expr, $page_number, $page_size, $order_by, $order_way);
//
//        if (empty($result)) {
//            // Si no hay registros, estado "Sin contenido"
//            $this->response('Sin registros', 204);
//        } else {
//            // Si todo sale bien, enviar� cabecera de "OK" y la lista de la b�squeda en formato JSON
//            $this->response($this->json($result), 200);
//        }
//    }

    /** 
     * Inicio de sesión
     * Válida credenciales de usuario, si todo sale bien agrega el usuario al contexto
     * email : <Correo eléctronico>
     * pwd : <Contraseña>
     */
    private function login($email_sl = NULL,$passwd_sl = NULL)
    {
        // Validación Cross si el método de la petición es POST de lo contrario volverá estado de "no aceptable"
        if($this->get_request_method() != "POST") {
            $this->response('',406);
        }

        $email = strtolower(trim( $email_sl != NULL ? $email_sl : $this->_request['email']) );
        $password =  trim( $passwd_sl != NULL ? $passwd_sl : $this->_request['pwd']);
                
        // Validaciones de entrada
        if(!empty($email) and !empty($password)) {
            if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $customer = new Customer();
                $authentication = $customer->getByEmail($email, $password);
                //error_log("\n\nEste es el customer:\n".print_r($customer, true),3,"/tmp/error.log");

                // Login social meadia
                if($email_sl != NULL && $passwd_sl != NULL && Customer::customerExists($email_sl)){
                    $authentication = $customer->getByEmailSM($email);
                }
				
                if (!$authentication || !$customer->id) {
                    // Error de autenticación
                    $this->response(array('success'=>FALSE), 204);	// Si no hay registros, estado "No Content"
                } else {
                    $context = Context::getContext();
//                    error_log("\n\n\n\nEste es el contexto cuando loguea:\n\n\n\n".print_r(Context::getContext(),true),3,"/tmp/error.log");
                    $context->cookie->id_compare = isset($context->cookie->id_compare) 
                    ? $context->cookie->id_compare
                    : CompareProduct::getIdCompareByIdCustomer($customer->id);
                    $context->cookie->id_customer = (int)($customer->id);
                    $context->cookie->customer_lastname = $customer->lastname;
                    $context->cookie->customer_firstname = $customer->firstname;
                    $context->cookie->logged = 1;
                    $customer->logged = 1;
                    $context->cookie->is_guest = $customer->isGuest();
                    $context->cookie->passwd = $customer->passwd;
                    $context->cookie->email = $customer->email;
                    $context->cookie->active = $customer->active;  
                    $context->cookie->kick_out = $customer->kick_out;  
                    $context->cookie->manual_inactivation = $customer->manual_inactivation;  
                    $context->cookie->days_inactive = $customer->days_inactive;  
                    $context->cookie->autoaddnetwork = $customer->autoaddnetwork;
                    $context->cookie->dni = $customer->dni;
                    
                    $addresses = $customer->getAddresses((int)Configuration::get('PS_LANG_DEFAULT'));
                    $data = array();
                    foreach ($addresses as $key => $address) {
                        $data[] = $address['phone_mobile'];
                    }
                    //$phones = $this->json($data);
                    $phones = implode(",", $data);
                    $context->cookie->phone = $phones;                    
                    
                    // Agrega el cliente a el contexto
                    $context->customer = $customer;
//                    error_log("Estye es el customer del login: ".print_r($context->customer, true),3,"/tmp/error.log");
                                        
                    // Si todo sale bien, enviará cabecera de "OK" y los detalles del usuario en formato JSON

                    unset($customer->passwd, $customer->last_passwd_gen);
                    $gender = $customer->id_gender  == 1 ? 'M' : ($customer->id_gender  == 2 ? 'F' : "");
                    $this->response($this->json(array(
                        'id' => (int) $customer->id,
                        'lastname' => $customer->lastname,
                        'firstname' => $customer->firstname,
                        'email' => $customer->email,
                        'newsletter' => (bool)$customer->newsletter,
                        'dni' => $customer->identification,
                        'gender' => $gender,
                        'id_type' => (int)$customer->id_type,
                        'birthday' => $customer->birthday,
                        'website' => $customer->website,
                        'company' => $customer->company,
                        'active' => $customer->active,
                        'kick_out' => $customer->kick_out,
                        'manual_inactivation' => $customer->manual_inactivation,
                        'days_inactive' => $customer->days_inactive,
                        'autoaddnetwork' => $customer->autoaddnetwork,
                        'dni' => $customer->dni,
                        'phone' => $phones,
                        'success' => TRUE)), 200);
                }
            }
        }

        // Si las entradas son inválidas, mensaje de estado "Bad Request" y la razón
        $this->response($this->json(array(
                "success" => false, 
                "message" => "Dirección de correo electrónico o contraseña no válidos"
                )), 400);
    }

    private function logout()
    {
        $context = Context::getContext();
        $context->customer->mylogout();
        $this->response(true, 200);
    }

    private function test()
    {
        $context = Context::getContext();
        $this->response($this->json((array) $context->customer), 200);
    }

    private function isLogin()
    {
        $context = Context::getContext();
        $this->response(json_encode($context->customer->isLogged()), 200);
    }

    private function categories($params)
    {
        $model = new Model();
        $this->response(json_encode($model->get_category(2,3)),200);
    }


    public function prodCategories() 
    {
        // Validación Cross si el método de la petición es GET de lo contrario volverá estado de "no aceptable"
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }    

        $ids         = $this->_request['ids'];
        $page_number = $this->_request['page_number'];
        $page_size   = $this->_request['page_size'];
        $order_by    = $this->_request['order_by'];
        $order_way   = $this->_request['order_way'];

        $ids_cats = explode(",", $ids);
        if(!is_array($ids_cats))
            $ids_cats[] = array((int)$ids_cats);

        $model = new Model();

        $result = $model->getProdCategories($ids_cats, $page_number,$page_size, $order_way,$order_by);

        if (empty($result)) {
            // Si no hay registros, estado "Sin contenido"
            $this->response('', 204);
        } else {
            // Si todo sale bien, enviará cabecera de "OK" y la lista de la búsqueda en formato JSON
            $this->response($this->json($result), 200);
        }

        //return $this->response($this->json($mugre), 200);
        //return $this->response(json_encode($model->getProdCategories($ids_cats, $page_number,$page_size, $order_way,$order_by)),200);
    }  

    private function header()
    {

    }

    private function myAccount()
    {

    }	
    private function orderHistory()
    {

    }

    private function footer()
    {

    }

    private function product() 
    {

        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $id_prod = $this->_request['id'];

        $model = new Model();
        //return $this->response(json_encode("XD"),200);
        return $this->response(json_encode($model->getProduct($id_prod)),200);
    }

    private function manufacturers()
    {
        $model = new Model();
        return $this->response(json_encode($model->manufacturers()),200);
    }
		

    private function socialLogin()
    {
        $arguments['firstname']	= $this->_request['firstname'];
        $arguments['lastname']		= $this->_request['lastname'];
        $arguments['email']			= $this->_request['email'];
        $arguments['id']		= $this->_request['id'];
        $arguments['passwd'] = NULL;
        $arguments['gender'] 			=  substr($this->_request['gender'], 0,1);

        if (Validate::isEmail($arguments['email']) && !empty($arguments['id']) && !empty($arguments['firstname']) ){

            $tem_data = explode("@", $arguments['email']);
            $arguments['passwd'] = md5($tem_data[1].$arguments['id'].$tem_data[0]);
            if(!Customer::customerExists($arguments['email'])){
                $model = new Model();
                if($customer = $model->setAccount($arguments)) {
                    $this->response($this->json( $customer ),200);
                }
            }else{
                $this->login($arguments['email'],$arguments['passwd']);
            } 
        }
    }

    private function createAccount($update = false)
    {
        // Validación Cross si el método de la petición es POST de lo contrario volverá estado de "no aceptable"
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        } 
        $arguments = array();
        $arguments['email']	= $this->_request['email'];      //Correo
        $arguments['gender'] 	= $this->_request['gender'];     //Genero
        $arguments['firstname'] = $this->_request['firstname'];  //Nombre
        $arguments['lastname']	= $this->_request['lastname'];   //Apellido
        $arguments['passwd']	= $this->_request['passwd'];     //Contrase�a
        $arguments['birthday']	= $this->_request['birthday'];   //Fecha Nacimiento
        $arguments['news']	= $this->_request['news'];       //Bolet�n
        $arguments['dni']	= $this->_request['dni'];        //c�dula
        $arguments['signon']	= $this->_request['signon'];	 //de donde inicia sesi�n
        $arguments['website']	= $this->_request['website'];    //Sitio Web
        $arguments['company']	= $this->_request['company'];    //Compa�ia
        $arguments['id_type']	= $this->_request['id_type'];    // NOAPPLY
        $arguments['update']	= $this->_request['update'];	 //Bandera de actualizar
        $arguments['cellphone']	= $this->_request['cellphone'];	 //Tel�fono celular
        $arguments['phone']	= $this->_request['phone'];	 //Tel�fono fijo
        
        //Valida que ingrese email, y que sea valido.
        if (Validate::isEmail($arguments['email']) && !empty($arguments['email'])){
            if(!$update){
                if(Customer::customerExists($arguments['email'])){
                    // Si las entradas son inválidas, mensaje de estado "Bad Request" y la razón
                    $this->response($this->json(array(
                        "success" => false, 
                        "message" => "No se pudo crear la cuenta, el (".$arguments['email']." ) email ya esta registrado"
                    )), 400);
                }
            }
        } else {
            $this->response($this->json(array(
                "success" => false, 
                "message" => "se requiere un correo valido (".$arguments['email'].' )' 
            )), 400);
        }
        
        if (!Validate::isPasswd($arguments['passwd']) && isset($arguments['update']) && empty($arguments['update']))
            $this->response($this->json(array(
                "success" => false, 
                "message" => "La contraseña no es valida, utiliza una contraseña con una longitud mínima de 5 caracteres." 
            )), 400);	

        $model = new Model();
        if($customer = $model->setAccount($arguments)) {
            $this->response($this->json( $customer ),200);
        }

        $this->response($this->json(array(
            "success" => false, 
            "message" => "Error creando la cuenta."
        )), 400);
    }


    private function updateAccount()
    {
        $this->createAccount(TRUE);
    }

    private function addresses()
    {
        // Validación Cross si el método de la petición es GET de lo contrario volverá estado de "no aceptable"
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        } 

        $id_customer	= $this->_request['id_customer'];
        //$id_address	= $this->_request['id_address'];
        $model = new Model();		
        return $this->response(json_encode($model->get_address($id_customer,$id_address)),200);	
    } 			

    private function setAddress()
    {        
        // Validación Cross si el método de la petición es GET de lo contrario volverá estado de "no aceptable"
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        } 

        $arg = array();

        $arg['id_customer'] = $this->_request['id_customer'];
        $arg['id_country'] = $this->_request['id_country'];
        $arg['id_state'] = $this->_request['id_state'];
        $arg['alias'] = $this->_request['alias'];
        $arg['lastname'] = $this->_request['lastname'];
        $arg['firstname'] = $this->_request['firstname'];
        $arg['address1'] = $this->_request['address1'];
        $arg['address2'] = $this->_request['address2'];
        $arg['city'] = $this->_request['city'];
        $arg['phone'] = $this->_request['phone'];
        $arg['mobile'] = $this->_request['mobile'];
        $arg['dni'] = $this->_request['dni'];
        $arg['postcode'] = $this->_request['postcode'];	
        $arg['id_colonia'] = $this->_request['id_colonia'];
        $arg['is_rfc'] = $this->_request['is_rfc'];
        $arg['id_city'] = $this->_request['id_city'];
        $arg['id'] = $this->_request['id'];

        $model = new Model();		
        return $this->response(json_encode($model->set_address($arg)),200);
    }

    private function getPostCodeInfo() 
    {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        } 

        $postcode	= $this->_request['postcode'];
        $model = new Model();

        return $this->response(json_encode($model->get_fromPostcode($postcode)),200);	
    }	

    private function getColoniaByIdCity() 
    {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        } 

        $id_city	= $this->_request['id_city'];
        $model = new Model();

        return $this->response(json_encode($model->get_colonia_fromid_city($id_city)),200);	
    }

    private function countries() 
    {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        } 

        $model = new Model();

        return $this->response(json_encode($model->get_countries()),200);	
    }
    
    /**
     * 
     */
    private function states()
    {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
	} 
	$id_country = 	$this->_request['id_country'];

	$model = new Model();

	return $this->response(json_encode($model->get_states($id_country)),200);	
    }
    
	
    /**
     * 
     */
    private function cities()
    {
        $model = new Model();
	return $this->response(json_encode($model->get_cities()),200);	
    }

    /**
     * 
     */
    private function personalinformation()
    {
	$id_customer = $this->_request['id_customer'];

	$model = new Model();
	return $this->response(json_encode($model->personalinformation($id_customer)),200);
    }
    
    /**
     * 
     */
    private function sevedCreditCard()
    {
	$id_customer = $this->_request['id_customer'];

	$model = new Model();
	return $this->response(json_encode($model->sevedCreditCard($id_customer)),200);
    }

    /**
     * 
     */
    private function savepersonalinformation()
    {       
        $params = array();

        $params["id_customer"] = $this->_request['id_customer'];
        $params["password"] = $this->_request['password'];
        $params["password_new"] = $this->_request['password_new'];
        $params["id_gender"] = $this->_request['id_gender'];
        $params["firstname"] = $this->_request['firstname'];
        $params["lastname"] = $this->_request['lastname'];
        $params["email"] = $this->_request['email'];
        $params["dni"] = $this->_request['dni'];
        $params["birthday"] = $this->_request['birthday'];
        $params["civil_status"] = $this->_request['civil_status'];
        $params["occupation_status"] = $this->_request['occupation_status'];
        $params["field_work"] = $this->_request['field_work'];
        $params["pet"] = $this->_request['pet'];
        $params["pet_name"] = $this->_request['pet_name'];
        $params["spouse_name"] = $this->_request['spouse_name'];
        $params["children"] = $this->_request['children'];
        $params["phone_provider"] = $this->_request['phone_provider'];
        $params["phone"] = $this->_request['phone'];
        $params["address1"] = $this->_request['address1'];
        $params["address2"] = $this->_request['address2'];
        $params["city"] = $this->_request['city'];

	$model = new Model();
	$this->response( $this->json($model->savepersonalinformation($params)) , 200 );
    }
    
    private function createCustomer() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        
        $complete = false;
        $message = "";
        $error = array();

        try {
            $firstname = $this->_request['firts_name'];
            $lastname = $this->_request['last_name'];
            $email = $this->_request['email'];
            $phone = $this->_request['phone'];
            $birthday = $this->_request['date'];
            $addres1 = $this->_request['address'];
            $city = $this->_request['city'];
            $type_dni = $this->_request['type_identification'];
            $dni = $this->_request['number_identification'];
            $username = $this->_request['user_name'];
            $addres2 = $this->_request['address2'];        

            $valid_dni = Db::getInstance()->getRow('SELECT COUNT(dni) as dni 
                                                    FROM '._DB_PREFIX_.'customer WHERE dni = "'.$dni.'" ');

            $valid_username = Db::getInstance()->getRow('SELECT COUNT(username)  as username 
                                                         FROM '._DB_PREFIX_.'customer WHERE username = "'.$username.'" ');

            if (empty($firstname) || empty($lastname) || !Validate::isName($firstname) || !Validate::isName($lastname)) {
                $error[] = 'Nombre o Apellido invalido.';
            } elseif (!Validate::isEmail($email)) {
                $error[] = 'El correo electronico es invalido.';
            } elseif ( Validate::isIdentification($dni) || empty($dni) ) {
                $error[] = 'El numero de identificacion es invalido.';
            } elseif ($valid_dni['dni'] > 0) {
                $error[] = 'El numero de identificacion se encuentra en uso.';
            } elseif ($valid_username['username'] > 0) {
                $error[] = 'El nombre de usuario se encuentra en uso.';
            } elseif (RewardsSponsorshipModel::isEmailExists($email) || Customer::customerExists($email)) {
                $error[] = 'El correo electronico se encuentra en uso.';
            }

            if ( empty($error) ) {
                // Agregar Cliente
                $customer = new Customer();
                $customer->firstname = $firstname;
                $customer->lastname = $lastname;
                $customer->email = $email;
                $customer->passwd = Tools::encrypt($dni);
                $customer->dni = $dni;
                $customer->username = $username;
                $customer->birthday = $birthday;
                $customer->id_default_group = 4;
                $customer->kick_out = 0;
                $customer->active = 1;
                $customer->id_lang = Context::getContext()->language->id;
                $customer->date_kick_out = date('Y-m-d H:i:s', strtotime('+30 day', strtotime(date("Y-m-d H:i:s"))));
                $saveCustomer = $customer->add();
                $customer->updateGroup(array("3","4"));

                // Agregar Direccion
                $address = new Address();
                $address->id_country = 69;
                $address->dni = $customer->dni;
                $address->id_customer = $customer->id;
                $address->alias = 'Mi Direccion';
                $address->firstname = $customer->firstname;
                $address->lastname = $customer->lastname;
                $address->address1 = $addres1;
                $address->address2 = $addres2;
                $address->city = $city;
                $address->phone = $phone;
                $address->phone_mobile = $phone;
                $address->type_document = $type_dni;
                $address->active = 1;
                $saveAddress = $address->add();

                // Agregar Sponsor
                $sponsor = Db::getInstance()->executeS('SELECT
                                                        c.id_customer,
                                                        c.username,
                                                        c.email,
                                                        (2 - COUNT(rs.id_sponsorship)) pendingsinvitation
                                                    FROM '._DB_PREFIX_.'customer c
                                                    LEFT JOIN '._DB_PREFIX_.'rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                                                    LEFT JOIN '._DB_PREFIX_.'customer_group cg ON ( c.id_customer = cg.id_customer AND cg.id_group = 4 )
                                                    WHERE c.active = 1
                                                    AND c.kick_out = 0
                                                    GROUP BY c.id_customer
                                                    HAVING pendingsinvitation > 0
                                                    ORDER BY c.id_customer ASC
                                                    LIMIT 1');
                $sponsorship = new RewardsSponsorshipModel();
                $sponsorship->id_sponsor = $sponsor[0]['id_customer'];
                $sponsorship->id_customer = $customer->id;
                $sponsorship->firstname = $customer->firstname;
                $sponsorship->lastname = $customer->lastname;
                $sponsorship->email = $customer->email;
                $sponsorship->channel = 1;
                $saveSponsorship = $sponsorship->save();

                if ( $saveCustomer && $saveAddress && $saveSponsorship ) {
                    $complete = true;

                    $vars = array(
                        '{username}' => $customer->username,
                        '{password}' => $customer->dni,
                        '{firstname}' => $customer->firstname,
                        '{lastname}' => $customer->lastname,
                        '{dni}' => $customer->dni,
                        '{birthdate}' => $customer->birthday,
                        '{address}' => $address->address1,
                        '{phone}' => $address->phone,
                        '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                        '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                        '{shop_url_personal}' => Context::getContext()->link->getPageLink('identity', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                        '{learn_more_url}' => "http://reglas.fluzfluz.co",
                    );

                    $template = 'welcome_fluzfluz';
                    $prefix_template = '16-welcome_fluzfluz';

                    $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
                    $row_subject = Db::getInstance()->getRow($query_subject);
                    $message_subject = $row_subject['subject_mail'];

                    $allinone_rewards = new allinone_rewards();
                    $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject),$vars, $sponsorship->email, $customer->firstname.' '.$customer->lastname);
                } else {
                    $error[] = 'Se ha producido un error en el registro. Por favor verifica tus datos he intenta de nuevo.';
                }
            }
        } catch (Exception $e) {
            $error[] = 'Se ha producido un error en el registro. Por favor verifica tus datos he intenta de nuevo.';
            $message = $e->getMessage();
        }
        
        if ( !$complete ) {
            DB::getInstance()->execute("DELETE FROM "._DB_PREFIX_."customer WHERE id_customer = ".$customer->id);
            DB::getInstance()->execute("DELETE FROM "._DB_PREFIX_."customer_group WHERE id_customer = ".$customer->id);
            DB::getInstance()->execute("DELETE FROM "._DB_PREFIX_."address WHERE id_address = ".$address->id);
            DB::getInstance()->execute("DELETE FROM "._DB_PREFIX_."rewards_sponsorship WHERE id_sponsorship = ".$sponsorship->id);
        }
        
        $response = array('success' => $complete, 'error' => $error, 'message' => $message);        
        $this->response( $this->json($response) , 200 );
    }
    
    private function getPhonesCustomer() {
        $telconumbers = DB::getInstance()->executeS( "SELECT phone_mobile, default_number
                                                        FROM "._DB_PREFIX_."address
                                                        WHERE phone_mobile != ''
                                                        AND id_customer = ".$this->_request['id_customer']."
                                                        ORDER BY phone_mobile" );
        $this->response( $this->json($telconumbers) , 200 );
    }

    private function addPhoneCustomer() {
        $query = "SELECT *
                    FROM "._DB_PREFIX_."address
                    WHERE id_customer = ".$this->_request['id_customer']."
                    LIMIT 1";
        $address = Db::getInstance()->executeS($query);
        $address = $address[0];

        $queryInsert = "INSERT INTO "._DB_PREFIX_."address
                        VALUES (NULL,".$address['id_country'].", 0, ".$this->_request['id_customer'].", 0, 0, 0, 'Mi Direccion', '', '".$address['lastname']."', '".$address['firstname']."', '".$address['address1']."', '".$address['address2']."', '', '".$address['city']."', '', ".$address['phone'].", ".$this->_request['phone'].", '', ".$address['type_document'].", ".$address['dni'].", ".$address['checkdigit'].", NOW(), NOW(), 1, 0, 0, 0, 0)";
        
        $addphone = DB::getInstance()->execute($queryInsert);
        $this->response( $this->json($addphone) , 200 );
    }

    private function setPhonesRecharged() {
        $response = true;
        $cart = $this->_request['id_cart'];
        $phones = $this->_request['phones'];

        $queryDelete = "DELETE FROM "._DB_PREFIX_."webservice_external_telco
                        WHERE id_cart = ".$cart;
        $response += DB::getInstance()->execute($queryDelete);
        
        if ( !empty($phones) ) {
            foreach ($phones as $product => $phone) {
                $queryInsert = "INSERT INTO "._DB_PREFIX_."webservice_external_telco(id_cart, id_product, phone_mobile)
                                VALUES(".$cart.", ".$product.", ".$phone.")";
                $response += DB::getInstance()->execute($queryInsert);
            }
        }
        
        $this->response( $this->json($response) , 200 );
    }

   
  /**
   * AddVoucher 
   */
  private function cart() {
    
    if($this->get_request_method() != "POST") {
      $this->response('',406);
    }

    if (isset($this->_request['option']) && !empty($this->_request['option'])) {
      $option = $this->_request['option'];
    }
    
    if (isset($this->_request['idCustomer']) && !empty($this->_request['idCustomer'])) {
      $context->customer = new Customer((int) $this->_request['idCustomer']);
    }
    
    $model = new Model();
    $link = new Link();
    
//    error_log("\nEn App: -- Option: ".print_r($option,true),3,"/tmp/error.log");
    //Agrega al carrito
    if ( $option == 1 ){
      $requestData = array(
        'idCart' => 0,
        'idProduct' => 0,
        'qty' => 1,
        'op' => 'down'
      );
    
      foreach ($requestData as $rqd => $value) {
        ${$rqd} = isset($this->_request[$rqd]) ? $this->_request[$rqd] : $value;
      }
      
//      error_log("\nEn App: -- Option: ".print_r($idCart." - ".$idProduct." - ".$qty." - ".$op,true),3,"/tmp/error.log");
      $cart = $model->setCart($idCart, $idProduct, $qty, $op);      
    }
    //Actualiza carrito
    else if( $option == 2 ){
      $cartData = $this->_request['cart'];
      $cart = $model->updateAllProductQty( $cartData );
    }
    //Agrega descuento carrito
    else if( $option == 3 ){
      $cartData = $this->_request['cart'];
      $points = $this->_request["points"];
      $cart = $model->applyPoints( $cartData["id"],$points );
    }
      
    if (!is_array($cart)) {
      $this->response($this->json(array(
        'success' => false,
        'message' => $cart
      )), 400);
    }
    if( $cart['success'] ){
      foreach ($cart['products'] as &$product) {
        $product['app_price_shop'] = $this->formatPrice($product['price_shop']);
        $product['app_total'] = $this->formatPrice($product['total']);
        $product['app_price_in_points'] = $this->formatPrice($product['price_in_points']);
        $product['image_manufacturer'] = $link->getManufacturerImageLink($product['id_manufacturer']);
        $sql = "select online_only from "._DB_PREFIX_."product where id_product = ".$product['id_product'];
        $product['online_only'] = Db::getInstance()->getValue($sql);;
      }
      $cart['app_total_price_in_points'] = $this->formatPrice($cart['total_price_in_points']);
      $this->response($this->json($cart), 200);
    }
    $this->response($this->json(array(
      "success" => true, 
      "message" => "Se elimin� el carrito."
    )), 204);
  }
  
  private function getBanner(){
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    $model = new Model();
    $link = new Link();
    $banners = $model->getBannerElements($this->id_lang_default, true);
    foreach ($banners['result'] as &$banner){
      $banner['b_img'] = $link->getBannerImageLink((int)$banner['b_id']);
    }
//    error_log("\n\n Esta es la respuesta del banner: ".print_r($banners,true),3,"/tmp/error.log");
    return $this->response(json_encode($banners),200);
  }
  
  private function getCategory(){
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    if (isset($this->_request['option']) && !empty($this->_request['option'])) {
      $option = $this->_request['option'];
    }
    
    $model = new Model();
    $link = new Link();
    if( $option == 1 ){
      $categories = $model->getCategoriesHome($this->id_lang_default, true, true, true, 3, 5, true);
      //error_log("\n\nEntro a opcion 1: \n\n Categorias:\n\n".print_r($categories,true),3,"/tmp/error.log");
      
      foreach ($categories['result'] as $key => &$category) {
        $category['img_category'] = $link->getCategoryImageLink($category['id_category']);
      }
    }
    else if( $option == 2 ){
      $limit = (isset($this->_request['limit']) && !empty($this->_request['limit'])) ? $this->_request['limit'] : 0 ;
      $categories = $model->getCategoriesHome($this->id_lang_default, true, false, true, $limit, 0, false);
      foreach ($categories['result'] as $key => &$category) {
        $category['img_category'] = $link->getCategoryImageLink($category['id_category']);
      }
    }
    else if( $option == 3 ){
//      error_log("\n\nEntro a opcion 3: ",3,"/tmp/error.log");
      if (isset($this->_request['id_category']) && !empty($this->_request['id_category'])) {
        $id_category = $this->_request['id_category'];
      }
      else {
        $this->response('', 206);
      }
      
      $categories['products'] = $model->getCategories( $this->id_lang_default , $id_category, 0, 1, 1, false );
      foreach ($categories['products'] as $key => &$product) {
        $product['image'] = $link->getManufacturerImageLink($product['m_id']);
        $product['pf_points'] = round($product['pf_points']);
      }
    }
    return $this->response(json_encode($categories),200);
  }
        
  
    public function phoneProviders()
    {
        $model = new Model();
	return $this->response( $this->json( $model->phoneProviders() ) , 200 );	
    }

    /**
     * 
     */

    public function searchFluzzer()
    {
        if($this->get_request_method() != "POST") {
            $this->response('',406);
        }
        
        $params = array();
        $params["searchBox"] = $this->_request['searchBox'];

	$model = new Model();
	$this->response( $this->json($model->searchFluzzer($params)) , 200 );
    }

    /**
     * 
     */

    public function transferFluz()
    {
        if($this->get_request_method() != "POST") {
            $this->response('',406);
        }
        
        $params = array();
        $params["user"] = $this->_request['user'];
        $params["fluzzer"] = $this->_request['fluzzer'];
        $params["points"] = $this->_request['points'];

	$model = new Model();
	$this->response( $this->json($model->transferFluz($params)) , 200 );
    }
    
    /**
     * 
     */

    public function pay()
    {
        if($this->get_request_method() != "POST") {
            $this->response('',406);
        }
        
        $params = array();
        
        $params["method"] = "payulatam";
        $params["payment"] = $this->_request['payment'];
        $params["id_cart"] = $this->_request["id_cart"];
        $params["id_customer"] = $this->_request["id_customer"];
        
        // Tarjeta Credito
        $params["namecard"] = $this->_request["namecard"];
        $params["numbercard"] = $this->_request["numbercard"];
        $params["datecard"] = $this->_request["datecard"];
        $params["codecard"] = $this->_request["codecard"];
        $params["checkautorizationcard"] = (bool)$this->_request["checkautorizationcard"];
        
        // Tarjeta Debito
        $params["bank"] = $this->_request["bank"];
        $params["bankname"] = $this->_request["bankname"];
        $params["typecustomer"] = $this->_request["typecustomer"];
        $params["typedocument"] = $this->_request["typedocument"];
        $params["numberdocument"] = $this->_request["numberdocument"];

	$model = new Model();
	$this->response( $this->json($model->pay($params)) , 200 );
    }

    public function payFreeOrder()
    {
        if($this->get_request_method() != "POST") {
            $this->response('',406);
        }
        
        $params = array();
        
        $params["method"] = "bankwire";
        $params["payment"] = "Pedido gratuito";
        $params["id_cart"] = $this->_request["id_cart"];
        $params["id_customer"] = $this->_request["id_customer"];

	$model = new Model();
	$this->response( $this->json($model->payFreeOrder($params)) , 200 );
    }

    public function bankPse()
    {
	return $this->response( $this->json(PasarelaPagoCore::get_bank_pse()) , 200 );	
    }

    public function KeysOpenPay()
    {
	return $this->response($this->json(PasarelaPagoCore::get_keys_open_pay('Tarjeta_credito')),200);	
    }

    public function franquicia()
    {
	$cart_number = 	$this->_request['cart_number'];
	$this->response(json_encode( PasarelaPagoCore::getFranquicia($cart_number, 'payulatam')),200);
    }

    public function addImg()
    {
        // Validación Cross si el método de la petición es POST de lo contrario volverá estado de "no aceptable"
	if($this->get_request_method() != "POST") {
            $this->response('',406);
	}

	//$str_img = 	$this->_request['str_img'];
	$option = 	$_REQUEST['option']; //$this->_request['option'];

	$model = new Model();

	$flag = true;
	foreach ($_FILES as $key) {
            if(!$model->add_image($key,$option)){
                $flag = false;
                break;
            }
	}
	$this->response(json_encode(array('success'=>$flag)),200);
    }

    public function password()
    {
	$email = $this->_request['email'];

	/*if ($this->get_request_method() != "POST") {
		$this->response('', 406);
	}*/
	$model = new Model();
	
//exit(json_encode($email));
	return $this->response($this->json($model->password($email)),200);	
    }


    /**
     * Retorna las ordenes generadas por un usuario
     */
    public function getHistory()
    {    
	$id_customer = 	$this->_request['id'];
	$orders_out = array();
	if ($orders = Order::getCustomerOrders($id_customer))
            $contador = 0;
	foreach ($orders as &$order)
	{
            $contador ++;
            $myOrder = new Order((int)$order['id_order']);
            if (Validate::isLoadedObject($myOrder))
                $order['virtual'] = $myOrder->isVirtual(false);

            $order_state = Db::getInstance()->getValue("SELECT  `name` FROM ps_order_state_lang WHERE id_order_state = ". (int) $order['current_state']);

            $date = new DateTime($order['date_add']);	
            $address = new Address((int) $order['id_address_invoice']);
            $address_str = 	$address->address1.' '.$address->address2.' '.$address->city.'. C.P. '.$address->postcode;	
            $orders_out[] = array('id' => (int) $order['id_order'] ,
                  'state' =>  $order_state ,
                  'ref' => $order['reference'] ,
                  'id_customer' => (int) $order['id_customer'] ,
                  'id_cart' => (int) $order['id_cart'] ,
                  'id_address_delivery' => (int) $order['id_address_delivery'] ,
                  'id_address_invoice' => (int) $order['id_address_invoice'] ,
                  'address' => $address_str,
                  'payment' => $order['payment'] ,
                  'gift_message' => $order['gift_message'] ,
                  'total' => (float) $order['total_paid'] ,
                  'total_shipping' => (float) $order['total_shipping'] ,
                  'total_products' => (float) $order['total_products'] ,
                  'total_discounts' => (float) $order['total_discounts'] ,
                  'invoice_number' => (int) $order['invoice_number'],
                  'date_add' => $date->format("d/m/Y"),
                  'order_detail' => $this->orderDetail((int) $order['id_order']));
            if($contador == 20)
            break;
        }
        return $this->response($this->json($orders_out),200);
    }



    private function orderDetail($id_order = NULL)
    {
        /*if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }*/

        $id = $this->_request['id'];
        $model = new Model();
        if($id_order != NULL)
            return $model->get_order_datail($id_order);

        $this->response($this->json($model->get_order_datail($id)),200);
    }


    private function docTypes()
    {
        $model = new Model();
        $this->response($this->json($model->get_type_docs()),200);
    }


    private function tracker()
    {
        /*if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }*/

        $id_order = 	$this->_request['id'];
        $model = new Model();
        $this->response($this->json($model->get_traker_order($id_order)),200);
    }

    private function callback()
    {
        if ($this->get_request_method() != "GET" && $this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $model = new Model();
        //$this->response($this->json($model->get_traker_order($id_order)),200);

        $accountObj = $model->call_api($_REQUEST['accessToken'],"https://www.googleapis.com/plus/v1/people/me");

        return $this->response(json_encode($accountObj),200);
    }
    

    /**
     * Genera la ruta relativa de las imágenes publicitarias por directorio
     * dirname : <Nombre del directorio que contiene las imágenes>
     */
    private function publicityBanners()
    {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!isset($this->_request['dirname']) 
            || empty($this->_request['dirname'])) {
                $this->response('', 204);
        }

        $dirname = $this->_request['dirname'];
        $dir = "../publicity/banners/" . $dirname . "/";
        $images = glob($dir . "*.jpg");
        $this->response(json_encode($images), 200);
    }
    
    
    public function getVaultData(){
      if ($this->get_request_method() != "GET") {
        $this->response('', 406);
      }
      if (isset($this->_request['id_customer']) && !empty($this->_request['id_customer'])) {
        $id_customer = $this->_request['id_customer'];
        $model = new Model();
        $link = new Link();
        $countPurchases = 0;
        if (isset($this->_request['id_manufacturer']) && !empty($this->_request['id_manufacturer']) && $this->_request['id_manufacturer'] != null) {
          $id_manufacturer = $this->_request['id_manufacturer'];
          $purchases = $model->getVaultByManufacturer($id_customer, $id_manufacturer);
          foreach ($purchases['result'] as &$purchase){
//            $purchase['card_code'] = (int)$purchase['card_code'];            
            $purchase['price'] = round($purchase['price']);
            $purchase['formatPrice'] = $this->formatPrice($purchase['price']);
            $purchase['showDetails'] = false;
            $countPurchases++;
          }
          $purchases['total'] = $countPurchases;
          return $this->response(json_encode($purchases),200);
        }
        
        $purchases = $model->getVault($id_customer, $this->id_lang_default);
        foreach ($purchases['result'] as &$purchase){
          $purchase['total'] = round($purchase['total']);
          $purchase['m_img'] = $link->getManufacturerImageLink($purchase['id_manufacturer']);
          $countPurchases++;
        }
        $purchases['total'] = $countPurchases;
        return $this->response(json_encode($purchases),200);
      }
      else {
        $this->response('', 204);
      }

    }
    
    public function getNetwork() {
      if ($this->get_request_method() != "GET") {
        $this->response('', 406);
      }
      
      if (isset($this->_request['id_customer']) && !empty($this->_request['id_customer'])) {
        $id_customer = $this->_request['id_customer'];
        $object_inv = $this->_request['obj_inv'];
        $model = new Model();
        $link = new Link();
        $result = array();
        $option = isset($this->_request['option']) && !empty($this->_request['option']) ? $this->_request['option'] : 0;
        $limit = (isset($this->_request['limit']) && !empty($this->_request['limit'])) ? $this->_request['limit'] : 0 ;
        $last_total = (isset($this->_request['last_total']) && !empty($this->_request['last_total'])) ? $this->_request['last_total'] : 0 ;
//        error_log("\n\n 1- Esto es option que llega: \n\n".print_r($option,true),3,"/tmp/error.log");
        
        if( $option == 1 ){
          $activityNetwork = $model->getActivityNetwork( $this->id_lang_default, $id_customer, $limit );
          foreach ($activityNetwork['result'] as &$activityNetworkk){
            $activityNetworkk['credits'] = round($activityNetworkk['credits']);
            $activityNetworkk['img'] = $link->getManufacturerImageLink($activityNetworkk['id_manufacturer']);
          }
          if ( $limit != 0 ){
            for ( $i = $last_total; $i < $limit; $i++ ) {
              $result[] = $activityNetwork['result'][$i];
            }
          }
          return $this->response(json_encode(array('result' => $result)),200);
        }
        else if ( $option == 2 ){
          $my_network = $model->getMyNetwork( $this->id_lang_default, $id_customer );
          $max_limit = count($my_network['result']);
          $limit = ( $limit <= $max_limit ) ? $limit : $max_limit;
          if ( $limit != 0 ){
            for ( $i = $last_total; $i < $limit; $i++ ) {
              $result[] = $my_network['result'][$i];
            }
          }
          else {
            $result = $my_network['result'];
          }
          return $this->response(json_encode(array('result' => $result)),200);
        }
        elseif ( $option == 3 ) {
          $my_network = $model->getMyInvitation( $this->id_lang_default, $id_customer );
          $max_limit = count($my_network['result']);
          $limit = ( $limit <= $max_limit ) ? $limit : $max_limit;
          if ( $limit != 0 ){
            for ( $i = $last_total; $i < $limit; $i++ ) {
              $result[] = $my_network['result'][$i];
            }
            if($max_limit == 1){
                $result[0]['contador'] = $max_limit;
            }else{
                $result[0]['contador'] = $max_limit;
                $result[1]['contador'] = $max_limit;
            }
            
          }
          else{
              $result[] = $my_network['result'][$i];
              $result[0]['contador'] = $max_limit;
          }
          
          
//          error_log("\n\nEste es el codigo 1: ".print_r($result, true),3,"/tmp/error.log");

          return $this->response(json_encode(array('result' => $result)),200);
        }
        else if ( $option == 4 ){
          $my_network = $model->getMyNetworkInvitations( $this->id_lang_default, $id_customer );
          $max_limit = count($my_network['result']);
          $limit = ( $limit <= $max_limit ) ? $limit : $max_limit;
          if ( $limit != 0 ){
            for ( $i = $last_total; $i < $limit; $i++ ) {
              $result[] = $my_network['result'][$i];
            }
          }
          return $this->response(json_encode(array('result' => $result)),200);
        }
        else if ( $option == 5 ) { 
          $object_inv = json_decode($object_inv, true);  
          $invitation = $model->getSendInvitation( $this->id_lang_default, $id_customer, $object_inv );
          return $this->response(json_encode(array('result' => $invitation)),200);
        }
      }
      else {
        $this->response('', 204);
      }
    }
    
    public function redemption() {
      if ($this->get_request_method() != "GET") {
        $this->response('', 406);
      }
      
      $requestData = array(
        'identification' => '',
        'firts_name' => '',
        'last_name' => '',
        'card' => '',
        'account' => '',
        'bank' => '',
        'points' => '',
        'credits' => ''
      );
    
      //llena las variables de busqueda.
      foreach ($requestData as $rqd => $value) {
        ${$rqd} = isset($this->_request[$rqd]) ? $this->_request[$rqd] : $value;
      }
      
      $sql = "INSERT INTO 
                "._DB_PREFIX_."rewards_payment (
                  nit_cedula,
                  nombre,
                  apellido,
                  numero_tarjeta,
                  tipo_cuenta,
                  banco,
                  points,
                  credits,
                  detail,
                  invoice,
                  paid
                )
              VALUES (
                ".$identification.", '".$firts_name."', '".$last_name."', ".$card.", '".$account."', '".$bank."', ".$points.", ".$credits.", 0, 0, '-".$credits."'
              )";
      
      $result = Db::getInstance()->ExecuteS($sql);
      
      return $this->response(json_encode(array('result' => $result)),200);
    }
    
  public function sendMessage() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    $id_customer_send = $this->_request['id_customer_send'];
    $id_customer_receive = $this->_request['id_customer_receive'];
    $message = $this->_request['message'];
    
    $query = "INSERT INTO "._DB_PREFIX_."message_sponsor(id_customer_send, id_customer_receive, message, date_send)
              VALUES (".$id_customer_send.", ".$id_customer_receive.", '".$message."', NOW())";
    $result = Db::getInstance()->execute($query);
    return $this->response(json_encode(array('result' => $result)),200);
  }
  
  public function getConversations() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    $id_customer = $this->_request['id_customer'];
    
    // Lista de conversaciones
    $sql = "SELECT CAST(
                CONCAT(
                    IF(id_customer_send=".$id_customer.",'',id_customer_send) , IF(id_customer_receive=".$id_customer.",'',id_customer_receive)
                ) AS INT
            ) AS customer
            FROM ps_message_sponsor
            WHERE (
                (id_customer_receive=".$id_customer." AND id_customer_send<>".$id_customer.") OR (id_customer_receive<>".$id_customer." AND id_customer_send=".$id_customer.")
            )
            GROUP BY customer
            ORDER BY customer";
    $conversations = Db::getInstance()->executeS($sql);

    foreach ($conversations as &$conversation) {
        // Username usuario en conversacion
        $sql = "SELECT username
                FROM ps_customer
                WHERE id_customer = ".$conversation["customer"];
        $conversation["username"] = Db::getInstance()->getValue($sql);

        // Numero de mensajes sin leer
        $sql = "SELECT
                  COUNT(*) unread_messages
                FROM ps_message_sponsor
                WHERE (id_customer_send = ".$conversation["customer"]." AND id_customer_receive = ".$id_customer.")
                AND `read` = 0";
        
        $conversation["unread_messages"] = Db::getInstance()->getValue($sql);
        
        // Ultimo mensaje recibido o enviado en conversacion
        $sql = "SELECT
                    message,
                    date_send,
                    UNIX_TIMESTAMP(date_send) date_send_ts,
                    IF(
                        date_send>=DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00'),
                        DATE_FORMAT(date_send, '%H:%i'),
                        DATE_FORMAT(date_send, '%Y-%m-%d') 
                    ) date_show
                FROM ps_message_sponsor
                WHERE (id_customer_send = ".$id_customer." AND id_customer_receive = ".$conversation["customer"].")
                OR (id_customer_send = ".$conversation["customer"]." AND id_customer_receive = ".$id_customer.")
                ORDER BY date_send DESC
                LIMIT 1";
        $message = Db::getInstance()->executeS($sql);
        $conversation["message"] = $message[0]["message"];
        $conversation["date_show"] = $message[0]["date_show"];
        $conversation["date_send"] = $message[0]["date_send"];
        $conversation["date_send_ts"] = $message[0]["date_send_ts"];
    }

    // Ordenar conversaciones por fecha DESC
    usort($conversations, function($a, $b) {
        return  $b['date_send_ts'] - $a['date_send_ts'];
    });
    
//    error_log("\n\n\n\n Esto es query get messages: \n\n".print_r($sql, true),3,"/tmp/error.log");
    foreach ($conversations AS $key => &$conversation) {
      if ( file_exists(_PS_IMG_DIR_."profile-images/".(string)$id_customer.".png") ) {
        $conversation['img'] = "http://".Configuration::get('PS_SHOP_DOMAIN')."/img/profile-images/".(string)$id_customer.".png";
      }
    }
    
    return $this->response(json_encode(array('result' => $conversations)),200);
  }
  
  public function getConversation() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    $id_customer = $this->_request['id_customer'];
    $id_customer_conversation = $this->_request['id_customer_conversation'];
    
    $sql = "SELECT
                id_customer_send,
                id_customer_receive,
                message,
                `read`,
                date_send,
                DATE_FORMAT(date_send, '%Y-%m-%d') date,
                DATE_FORMAT(date_send, '%H:%i') hour
            FROM ps_message_sponsor
            WHERE (id_customer_send = ".$id_customer." AND id_customer_receive = ".$id_customer_conversation.")
            OR (id_customer_send = ".$id_customer_conversation." AND id_customer_receive = ".$id_customer.")
            ORDER BY date_send ASC";
    
//    error_log("\n\n\n Este es el que trae la conversacion: \n\n".print_r($sql, true),3,"/tmp/error.log");
    $conversation = Db::getInstance()->executeS($sql);
    return $this->response(json_encode(array('result' => $conversation)),200);
  }
  
  
  public function getPasscode() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    $id_customer = $this->_request['id_customer'];
    
    $sql = 'SELECT id_customer, vault_code 
            FROM '._DB_PREFIX_.'customer
            WHERE id_customer = '.$id_customer.';';
    $result = Db::getInstance()->executeS($sql);
    return $this->response(json_encode(array('result' => $result)),200);
  }
  
  public function setPasscode() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    $id_customer = $this->_request['id_customer'];
    $passcode = $this->_request['passcode'];
    
    $sql = 'UPDATE '._DB_PREFIX_.'customer
            SET vault_code = '.$passcode.'
            WHERE id_customer = '.$id_customer.';';
    $result = Db::getInstance()->execute($sql);
    return $this->response(json_encode(array('result' => $result)),200);
  }
  
  public function validatePasscode() {
    if($this->get_request_method() != "POST") {
      $this->response('',406);
    }
    
    $id_customer = $this->_request['id_customer'];
    $passcode = $this->_request['passcode'];
    
    $sql = 'SELECT vault_code
            FROM '._DB_PREFIX_.'customer
            WHERE id_customer = '.$id_customer.';';
    $passcode_db = Db::getInstance()->getValue($sql);
    $result = ( $passcode_db == $passcode ) ? true : false;
    
    if ( $result == true ){
      $this->response($this->json(array(
        "success" => true, 
        "message" => "Todo ok.",
        "result"  => $result
      )), 200);
    }
    else {
      $this->response($this->json(array(
        "success" => false, 
        "message" => "La contrase�a no coincide.",
        "result"  => $result
      )), 204);
    }
  }
    
  public function updateBonus() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    $card = $this->_request['card'];
    $used = $this->_request['used'];
    
    if( $used == 1 ){
      $value = $this->_request['price_card_used'];
      $setValue = Wallet::setValueUsed( $card, $value );
    }
    
    $setUsed = Wallet::setUsedCard( $card , $used );
    
    if ( $setUsed ){
      $sql = "SELECT id_product_code, used
              FROM "._DB_PREFIX_."product_code
              WHERE id_product_code = ".$card.";";
      $result = Db::getInstance()->getRow($sql);
    }
    return $this->response(json_encode(array('result' => $result)),200);
  }
  
  public function getPhoneByIdCustomer() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    $id_customer = $this->_request['id_customer'];
    
    $sql = "SELECT id_customer, phone
            FROM "._DB_PREFIX_."customer
            WHERE id_customer = ".$id_customer.";";
    
    $result = Db::getInstance()->getRow($sql);
    $result['formatPhone'] = str_repeat("X", (strlen($result['phone']) - 6)).substr($result['phone'], -4);
    return $this->response(json_encode(array('result' => $result)),200);
  }

  public function setPhoneByIdCustomer() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    $id_customer = $this->_request['id_customer'];
    $phone = $this->_request['phone'];
//    error_log("n\n Este es el telefono: \n".print_r($phone,true),3,"/tmp/error.log");
    $sql = 'UPDATE '._DB_PREFIX_.'customer
            SET phone = '.$phone.'
            WHERE id_customer = '.$id_customer.';';
//    error_log("n\n Este es el telefono: \n".print_r($sql,true),3,"/tmp/error.log");
    $result = Db::getInstance()->execute($sql);
    return $this->response(json_encode(array('result' => $result)),200);
  }

  public function sendSMSConfirm() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    $id_customer = $this->_request['id_customer'];
    
    $sql = "SELECT phone
            FROM "._DB_PREFIX_."customer
            WHERE id_customer = ".$id_customer.";";
    $phone = Db::getInstance()->getValue($sql);
    $numberConfirm = rand(100000, 999999);
    $updateNumberConfirm = 'UPDATE '._DB_PREFIX_.'customer
                            SET app_confirm = '.$numberConfirm.'
                            WHERE id_customer = '.$id_customer.';';
    $result = Db::getInstance()->execute($updateNumberConfirm);
//      $msg = "Tu n�mero de confirmaci�n para FLuzFluz es: ".$numberConfirm;
    $curl = curl_init();
    
    $url = Configuration::get('APP_SMS_URL').$phone."&messagedata=".$numberConfirm;
    
    curl_setopt($curl, CURLOPT_URL, $url);
    $response = curl_exec($curl);
    curl_close($curl);
    
    $result = ( $response == 1 ) ? "Se ha enviado el sms." : "No se pudo enviar el sms";
    return $this->response(json_encode(array('result' => $result)),200);
  }
  
  
  private function confirm() {
    if($this->get_request_method() != "POST") {
      $this->response('',406);
    }
    $code =  trim( $code != NULL ? $code : $this->_request['confirmNumber']);
    $id_customer = $this->_request['id_customer'];

    $sql = "SELECT app_confirm
          FROM "._DB_PREFIX_."customer
          WHERE id_customer = ".$id_customer.";";

    $app_confirm = Db::getInstance()->getValue($sql);

    if( $code == $app_confirm ){
      $this->response($this->json(array(
                "success" => true, 
                "message" => "Usuario confirmado"
                )), 200);
    }
    else {
      $this->response($this->json(array(
          "success" => true, 
          "message" => "El n�mero de verificaci�n no coincide."
          )), 204);
    }
  }
    
  private function getAddressMaps() {
    if($this->get_request_method() != "GET") {
      $this->response('',406);
    }
    $latitude = $this->_request['latitude'];
    $longitude = $this->_request['longitude'];
    $option = $this->_request['option'];
    
    if ( $option == 2 ){
      $id_manufacturer = $this->_request['id_manufacturer'];
    }
    
//    error_log("\n\nEstos son los datos\n\n",3,"/tmp/error.log");
//    error_log("\n\nLatitude ".print_r($latitude,true),3,"/tmp/error.log");
//    error_log("\n\nLongitude ".print_r($longitude,true),3,"/tmp/error.log");
//    error_log("\n\nManufacturer ".print_r($id_manufacturer,true),3,"/tmp/error.log");
    // Unidades de distancia ( Metros )
    $units = "units=metric";
    
    // El origen de donde calcula las distancias:
    $origins = 'origins='.$latitude.','.$longitude;
    
    // Ubicaciones de las ciudades;
    $cities['latitudes'] = Db::getInstance()->executeS("SELECT latitud_inicial, latitud_final
                                                      FROM "._DB_PREFIX_."cities
                                                      WHERE latitud_inicial is not null
                                                      ORDER BY latitud_inicial");
    
    $cities['longitudes'] = Db::getInstance()->executeS("SELECT longitud_inicial, longitud_final
                                                      FROM "._DB_PREFIX_."cities
                                                      WHERE latitud_inicial is not null
                                                      ORDER BY longitud_inicial");
    
    // Capturo la ciudad en la que estoy.
    foreach ($cities['latitudes'] as $latitudes){
      if ( $latitudes['latitud_inicial'] >= $latitude && $latitude >= $latitudes['latitud_final'] ){
        $city['latitude'] = $latitudes;
      }
    }
    
    foreach ($cities['longitudes'] as $longitudes){
      if ( $longitudes['longitud_inicial'] >= $longitude && $longitude >= $longitudes['longitud_final'] ){
        $city['longitude'] = $longitudes;
      }
    }
    
    // Traigo todas las posiciones dentro de mi ciudad ($city)
    $sql = "SELECT a.latitude, a.longitude, count(a.latitude) as size
            FROM "._DB_PREFIX_."address as a
            INNER JOIN "._DB_PREFIX_."manufacturer as m on (m.id_manufacturer = a.id_manufacturer)            
            WHERE a.latitude < ".$city['latitude']['latitud_inicial']."
            and a.latitude > ".$city['latitude']['latitud_final']." 
            and a.longitude < ".$city['longitude']['longitud_inicial']." 
            and a.longitude > ".$city['longitude']['longitud_final']."
            and m.active = 1
            and a.active = 1
            GROUP BY a.latitude, a.longitude";
        
    if ($option == 2){
      if($id_manufacturer != ''){
        $sql .= ' and id_manufacturer = '.$id_manufacturer.';';
      }
      else {
        return $this->response(json_encode(array('result' => '')),206);
      }
    }
//    error_log("\n\n\n Este es el query de posiciones: \n\n".print_r($sql,true),3,'/tmp/error.log');
    
    $positions = Db::getInstance()->executeS($sql);
    
    // Destinos
    foreach($positions as &$pos) {
      $pos['distance'] = $this->getDistanceToCoords($latitude,$longitude,$pos['latitude'],$pos['longitude']);
    }
    usort($positions, function($a, $b) {
      return str_replace('.', ',', $a['distance']) > str_replace('.', ',', $b['distance']) ? 1:-1 ;
    });
    
    // Mostrar 10 resultados cercanos
//    for($i = 0; $i <= 10; $i++){
//      $result[] = $positions[$i];
//    }
    
    foreach ($positions as $pos){
      if(floatval($pos['distance']) <= 10){
        $result[] = $pos;
      }
    }
    array_unique($result);
    
    return $this->response(json_encode(array('result' => $result)),200);
  }

  public function getDistanceToCoords($lat1,$lon1,$lat2,$lon2) {
    $R = 6371; // Radius of the earth in km
    $dLat = $this->deg2rad($lat2-$lat1);  // deg2rad below
    $dLon = $this->deg2rad($lon2-$lon1); 
    $a = 
      sin($dLat/2) * sin($dLat/2) +
      cos($this->deg2rad($lat1)) * cos($this->deg2rad($lat2)) * 
      sin($dLon/2) * sin($dLon/2); 
    $c = 2 * atan2(sqrt($a), sqrt(1-$a)); 
    $d = $R * $c; // Distance in km
    return $d;
  }
  
  public function deg2rad($deg) {
    return $deg * (M_PI/180);
  }
  
  
  private function getNotificationBarOrders(){
    if($this->get_request_method() != "GET") {
      $this->response('',406);
    }
    $id_customer = $this->_request['id_customer'];
    
    $model = new Model();
    $result = $model->getNotificationOrder($id_customer);
    
    $this->response($this->json($result), 200);
  }
}


// Access-Control-Allow-Origin | CORS
//header("Access-Control-Allow-Origin: *");
//header("Access-Control-Allow-Credentials: true");
//header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Key, Authorization");
//header("Access-Control-Allow-Headers: Content-Type,x-prototype-version,x-requested-with");
//header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE, PATCH");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
header('Access-Control-Allow-Credentials: true');

// Iniciar
$api = new API;
$api->processApi();

?>
