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
        
    }
    
    /**
     * M�todo de pruebas
     */
    private function confirm() {
      if($this->get_request_method() != "POST") {
        $this->response('',406);
      }

      $code =  trim( $code != NULL ? $code : $this->_request['confirmNumber']);
//      error_log("\n\nEste es el codigo 1: ".print_r($this->_request['confirmNumber'], true),3,"/tmp/error.log");
//      error_log("\n\nEste es el codigo 2: ".print_r($code, true),3,"/tmp/error.log");

      if( !empty($code) ){
        if ( $code != 00000 ){
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
      else {
        $this->response($this->json(array(
          "success" => false, 
          "message" => "El n�mero de verificaci�n no v�lido."
        )), 400);
      }
    }
    
    private function myAccountData(){
      if ($this->get_request_method() != "GET") {
        $this->response('', 406);
      }
      
      $id_customer =  trim( $this->_request['userId']);
      $context = Context::getContext();
      $MyAccountController = new MyAccountController();
      $userData = $MyAccountController->getUserDataAccountApp( $id_customer );
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

    
  private function search() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    $requestData = array(
      'param' => '',
      'option' => 0
    );
    
    //llena las variables de busqueda.
    foreach ($requestData as $rqd => $value) {
      ${$rqd} = isset($this->_request[$rqd]) ? $this->_request[$rqd] : $value;
    }
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
    
    //Si la busqueda es por tienda, Busqueda 1
    if( $option == 1 ){
      $manufacturer = $search['result'];
      for ($i = 0; $i < count($manufacturer); $i++) {
        $manufacturer[$i]['image_manufacturer'] = $this->protocol . $link->getManufacturerImageLink($manufacturer[$i]['m_id']);
        $manufacturer[$i]['m_points'] = round($manufacturer[$i]['m_points']);
        $prices = explode(",", $manufacturer[$i]['m_prices']);
        $price_min = round($prices[0]);
        $price_max = round($prices[ count($prices) - 1 ]);
        $manufacturer[$i]['prices'] = $price_min." - ".$price_max;
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
        $productFather[$i]['prices'] = $price_min." - ".$price_max;
      }
      $search['result'] = $productFather;
      $this->response($this->json($search), 200);
    }
    else if ( $option == 3 ){
      $productChild = $search['result'];
//      error_log("\n*****************************************\n\nsearch: \n\n".print_r($search,true),3,"/tmp/error.log");
      for ($i = 0; $i < count($productChild); $i++){
        $productChild[$i]['c_price'] = round($productChild[$i]['c_price']);
        $productChild[$i]['c_percent_save'] = round( ( ( $productChild[$i]['c_price_shop'] - $productChild[$i]['c_price'] )/ $productChild[$i]['c_price_shop'] ) * 100 );
        $productChild[$i]['c_win_fluz'] = round( ( $productChild[$i]['c_price'] * ( $productChild[$i]['c_price'] / 100 ) )/ 25 );
        $productChild[$i]['c_price_fluz'] = round( $productChild[$i]['c_price'] / 25 );
        
      }
      $search['result'] = $productChild;
      $this->response($this->json($search), 200);
    }
    
  }
    
    
    
    
    
  private function searchold() {
    // Validación Cross si el método de la petición es GET de lo contrario volverá estado de "no aceptable"
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    $arguments = array();
    $requestData = array(
    'expr' => '',
    'page_number' => 1,
    'page_size' => 10,
    'order_by' => "position",
    'order_way' => "asc"
    );
    foreach ($requestData as $rqd => $value) {
      ${$rqd} = isset($this->_request[$rqd]) ? $this->_request[$rqd] : $value;
    }

    //$model = new Model();
    //$result = $model->productSearch($this->id_lang_default, $expr, $page_number, $page_size, $order_by,  $order_way);

    $search = Search::find($this->id_lang_default, $expr, $page_number, $page_size, $order_by, $order_way, false, false);

    if (!isset($search['result']) || empty($search['result'])) {
      $this->response($this->json(array(
      'success' => false,
      'message' => ':('
      )), 400);
    }
    
    $context = Context::getContext();
    $link = new Link();
    $search['expr'] = $expr;
    $search['page_number'] = (int) $page_number;
    $search['total'] = (int) $search['total'];
    $search['total_pages'] = ceil($search['total'] / $page_size);

    $products = $search['result'];

    for ($i = 0; $i < count($products); $i++) {
      $cover = Product::getCover($products[$i]['id_product'], $context);
      $products[$i]['id_image'] = (int) $cover['id_image'];
      $products[$i]['image'] = $this->protocol . $link->getImageLink($products[$i]['link_rewrite'], $cover['id_image'], 'large_default');
      $products[$i]['image_manufacturer'] = $this->protocol . $link->getManufacturerImageLink($products[$i]['id_manufacturer']);
      $products[$i]['total_format_price'] = Product::convertAndFormatPrice(Product::getPriceStatic($products[$i]['id_product']));
    }

    $search['result'] = $products;

    if (empty($search)) {
      // Si no hay registros, estado "Sin contenido"
      $this->response('', 204);
    } else {
      // Si todo sale bien, enviará cabecera de "OK" y la lista de la búsqueda en formato JSON
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
	if ($this->get_request_method() != "GET") {
            $this->response('', 406);
	} 
	$id_state = $this->_request['id_state'];
        $model = new Model();

	return $this->response(json_encode($model->get_cities($id_state)),200);	
    }
	

    private function costoEnvio()
    {
	if ($this->get_request_method() != "GET") {
		$this->response('', 406);
	} 
	$id_city = $this->_request['id_city'];

	$model = new Model();

	return $this->response(json_encode($model->get_costo_envio($id_city)),200);		
    }
    
    /**
     * AddVoucher 
     */
    private function cart()
    {
      if($this->get_request_method() != "POST") {
        $this->response('',406);
      }
      $requestData = array(
        'idProduct' => 0,
        'option' => 1
      );
      foreach ($requestData as $rqd => $value) {
        ${$rqd} = isset($this->_request[$rqd]) ? $this->_request[$rqd] : $value;
      }
      
      error_log("\n\nEsto es lo que recibe: ".print_r($idProduct,true),3,"/tmp/error.log");
      error_log("\nEsto es lo que recibe: ".print_r($option,true),3,"/tmp/error.log");
            
      $this->response($this->json(array(
        'success' => true,
        'message' => 'Si estoy llegando, y obtengo: idProduct: '.$idProduct.' option: '.$option
      )), 200);
      
      

      $param['products'] = 		$this->_request['products'];
      $param['id_customer'] = 	$this->_request['id_customer'];
      $param['discounts'] = 		$this->_request['discounts'];
      $param['deleteDiscount'] = 	$this->_request['deleteDiscount'];
      $param['id_address'] = 		$this->_request['id_address'];
      $param['msg'] = 			$this->_request['msg'];
      $param['id_cart'] = 		($this->_request['id_cart'] > 0 ? $this->_request['id_cart'] : NULL);
      $param['clear'] = 		(!empty($this->_request['clear'])  ? (boolean) $this->_request['clear'] : FALSE);

      $model = new Model();
      $this->response($this->json($model->cart($param['products'],$param['id_customer'],$param['id_address'],$param['discounts'],$param['deleteDiscount'],$param['msg'],$param['id_cart'],$param['clear'])),200);
    }
    
    /**
     * 
     */

    public function pay()
    {
	$param['payment'] = 	$this->_request['payment'];
	$param['products'] = 	$this->_request['products'];
	$param['id_customer'] = 	$this->_request['id_customer'];
	$param['id_address'] = 	$this->_request['id_address'];
	$param['discounts'] = 		$this->_request['discounts'];
	$param['msg'] = 			$this->_request['msg'];
	$param['id_cart'] = 		($this->_request['id_cart'] > 0 ? $this->_request['id_cart'] : NULL);			

	$model = new Model();
	$this->response($this->json($model->pay($param)),200);	
    }

    public function bankPse()
    {
	return $this->response($this->json(PasarelaPagoCore::get_bank_pse()),200);	
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
