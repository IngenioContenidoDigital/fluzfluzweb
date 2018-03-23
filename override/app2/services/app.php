<?php
require_once('classes/Rest.inc.php');
require_once('classes/Model.php');
include_once(_PS_CLASS_DIR_.'order/Order.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/allinone_rewards.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsSponsorshipModel.php');
include_once(_PS_MODULE_DIR_.'/allinone_rewards/controllers/front/sponsorship.php');
include_once(_PS_MODULE_DIR_.'/bitpay/bitpay.php');

class API extends REST {

  public $id_lang_default = 0;
  
  public function __construct(){
    parent::__construct(); // Init parent contructor
    $this->id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
  }

  /**
   * Método público para el acceso a la API.
   * Este método llama dinámicamente el método basado en la cadena de consulta
   *
   */
  public function processApi(){
    $func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
    if((int)method_exists($this,$func) > 0)
      $this->$func();
    else
      $this->response('No funciona',404); // If the method not exist with in this class, response would be "Page not found".
  }
  
  /**
   * Método privado para dar formato a los precios.
   * @param int $number
   * @return string
   */
  private function formatPrice($number){
    return number_format($number, 0, '', '.');
  }
  
  /**
   * Método privado para obtener los datos de la cuenta.
   * @param string $id_customer Id de usuario.
   * @return json $userData Todos los datos de la cuenta de usuario.
   */  
  private function myAccountData(){
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    if( is_numeric(trim($this->_request['userId'])) ){
      $id_customer = trim($this->_request['userId']);
    }
    else {
      $this->response('', 202);
    }
    
    $context = Context::getContext();
    $MyAccountController = new MyAccountController();
    $userData = $MyAccountController->getUserDataAccountApp( $id_customer );
    $userData['totalMoney'] = $this->formatPrice( $userData['fluzTotal'] * 25 );
    return $this->response($this->json($userData), 200);
  }
    
  /**
   * Método privado para obtener la informacion del perfil del cualquier usuario en la red del cliente.
   * @param int $id_customer Id de usurio.
   * @param int $id_profile Id de usuario del perfil a obtener.
   * @return json Perfil de usuario.
   */  
  private function getProfile(){
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    if( is_numeric(trim($this->_request['id_customer'])) &&
        is_numeric(trim($this->_request['id_profile'])) ){
      $id_customer = trim($this->_request['id_customer']);
      $id_profile = trim( $this->_request['id_profile']);
    }
    else {
      $this->response('', 202);
    }
    
    $model = new Model();
    $result = $model->getProfileById($id_customer, $id_profile);
    return $this->response($this->json($result), 200);
  }
  
  /**
   * Método privado que obtiene los usuarios invitados con su estado a partir de un id de usuario.
   * @param int $id_customer Id de usurio
   * @return json Usuarios invitados y el total de usuarios.
   */
  private function getInviteduserForProfile() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    $id_customer = trim($this->_request['id_customer']);
    if( !is_numeric(trim($id_customer)) ){
      $this->response('', 202);
    }

    $model  = new Model();
    $result = $model->getMyInvitation($id_lang = 1, $id_customer );
    $result['total'] = count($result['result']);
    return $this->response($this->json($result), 200);
  }
  
  /**
   * Método privado que recibe el id de cliente, el lenguaje y retorna los números de teléfono de ese cliente.
   * @param int $id_customer Id de usuario
   * @param int $id_lang Id de idioma
   * @return Array $phone Teléfonos.
   */
  private function getTelephoneByCustomer($id_customer, $id_lang = 1) {
    $customer = new Customer($id_customer);
    $addresses = $customer->getAddresses($id_lang);
    $phone = array();
    foreach ($addreses as $key => $address) {
      $phone[$key] =  $address['phone_mobile'];
    }
    return $phone;
  }

  /**
   * Método privado que Codifica el array en un JSON
   * @param array $data Arreglo de datos
   * @return json Arreglo en formato json
   */
  private function json($data){
    if(is_array($data)){
      return json_encode($data);
    }
  }
  
  /**
   * Método privado que valida una latitud
   * @param string $lat
   * @return boolean
   */
  private function isLat($lat) {
    return preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/', trim($lat));
  }
  
  /**
   * Método privado que valida una latitud
   * @param string $lat
   * @return boolean
   */
  private function isLng($lng) {
    return preg_match('/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', trim($lng));
  }
  
  /**
   * Método privado que busca comercios segun la ubicación en el mapa con su ubicación.
   * @param string $position['lat'] Latitud
   * @param string $position['lng'] Longitud
   * @return json Resultado de la busqueda de comercios.
   */
  private function searchByMap() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    $position['lat'] =  round($this->_request['lat'], 6);
    $position['lng'] =  round($this->_request['lng'], 6);
    if( !$this->isLat($position['lat']) || !$this->isLng($position['lng']) ){
      $this->response('', 202);
    }
    
    $query = 'SELECT GROUP_CONCAT(DISTINCT id_manufacturer)
              FROM  '._DB_PREFIX_.'address
              WHERE latitude = '.$position['lat'].' and longitude = '.$position['lng'];
    $manufacturers = Db::getInstance()->getValue($query);
    $search = Search::findApp( $manufacturers, 4 );
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
  
  /*
   * Método privado que busca segun la opcion que reciba.
   * @param string $param Parametro o término de búsqueda.
   * @param int $option Opcion de busqueda:
   *                      1- Busca el $param por comercios.
   *                      2- Busca los productos padre de un comercio.
   *                      3- Busca los productos hijos de un producto padre.
   * @param int $limit Número que limita la cantidad de resultados a retornar.
   * @param int $lastTotal Número que indica la cantida de resultados ya retornados, para omitirlos y tomar los siguientes
   *                       n números como indique $limit.
   * @return json El resultado de la búsqueda.
   */
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
    
    if( !Validate::isCheckDigit(trim($option)) ||
        !is_numeric($limit) ||
        !is_numeric($lastTotal) ){
      $this->response('', 202);
    }
    
    $words = Search::sanitize($param, 1, false, 'es');
    
    //Hace la busqueda
    $search = array();
    $search = Search::findApp( $words, $option );
    
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
    
    //Si la busqueda es por comercio, Busqueda 1
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
    //Si la busqueda es por producto padre, Búsqueda 2
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
    //Si la busqueda es por producto hijo, Búsqueda 3
    else if ( $option == 3 ){
      $productChild = $search['result'];
      for ($i = 0; $i < count($productChild); $i++){
        $productChild[$i]['c_price'] = round($productChild[$i]['c_price']);
        $productChild[$i]['c_percent_save'] = round( ( ( $productChild[$i]['c_price_shop'] - $productChild[$i]['c_price'] )/ $productChild[$i]['c_price_shop'] ) * 100 );
        $productChild[$i]['c_price_shop_format'] = $this->formatPrice(round($productChild[$i]['c_price_shop']));
        $productChild[$i]['c_win_fluz'] = (round( $model->getPoints( $productChild[$i]['c_id_product'], $productChild[$i]['c_price'] ) ));
        $productChild[$i]['c_price_fluz'] = $this->formatPrice(round( $productChild[$i]['c_price'] / 25 ));
        $productChild[$i]['c_price'] = $this->formatPrice(round($productChild[$i]['c_price']));
        
      }
      $search['result'] = $productChild;
      $this->response($this->json($search), 200);
    }
    
  }
  
  /**
   *  Método privado que autentica a un usuario en la aplicación.
   * @param string $email Correo electronico del usuario
   * @param string $password Contraseña del usuario
   * @return json Informacion del usuario
   */
  private function login(){
    if($this->get_request_method() != "POST") {
      $this->response('',406);
    }
    
    
    $email    = strtolower( trim( $this->_request['email'] ) );
    $password = trim( $this->_request['pwd'] );
    
    if( !Validate::isEmail($email) || !Validate::isPasswd($password) ){
      $this->response($this->json(array('error'=> 1)), 200);
    }
    
    // Validaciones de entrada
    if(!empty($email) and !empty($password)) {
      if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $customer = new Customer();
        $authentication = $customer->getByEmail($email, $password);
        if (!$authentication || !$customer->id) {
          $this->response($this->json(array('success'=>false, 'error'=> 2)), 200);	// Si no hay registros, estado "No Content"
        }
        else {
          $context = Context::getContext();
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
          $context->cookie->phone = $customer->phone;                    

          // Agrega el cliente a el contexto
          $context->customer = $customer;

          // Si todo sale bien, enviarÃ¡ cabecera de "OK" y los detalles del usuario en formato JSON
          unset($customer->passwd, $customer->last_passwd_gen);
          $gender = $customer->id_gender  == 1 ? 'M' : ($customer->id_gender  == 2 ? 'F' : "");
          $sql = "SELECT code
                  FROM ps_rewards_sponsorship_code
                  WHERE id_sponsor = ".$customer->id;
        
          $refer_code = DB::getInstance()->getValue($sql);
          $array = array(
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
                'phone' => $customer->phone,
                'refer_code' => $refer_code,
                'error' => 0,
                'success' => TRUE);

          $this->response($this->json($array), 200);
        }
      }
    }

    // Si las entradas son inválidas, mensaje de estado "Bad Request" y la razon
    $this->response($this->json(array(
      "success" => false,
      "error" => 3,
      "message" => "DirecciÃ³n de correo electrÃ³nico o contraseÃ±a no vÃ¡lidos"
    )), 200);
  }
  
  /**
   * Método privado que cierra la sesion del usuario.
   * @return boolean Verdadero o falso.
   */
  private function logout(){
    $context = Context::getContext();
    $context->customer->mylogout();
    $this->response(true, 200);
  }
  
  /**
   * Método privado que retorna las ciudades.
   * @return json Ciudades
   */
  private function cities(){
    if($this->get_request_method() != "POST") {
      $this->response('',406);
    }
    
    $model = new Model();
    return $this->response(json_encode($model->get_cities()),200);	
  }

  /**
   * Método privado que retorna la información personal del usuario por id de cliente.
   * @param int $id_cliente
   * @return json Informacion personal del id de cliente.
   */
  private function personalInformation(){
    if($this->get_request_method() != "POST") {
      $this->response('',406);
    }
    
    $id_customer = trim($this->_request['id_customer']);
    if( !is_numeric(trim($id_customer)) ){
      $this->response('', 202);
    }
    
    $model = new Model();
    return $this->response(json_encode($model->personalinformation($id_customer)),200);
  }
    
  /**
   * Método privado que retorna la tarjeta de credito almacenada.
   * @param int $id_customer Id de usuario
   * @return json Informacion de la tarjeta de crédito
   */
  private function sevedCreditCard(){
    if($this->get_request_method() != "POST") {
      $this->response('',406);
    }
     
    $id_customer = $this->_request['id_customer'];
    if( !is_numeric(trim($id_customer)) ){
      $this->response('', 202);
    }
    $model = new Model();
    $result['success'] = true;
    $result['card'] = $model->sevedCreditCard($id_customer);
    return $this->response(json_encode($result),200);
  }

  /**
   * Método privado que Guarda la informacion personal.
   * @params int id_customer 
   * @params int password
   * @params int password_new
   * @params int id_gender
   * @params string firstname
   * @params string lastname
   * @params string email
   * @params int dni
   * @params string birthday
   * @params int civil_status
   * @params string occupation_status
   * @params string field_work
   * @params string pet
   * @params string pet_name
   * @params string spouse_name
   * @params string children
   * @params string phone_provider
   * @params int phone
   * @params string address1
   * @params string address2
   * @params string city
   * @return json Resultado de la actualizacion de datos.
   */
  private function savePersonalInformation(){
    if($this->get_request_method() != "POST") {
      $this->response('',406);
    }
    
    $params = array();
    $params["id_customer"]       = $this->_request['id_customer'];
    $params["password"]          = $this->_request['password'];
    $params["password_new"]      = $this->_request['password_new'];
    $params["id_gender"]         = $this->_request['id_gender'];
    $params["firstname"]         = $this->_request['firstname'];
    $params["lastname"]          = $this->_request['lastname'];
    $params["email"]             = $this->_request['email'];
    $params["dni"]               = $this->_request['dni'];
    $params["birthday"]          = $this->_request['birthday'];
    $params["civil_status"]      = $this->_request['civil_status'];
    $params["occupation_status"] = $this->_request['occupation_status'];
    $params["field_work"]        = $this->_request['field_work'];
    $params["pet"]               = $this->_request['pet'];
    $params["pet_name"]          = $this->_request['pet_name'];
    $params["spouse_name"]       = $this->_request['spouse_name'];
    $params["children"]          = $this->_request['children'];
    $params["phone_provider"]    = $this->_request['phone_provider'];
    $params["phone"]             = $this->_request['phone'];
    $params["address1"]          = $this->_request['address1'];
    $params["address2"]          = $this->_request['address2'];
    $params["city"]              = $this->_request['city'];
//    error_log("\n\n Informacion personal: \n\n".print_r($params, true),3,"/tmp/error.log");
    
//    $error = 0;
//    if( is_numeric(trim($params["id_customer"])) ){
//      if( Validate::isPasswd(trim($params["password"])) ){
//        if( (!empty(trim($params["password_new"])) && Validate::isPasswd(trim($params["password_new"]))) || empty(trim($params["password_new"])) ){
//          if( !empty(trim($params["id_gender"])) && Validate::isCheckDigit($params["id_gender"]) ){
//            if( !empty(trim($params["firstname"])) && Validate::isName($params["firstname"]) ){
//              if( !empty(trim($params["lastname"])) && Validate::isName($params["lastname"]) ){
//                if( !empty(trim($params["email"])) && Validate::isEmail($params["email"]) ){
//                  if( !empty(trim($params["dni"])) && Validate::isDniLite($params["dni"]) ){
//                    if( !empty(trim($params["birthday"])) && Validate::isDateFormat($params["birthday"]) ){
//                      if( !empty(trim($params["civil_status"])) && Validate::isString($params["civil_status"]) ){
//                        if( !empty(trim($params["occupation_status"])) && Validate::isString($params["occupation_status"]) ){
//                          if( !empty(trim($params["field_work"])) && Validate::isString($params["field_work"]) ){
//                            if( !empty(trim($params["pet"])) && Validate::isString($params["pet"]) ){
//                              if( !empty(trim($params["pet_name"])) && Validate::isString($params["pet_name"]) ){
//                                if( !empty(trim($params["spouse_name"])) && Validate::isString($params["spouse_name"]) ){
//                                  if( !empty(trim($params["children"])) && is_numeric($params["children"]) ){
//                                    if( !empty(trim($params["phone_provider"])) && Validate::isString($params["phone_provider"]) ){
//                                      if( !empty(trim($params["phone"])) && Validate::isPhoneTelcoNumber($params["phone"]) ){
//                                        if( !empty(trim($params["address1"])) && Validate::isAddress($params["address1"]) ){
//                                          if( !empty(trim($params["address2"])) && Validate::isAddress($params["address2"]) ){
//                                            if( !empty(trim($params["city"])) && !Validate::isCityName($params["city"]) ){
//                                              $error = 21;
//                                            }
//                                          }else{$error = 20;}
//                                        }else{$error = 19;}
//                                      }else{$error = 18;}
//                                    }else{$error = 17;}
//                                  }else{$error = 16;}
//                                }else{$error = 15;}
//                              }else{$error = 14;}
//                            }else{$error = 13;}
//                          }else{$error = 12;}
//                        }else{$error = 11;}
//                      }else{$error = 10;}
//                    }else{$error = 9;}
//                  }else{$error = 8;}
//                }else{$error = 7;}
//              }else{$error = 6;}
//            }else{$error = 5;}
//          }else{$error = 4;}
//        }else{$error = 3;}
//      }else{$error = 2;}
//    }else {$error = 1;}

    $model = new Model();
    $this->response( $this->json($model->savepersonalinformation($params)) , 200 );
  }
  
  /**
   * Metodo privado que crea un usuario
   * @params string firts_name
   * @params string last_name
   * @params string email
   * @params int phone
   * @params string date
   * @params string address
   * @params string city
   * @params string type_identification
   * @params int number_identification
   * @params string user_name
   * @params string address2
   * @params int cod_refer
   * @return json Responde el estado de la creación de la cuenta.
   */
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
      $birthday = !empty($this->_request['date']) ? $this->_request['date'] : null;
      $addres1 = $this->_request['address'];
      $city = $this->_request['city'];
      $type_dni = $this->_request['type_identification'];
      $dni = $this->_request['number_identification'];
      $username = $this->_request['user_name'];
      $addres2 = !empty($this->_request['address2']) ? $this->_request['address2']: null;
      $cod_refer = $this->_request['cod_refer'];
      $password = $this->_request['password'];
      
      $valid_dni = Db::getInstance()->getRow('SELECT COUNT(dni) as dni 
                                              FROM '._DB_PREFIX_.'customer WHERE dni = "'.$dni.'" ');

      $valid_username = Db::getInstance()->getRow('SELECT COUNT(username)  as username 
                                                   FROM '._DB_PREFIX_.'customer WHERE username = "'.$username.'" ');
      
      $valid_phone = Db::getInstance()->getRow('SELECT COUNT(phone)  as phone 
                                                   FROM '._DB_PREFIX_.'customer WHERE phone = "'.$phone.'" ');

      if (empty($firstname) || empty($lastname) || !Validate::isName($firstname) || !Validate::isName($lastname)) {
        $error[] = utf8_encode('Nombre o Apellido inválido.');
      } elseif (!Validate::isEmail($email)) {
        $error[] = utf8_encode('El correo electrónico es inválido.');
      } elseif ( Validate::isIdentification($dni) || empty($dni) ) {
        $error[] = utf8_encode('El número de identificación es invalido.');
      } elseif ($valid_dni['dni'] > 0) {
        $error[] = utf8_encode('El número de identificación se encuentra en uso.');
      } elseif ($valid_username['username'] > 0) {
        $error[] = utf8_encode('El nombre de usuario se encuentra en uso.');
      } elseif (RewardsSponsorshipModel::isEmailExists($email) || Customer::customerExists($email)) {
        $error[] = utf8_encode('El correo electrónico se encuentra en uso.');
      } elseif ($valid_phone['phone'] > 0){
        $error[] = utf8_encode('El número de teléfono registrado ya está en uso.');
      }
            
      $code_generate = Allinone_rewardsSponsorshipModuleFrontController::generateIdCodeSponsorship($username);
            
        if ( empty($error) ) {
          // Agregar Cliente
          $customer = new Customer();
          $customer->firstname = $firstname;
          $customer->lastname = $lastname;
          $customer->email = $email;
          $customer->passwd = Tools::encrypt($password);
          $customer->dni = $dni;
          $customer->username = $username;
          $customer->birthday = $birthday;
          $customer->id_default_group = 4;
          $customer->kick_out = 0;
          $customer->active = 0;
          $customer->phone = $phone;
          $customer->id_lang = Context::getContext()->language->id;
          $customer->method_add = 'Movil App';
          $customer->date_kick_out = date('Y-m-d H:i:s', strtotime('+30 day', strtotime(date("Y-m-d H:i:s"))));
          $customer->date_add = date('Y-m-d H:i:s', strtotime('+0 day', strtotime(date("Y-m-d H:i:s"))));
          $saveCustomer = $customer->add();
          error_log("\n\n\n Add Customer: ".print_r($saveCustomer,true),3,"/tmp/error.log");
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

        if(!empty($cod_refer) && $cod_refer != '' && $cod_refer != NULL ){
          // Busca el sponsor.
          $id_sponsor = RewardsSponsorshipCodeModel::getIdSponsorByCode($cod_refer);
          $tree = RewardsSponsorshipModel::_getTree($id_sponsor);
          $sql_count_customer = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'rewards_sponsorship WHERE id_sponsor = '.$tree[0]['id']);

          array_shift($tree);
          //$count_array = count($tree);

          if ($sql_count_customer < 2){
            $sql_sponsor = "SELECT c.id_customer, c.username, c.firstname, c.lastname, c.email, (2-COUNT(rs.id_sponsorship) ) sponsoships
                            FROM " . _DB_PREFIX_ . "customer c
                            LEFT JOIN " . _DB_PREFIX_ . "rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                            WHERE c.id_customer =".$id_sponsor;

            $sponsor = Db::getInstance()->getRow($sql_sponsor);

            if (!empty($sponsor)) {
              $sponsorship = new RewardsSponsorshipModel();
              $sponsorship->id_sponsor = $sponsor['id_customer'];
              $sponsorship->id_customer = $customer->id;
              $sponsorship->firstname = $customer->firstname;
              $sponsorship->lastname = $customer->lastname;
              $sponsorship->email = $customer->email;
              $sponsorship->channel = 1;
              $send = "";
                   
              if ($sponsorship->save()) {
                $complete = true;
                $this->sendMailCofirmCreateAccount($customer, $address);
                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_sponsor, code)
                                            VALUES ('.$customer->id.', "'.$code_generate.'")');
              }
            }
            else {
              $error[] = 'El referido no es correcto.';
            }
          }
          else {
            $array_sponsor = array();
            foreach ($tree as $network) {
              $sql_sponsor = "SELECT c.id_customer, c.username, c.firstname, c.lastname, c.email, (2-COUNT(rs.id_sponsorship) ) sponsoships
                              FROM " . _DB_PREFIX_ . "customer c
                              LEFT JOIN " . _DB_PREFIX_ . "rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                              WHERE c.id_customer =" . (int) $network['id'] . "
                              HAVING sponsoships > 0";
              $sponsor = Db::getInstance()->getRow($sql_sponsor);

              if( $sponsor != '' && $sponsor['id_customer'] && $sponsor['id_customer'] != ''){
                array_push($array_sponsor, $sponsor);
              }
            }
            $sort_array = array_filter($array_sponsor);

            usort($sort_array, function($a, $b) {
              return $a['id_customer'] - $b['id_customer'];
            });

            $sponsor_a = reset($sort_array);
            
            if (!empty($sponsor_a) && ($sponsor_a['sponsoships'] > 0)) {
              $sponsorship = new RewardsSponsorshipModel();
              $sponsorship->id_sponsor = $sponsor_a['id_customer'];
              $sponsorship->id_customer = $customer->id;
              $sponsorship->firstname = $customer->firstname;
              $sponsorship->lastname = $customer->lastname;
              $sponsorship->email = $customer->email;
              $sponsorship->channel = 1;

              if ($sponsorship->save()) {
                $complete = true;
                $this->sendMailCofirmCreateAccount($customer, $address);
                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_sponsor, code)
                                            VALUES ('.$customer->id.', "'.$code_generate.'")');
              }
            }
            else {
              $error[] = 'El referido no es correcto.';
            } 
          }
        }
        else {
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

          Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'rewards_sponsorship_code (id_sponsor, code)
                                      VALUES ('.$customer->id.', "'.$code_generate.'")');

          if ( $saveCustomer && $saveAddress && $saveSponsorship ) {
            $complete = true;
            $this->sendMailCofirmCreateAccount($customer, $address);
          }else {
            $error[] = 'Se ha producido un error en el registro. Por favor verifica tus datos he intenta de nuevo.';
          }
        }
      }
    }
    catch (Exception $e) {
      $error[] = 'Se ha producido un error en el registro. Por favor verifica tus datos he intenta de nuevo.';
      $message = $e->getMessage();
    }

    if ( !$complete ) {
      DB::getInstance()->execute("DELETE FROM "._DB_PREFIX_."customer WHERE id_customer = ".$customer->id);
      DB::getInstance()->execute("DELETE FROM "._DB_PREFIX_."customer_group WHERE id_customer = ".$customer->id);
      DB::getInstance()->execute("DELETE FROM "._DB_PREFIX_."address WHERE id_address = ".$address->id);
      DB::getInstance()->execute("DELETE FROM "._DB_PREFIX_."rewards_sponsorship WHERE id_sponsorship = ".$sponsorship->id);
      DB::getInstance()->execute("DELETE FROM "._DB_PREFIX_."rewards_sponsorship_code WHERE id_sponsor = ".$sponsorship->id);
    }
    
    $sql = "SELECT code
                  FROM ps_rewards_sponsorship_code
                  WHERE id_sponsor = ".$customer->id;
        
    $refer_code = DB::getInstance()->getValue($sql);
    $gender = $customer->id_gender  == 1 ? 'M' : ($customer->id_gender  == 2 ? 'F' : "");
    $customerData = array(
      'id' => (int) $customer->id,
      'lastname' => $customer->lastname,
      'firstname' => $customer->firstname,
      'email' => $customer->email,
      'newsletter' => (bool)$customer->newsletter,
      'dni' => $customer->dni,
      'gender' => $gender,
      'birthday' => $customer->birthday,
      'website' => $customer->website,
      'company' => $customer->company,
      'active' => $customer->active,
      'kick_out' => $customer->kick_out,
      'manual_inactivation' => $customer->manual_inactivation,
      'days_inactive' => $customer->days_inactive,
      'autoaddnetwork' => $customer->autoaddnetwork,
      'dni' => $customer->dni,
      'phone' => $customer->phone,
      'refer_code' => $refer_code,
      'success' => TRUE);

    $response = array('success' => $complete, 'error' => $error, 'message' => $message, 'customer' => ($complete)?$customerData:'');
    $this->response( $this->json($response) , 200 );
  }
    
  /**
   * Método publico envia un correo de confirmación de creación de cuenta.
   * @param int $customer Objeto usuario con toda la información de usuario.
   * @param int $id_customer Id de usuario
   * @return json Informacion de la tarjeta de crédito
   */
  public function sendMailCofirmCreateAccount($customer, $address){
    error_log("\n\n\n\n Este es el usuario al método que envia el correo: ".print_r($customer,true),3,"/tmp/error.log");
    $vars = array(
      '{username}' => $customer->username,
      '{password}' =>  Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, 'id_customer='.(int)$customer->id),
//      '{password}' =>  Context::getContext()->link->getPageLink('password', true, null, 'token='.$customer->secure_key.'&id_customer='.(int)$customer->id.'&valid_auth=1'),                
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
    $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject),$vars, $customer->email, $customer->firstname.' '.$customer->lastname);
  }
    
  /**
   * Método privado que obtiene los números de teléfono del usuario
   * @param int $id_customer Id del usuario
   * @return json Números de teléfono.
   */
  private function getPhonesCustomer() {
      $telconumbers = DB::getInstance()->executeS( "SELECT phone_mobile, default_number
                                                      FROM "._DB_PREFIX_."address
                                                      WHERE phone_mobile != ''
                                                      AND id_customer = ".$this->_request['id_customer']."
                                                      ORDER BY phone_mobile" );
      $telcoResponse['success'] = true;
      $telcoResponse['error'] = ($telconumbers)?0:1;
      $telcoResponse['result'] = $telconumbers;
      $this->response( $this->json($telcoResponse) , 200 );
  }

  /**
   * Método privado que agrega un número de teléfono a un usuario.
   * @param int $id_customer Id del usuario
   * @param int $phone Teléfono a gregar.
   * @return json Resultado.
   */
  private function addPhoneCustomer() {
    if ($this->get_request_method() != "POST") {
      $this->response('', 406);
    }
    
    $error = 0;
    $query = "SELECT *
              FROM "._DB_PREFIX_."address
              WHERE id_customer = ".$this->_request['id_customer'];
    $address = Db::getInstance()->executeS($query);
      
    foreach ($address as $add){
      $error = ( $add['phone_mobile'] == $this->_request['phone'] ) ? 1 : $error;
    }
    if($error == 0){
      $address = $address[0];
      $queryInsert = "INSERT INTO "._DB_PREFIX_."address
                      VALUES (NULL,".$address['id_country'].",0,
                        ".$this->_request['id_customer'].",0,0,0,'Mi Direccion','','".$address['lastname']."',
                        '".$address['firstname']."','".$address['address1']."','".$address['address2']."','',
                        '".$address['city']."','',".$address['phone'].",".$this->_request['phone'].",'',
                        ".$address['type_document'].",".$address['dni'].",".$address['checkdigit'].",NOW(),NOW(),1,0,0,
                        0,0)";
      $addphone = DB::getInstance()->execute($queryInsert);
      $error = ($addphone == 1)?$error:2;
    }
    $response['success'] = true;
    $response['error'] = $error;
    $this->response( $this->json($response) , 200 );
  }
  
  /**
   * Método privado que agrega el teléfono de recarga al carrito.
   * @param int $id_customer Id del usuario
   * @param array $phones Arreglo de teléfonos
   * @return json Resultado
   */
  private function setPhonesRecharged() {
    if($this->get_request_method() != "POST") {
      $this->response('',406);
    }
    
    $error = (isset($this->_request['id_customer']) && !empty($this->_request['id_customer']) && is_numeric($this->_request['id_customer']) ) ? 0 :1;
    $error = (isset($this->_request['id_cart']) && !empty($this->_request['id_cart']) && is_numeric($this->_request['id_cart'])) ? 0 :1;
    
    if($error === 0){
      if($error == 0){
        $phones = $this->_request['phones'];
        $id_cart = $this->_request['id_cart'];
        $cart = new Cart($id_cart);
        $products = $cart->getProducts();
        $c_phone=0;
        foreach ($products as $product) {
          $c_phone += (substr($product['reference'], 0, 4) == "MOV-") ? 1 : 0;
        }
        $error = ($c_phone == count($phones)) ? $error : 2;
        if($error == 0){
          $context->customer = new Customer((int) $this->_request['id_customer']);
          $response = 0;
          $queryDelete = "DELETE FROM "._DB_PREFIX_."webservice_external_telco
                          WHERE id_cart = ".$id_cart;
          $r=DB::getInstance()->execute($queryDelete);
          
          foreach ($products as $product) {
            foreach ($phones as $prod => $phone) {
              if((substr($product['reference'], 0, 4) == "MOV-") && $product['id_product'] == $prod){
                $queryInsert = "INSERT INTO "._DB_PREFIX_."webservice_external_telco(id_cart, id_product, phone_mobile)
                                VALUES(".$id_cart.", ".$prod.", ".$phone.")";
                
                $response += DB::getInstance()->execute($queryInsert) ? 1 :0;
              }
            }
          }
          $error = ($response == $c_phone) ? $error : 3;
        }
      }
    }
    
    $returnResponse['success'] = true;
    $returnResponse['error'] = $error;
    $this->response( $this->json($returnResponse) , 200 );
  }

  /**
   * Método privado que controla el carrito de compras.
   * @param int $id_customer Id del usuario
   * @param int $option     Opción que especifica que desea hacer en el carrito de compras.
   *                        1- Agrega al carrito
   *                        2- Actualiza Carrito
   *                        3- Agrega descuento al carrito
   * @param int $idCart     Id del carrito a afectar
   * @param int $idProduct  Id del producto
   * @param int $qty        Cantidad de producto
   * @return json Resultado de la operación.
   */ 
  private function cart() {
    if($this->get_request_method() != "POST") {
      $this->response('',406);
    }
    
    if (isset($this->_request['option']) && !empty($this->_request['option'])) {
      $option = $this->_request['option'];
    }
    if (isset($this->_request['id_customer']) && !empty($this->_request['id_customer'])) {
      $context->customer = new Customer((int) $this->_request['id_customer']);
    }
    else{
      $this->response('',406);
    }
    
    $model = new Model();
    $link = new Link();
    
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

      $cart = $model->setCart($idCart, $idProduct, $qty, $op, $this->_request['id_customer']);
    }
    //Actualiza carrito
    else if( $option == 2 ){
      $cartData = $this->_request['cart'];
      $cart = $model->updateAllProductQty( $cartData );
      if(empty($cart['products']) || !$cart['products']){
        $cart['error'] = 1;
      }
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
      )), 200);
    }
    if( $cart['success'] ){
      foreach ($cart['products'] as &$product) {
        $product['app_price_shop'] = $this->formatPrice($product['price_shop']);
        $product['app_total'] = $this->formatPrice($product['total']);
        $sql = "SELECT date_add as date FROM "._DB_PREFIX_."cart_product WHERE id_cart = ".$cart['id']." and id_product = ".$product['id_product'];
        $product['date'] = Db::getInstance()->getValue($sql);
        $product['app_price_in_points'] = $this->formatPrice($product['price_in_points']);
        $product['image_manufacturer'] = $link->getManufacturerImageLink($product['id_manufacturer']);
        $sql = "select online_only from "._DB_PREFIX_."product where id_product = ".$product['id_product'];
        $product['online_only'] = Db::getInstance()->getValue($sql);
      }
      $cart['app_total_price_in_points'] = $this->formatPrice($cart['total_price_in_points']);
      $products =  $cart['products'];
      usort($products, function($a1, $a2) {
        $v1 = strtotime($a1['date']);
        $v2 = strtotime($a2['date']);
        return $v2 - $v1; // $v2 - $v1 to reverse direction
      });
      $cart['products'] = $products;
      $this->response($this->json($cart), 200);
    }
    $this->response($this->json(array(
      "success" => true, 
      "message" => "Se eliminó el carrito."
    )), 200);
  }
  
  /**
   * Método priva do que obtiene los banners
   * @return json Arreglo con la información de los banners.
   */
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
    return $this->response(json_encode($banners),200);
  }
  
  /**
   * Método que obtiene las categorias
   * @param int $option Opcion que especifica la forma de consultar las categorias.
   * @param int $limit Limite de categorias a consultar
   * @param int $id_category id de categoria a consultar
   * @return type
   */
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
  
  /**
   * Método público que trae los provedores de telefonia
   * @return json Provedores de telefonia
   */
  public function phoneProviders(){
    $model = new Model();
    return $this->response( $this->json( $model->phoneProviders() ) , 200 );	
  }
  
  public function getNameOneCategoryById(){
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    $id_category = $this->_request['id_category'];
    
    $model = new Model();
    return $this->response( $this->json( $model->getNameOneCategoryById($id_category) ) , 200 );	
  }
  
  /**
   * Método público para la búsqueda de usuarios (Fluzzers)
   * @param int $userID Id de usuario
   * @param string $searchBox Parametro de búsqueda
   * @return json Resultado de la búsqueda
   */
  public function searchFluzzer(){
    if($this->get_request_method() != "POST") {
      $this->response('',406);
    }

    $params = array();
    $params["searchBox"] = $this->_request['searchBox'];
    $params["userId"] = $this->_request['userId'];

    $model = new Model();
    $this->response( $this->json($model->searchFluzzer($params)) , 200 );
  }

  /**
   * Método privado para la transferencia de Fluz
   * @param int $user id de usuario emisor
   * @param int $fluzzer id de usuario receptor
   * @param int points Total de puntos a transferir
   * @return json Resultado de la transferencia
   */
  private function transferFluz(){
    if($this->get_request_method() != "POST") {
      $this->response('',406);
    }
        
    $params = array();
    $params["user"] = $this->_request['user'];
    $params["fluzzer"] = $this->_request['fluzzer'];
    $params["points"] = $this->_request['points'];

    $MyAccountController = new MyAccountController();
    $userData = $MyAccountController->getUserDataAccountApp( $params["user"] );
    $userData['fluzTotal'];

    $model = new Model();

    if( $params["points"] < $userData['fluzTotal'] || $params["points"] == $userData['fluzTotal'] ){
      $this->response( $this->json($model->transferFluz($params)) , 200 );
    }
    else {
      $this->response( $this->json('error: No tiene los puntos suficientes'), 206);
    }
  }
    
  /**
   * Método privado para el pago
   * @params int payment Método de pago
   * @params int id_cart Id del carrito de compras
   * @params int id_customer  Id del usuario
   * @params string namecard Nombre en la tarjeta
   * @params int numbercard Número en la tarjeta
   * @params string datecard Fecha de vercimiento de la tarjeta
   * @params string codecard Codigoi de verificación de la tarjeta
   * @params boolean checkautorizationcard Bandera para guardar la tarjeta o no.
   * @params int bank id Banco
   * @params string bankname Nombre del banco
   * @params string typecustomer Tipo de usuario
   * @params string typedocument Tipo de documento
   * @params int numberdocument Número de Documento
   * @return json Resultado del pago.
   */
  private function pay(){
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
  
  /**
   * Método privado que permite le pago gratuito mediante fluz
   * @param int $id_cart Id del carrito
   * @param int $id_customer Id del usuario
   * @return json Resultado del pago gratuito
   */
  private function payFreeOrder(){
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
  
  /**
   * Método privado que retorna los bancos de pse
   * @return json Bancos de pse
   */
  private function bankPse(){
    $result['banks'] = PasarelaPagoCore::get_bank_pse();
    
    $result['success'] = true;
    $result['error'] = (!empty($result['banks']) && is_array($result['banks']) && $result['banks']['error'] != 1) ? 0:1;
    return $this->response( $this->json($result) , 200 );	
  }
  
  /**
   * Método privado que retorna las llaves de apertura de pago
   * @return json Llaves de apertura de pago
   */
  private function KeysOpenPay(){
    return $this->response($this->json(PasarelaPagoCore::get_keys_open_pay('Tarjeta_credito')),200);	
  }

  /**
   * Método privado que retorna la franquicia
   * @param int $cart_number Numero de la tarjeta
   * @return json Información de l a franquisia
   */
  private function franquicia(){
    $cart_number = 	$this->_request['cart_number'];
    $this->response(json_encode( PasarelaPagoCore::getFranquicia($cart_number, 'payulatam')),200);
  }
  
  /**
   * Método privado que retorna el detalle de la orden.
   * @param int $id_order id de la orden a consultar
   * @return json Detalle de la orden
   */
  private function orderDetail($id_order = NULL){
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    $id = $this->_request['id'];
    $model = new Model();
    if($id_order != NULL){
      return $model->get_order_datail($id_order);
    }

    $this->response($this->json($model->get_order_datail($id)),200);
  }
  
  /**
   * Método privado que trae la información de la bóveda
   * @param int $id_customer Id de usuario
   * @return json Información de la bóveda
   */
  private function getVaultData(){
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    if (isset($this->_request['id_customer']) && !empty($this->_request['id_customer'])) {
      $id_customer = $this->_request['id_customer'];
      $model = new Model();
      $link = new Link();
      $countPurchases = 0;
      if ( !empty($this->_request['id_manufacturer']) && $this->_request['id_manufacturer'] != 'undefined' && $this->_request['id_manufacturer'] != 'null' ) {
        $id_manufacturer = $this->_request['id_manufacturer'];
        $bonus = $model->getVaultByManufacturer($id_customer, $id_manufacturer);
        $gift = $model->getVaultGiftByManufacturer($id_customer, $id_manufacturer);
        $purchases['result'] = ($gift['result'] !== 'vacio') ? array_merge($bonus['result'], $gift['result']) : $bonus['result'];
          
        foreach ($purchases['result'] as &$purchase){
          $purchase['card_code'] = (string)$purchase['card_code'];            
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
  
  /**
   * Método privado que obtiene la network
   * @param int $id_customer Id de usuario
   * @param type $obj_inv objeto de invitación
   * @param type $option opcion a ejecutar
   * @param type $limit limite de resultados
   * @param type $last_total último total de resultados
   * @return json Network
   */
  private function getNetwork() {
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
      
      if( $option == 1 ){
        $activityNetwork = $model->getActivityNetwork( $this->id_lang_default, $id_customer, $limit );
        foreach ($activityNetwork['result'] as &$activityNetworkk){
          $activityNetworkk['credits'] = round($activityNetworkk['credits']);
          $activityNetworkk['img_product'] = $link->getManufacturerImageLink($activityNetworkk['id_manufacturer']);
          $activityNetworkk['img'] = $link->getProfileImageLink($activityNetworkk['id_customer']);
        }
        $count = count($activityNetwork['result']);
        $limit = ($limit > $count) ? $count : $limit;
        for($i = $last_total; $i < $limit; $i++){
          $result['result'][] = $activityNetwork['result'][$i];
        }
        $result['total'] = count($result);
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
   
  /**
   * Método privado que obtiene la actividad de la Network de un usuario
   * @param int $id_customer Id de usuario
   * @return json Actividad de la network
   */
  private function getActivityNetworkProfile(){
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
      
    $id_customer = $this->_request['id_customer'];
    $id_customer_consult = $this->_request['id_customer_consult'];
  
    $sql = "SELECT
              o.id_order,
              o.date_add,
              o.id_customer,
              c.username name_customer,
              pl.id_product,
              i.id_image,
              m.name name_product,
              m.id_manufacturer,
              pl.link_rewrite,
              p.price,
              od.points as credits
            FROM "._DB_PREFIX_."orders o
            INNER JOIN "._DB_PREFIX_."rewards r ON ( o.id_order = r.id_order AND r.plugin = 'sponsorship' AND r.id_customer = ".$id_customer." )
            INNER JOIN "._DB_PREFIX_."customer c ON ( o.id_customer = c.id_customer )
            INNER JOIN "._DB_PREFIX_."order_detail od ON ( o.id_order = od.id_order )
            INNER JOIN "._DB_PREFIX_."product p ON ( od.product_id = p.id_product )
            INNER JOIN "._DB_PREFIX_."image i ON ( od.product_id = i.id_product AND i.cover = 1 )
            INNER JOIN "._DB_PREFIX_."product_lang pl ON ( od.product_id = pl.id_product AND pl.id_lang = 1 )
            INNER JOIN "._DB_PREFIX_."manufacturer m ON ( p.id_manufacturer = m.id_manufacturer )
            WHERE o.id_customer IN ( ".$id_customer_consult." ) AND o.current_state = 2
            ORDER BY o.date_add DESC  LIMIT 5";
      
    $activity = Db::getInstance()->executeS($sql);
    $link = new Link();
    foreach ($activity as &$activityN){
      $activityN['credits'] = round($activityN['credits']);
      $activityN['img'] = $link->getManufacturerImageLink($activityN['id_manufacturer']);
    }
    $result['result'] = $activity;
    $result['total'] = count($result['result']);
      
    return $this->response($this->json($result), 200);
  }
  
  /**
   * Método privado para buscar una invitacón en la red propia
   * @param int $id_customer Id usuario
   * @return json Invitaciones disponibles.
   */
  private function findInvitation() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
      
    $id_customer = $this->_request['id_customer'];
    $model = new Model();
    $results = $model->getMyNetworkInvitations( $this->id_lang_default, $id_customer );
    $max_limit = count($results['result']);
    $limit = $max_limit < 4 ? $max_limit : 4;

    if($max_limit > 0){
      for( $i = 0 ; $i <= $limit ; $i++ ){
        $result[$i] = $results['result'][$i];
      }        
    }

    return $this->response(json_encode(array('result' => $result)),200);
  }
  
  /**
   * Método privado para enviar la invitación
   * @param int $id_customer Id de usuario
   * @param string $email Correo del usurio 
   * @param string $firtsname Primer nombre
   * @param string $lastname Segundo nombre
   * @param int $phone Numero de teléfono
   * @param bool $whatsapp Bandera para enviar invitacion por whatsapp
   * @return json Resultado de la invitación
   */
  private function sendInvitation() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    $id_customer = $this->_request['id_customer'];
    $invitation_data['email'] = $this->_request['email'];
    $invitation_data['firtsname'] = $this->_request['firtsname'];
    $invitation_data['lastname'] = $this->_request['lastname'];
    $invitation_data['whatsapp'] = $this->_request['whatsapp'];
    $phone = $this->_request['phone'];

    $model = new Model();
    $invitation = $model->sendInvitation( $this->id_lang_default, $id_customer, $invitation_data, $phone );
    return $this->response(json_encode(array('result' => $invitation)),200);
  }
  
  /**
   * Método privado para la redención
   * @param int $id_customer Id del cliente
   * @param int $identification Número de identificación del usuario
   * @param string $firts_name Primer nombre del usuario
   * @param string $last_name apellidos del usuario
   * @param int $card Numero de tarjeta del usuario
   * @param int $account tipo de cuenta
   * @param string $bank banco
   * @param int $points puntos 
   * @param int $credits creditos
   * @param string $typeRedemption tipo de redencion
   * @param int $cardVirtual tarjeta virtual
   * @param int $type_vitual Tipo de pago virtual
   * @return json Resultado de la redención
   */
  public function redemption() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    $requestData = array(
      'id_customer' => '',
      'identification' => '',
      'firts_name' => '',
      'last_name' => '',
      'card' => '',
      'account' => '',
      'bank' => '',
      'points' => '',
      'credits' => '',
      'typeRedemption' => '',
      'cardVirtual' => '',
      'type_vitual' => ''
    );

    //llena las variables de busqueda.
    foreach ($requestData as $rqd => $value) {
      ${$rqd} = isset($this->_request[$rqd]) ? $this->_request[$rqd] : $value;
    }

    $MyAccountController = new MyAccountController();
    $userData = $MyAccountController->getUserDataAccountApp( $id_customer );
    if( $userData['fluzTotal'] < $points ){
      return $this->response(json_encode(array('result' => 'error')),206);
    }

    if( $typeRedemption > 0 && $typeRedemption < 3){
      $card_value = ($typeRedemption == 1) ? $card : $cardVirtual;
      $bank_value = ($typeRedemption == 1) ? $bank : ( ($type_vitual == 1) ? 'BITCOIN' : 'ETHEREUM' );
      $account_value = ($typeRedemption == 1) ? $account : ( ($type_vitual == 1) ? 'BITCOIN' : 'ETHEREUM' );
    }
    else{
      return $this->response(json_encode(array('result' => 'error')),206);
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
              ".$identification.", '".$firts_name."', '".$last_name."', ".$card_value.", '".$account_value."', '".$bank_value."', ".$points.", ".$credits.", 0, 0, '-".$credits."'
            )";

    $result = Db::getInstance()->ExecuteS($sql);

    return $this->response(json_encode(array('result' => $result)),200);
  }
  
  /**
   * Método privado para enviar un mensaje a un usuario de la red
   * @param int $id_customer_send Id del usuario emisor
   * @param int $id_customer_receive Id del usuario receptor
   * @param string $message Mensaje
   * @return json Resultado del envio del mensaje
   */
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
  
  /**
   * Método privado para obtener las conversaciones
   * @param int $id_customer Id del usuario
   * @return json Conversaciones
   */
  private function getConversations() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    $id_customer = $this->_request['id_customer'];
    $model = new Model();
    $conversations = $model->getConversations($id_customer);
    
    return $this->response(json_encode(array('result' => $conversations)),200);
  }
  
  /**
   * Método privado para obtener la informacion de los mensajes (Los no leidos)
   * @param int $id_customer Id del usuario
   * @return json Cantidad de mensajes no leidos
   */
  private function getMessagesData() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    $id_customer = $this->_request['id_customer'];
    $model = new Model();
    $conversations = $model->getConversations($id_customer);
    $count_unread_messages;
    foreach ($conversations as $conversation){
      $count_unread_messages += $conversation['unread_messages'];
    }
    return $this->response(json_encode(array('result' => $count_unread_messages)),200);
  }
  
  private function readConversation() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    $id_customer = $this->_request['id_customer'];
    $id_customer_conversation = $this->_request['id_customer_conversation'];
    
    $sql = 'UPDATE '._DB_PREFIX_.'message_sponsor
            SET '._DB_PREFIX_.'message_sponsor.read=1
            WHERE id_customer_send='.$id_customer_conversation.' and id_customer_receive = '.$id_customer.';';
    
    $result = Db::getInstance()->execute($sql);
    return $this->response(json_encode(array('result' => $result)),200);
  }
  
  /**
   * Método privado para obtener una conversación
   * @param int $id_customer Id del usuario
   * @param int $id_customer_conversation Id del usurio de la conversación
   * @return json Conversación
   */
  private function getConversation() {
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
    
    $conversation = Db::getInstance()->executeS($sql);
    return $this->response(json_encode(array('result' => $conversation)),200);
  }
  
  /**
   * Método privado para obtener el estado del passcode de la bóveda
   * @param int $id_customer Id del usuario
   * @return json Verdadero si la tiene / falso si no la tiene asignada
   */
  private function getPasscode() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    $id_customer = $this->_request['id_customer'];
    
    $sql = 'SELECT id_customer, vault_code 
            FROM '._DB_PREFIX_.'customer
            WHERE id_customer = '.$id_customer.';';
    $result = Db::getInstance()->executeS($sql);
    if ( isset($result['0']['vault_code']) && !empty($result['0']['vault_code']) && $result['0']['vault_code'] != 0 && $result['0']['vault_code'] >= 1000 ){
      return $this->response(json_encode(array('result' => true)),200);
    }
    else {
      return $this->response(json_encode(array('result' => false)),200);
    }
  }
  
  /**
   * Metodo privado para asignar la contraseña de la bóveda de bonos
   * @param int $id_customer Id del usuario
   * @param int $passcode Contraseña
   * @return json resultado de la asignacion de la contraseña
   */
  private function setPasscode() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    $id_customer = $this->_request['id_customer'];
    $passcode = $this->_request['passcode'];
    
    $sql = 'UPDATE '._DB_PREFIX_.'customer
            SET vault_code = '.$passcode.'
            WHERE id_customer = '.$id_customer.';';
    $result = Db::getInstance()->execute($sql);
    
    if ($result  == 1){
      return $this->response(json_encode(array('result' => true)),200);
    }
    else {  
      return $this->response(json_encode(array('result' => false)),200);
    }
  }
  
  /**
   * Método privado de validación del passcode de la bóveda de bonos
   * @param int $id_customer Id del usuario
   * @param int $passcode Contraseña para validar
   * @return json estado de la validación
   */
  private function validatePasscode() {
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
        "message" => "La contraseña no coincide.",
        "result"  => $result
      )), 200);
    }
  }
  
  /**
   * Metrodo porivado para actualizar el passcode de la bóveda de bonos
   * @param int $id_customer Id del usuario
   * @param int $passcode passcode a asignar
   * @return json Resultado de la actualización
   */
  private function updatePasscode(){
    if($this->get_request_method() != "POST") {
      $this->response('',406);
    }
    
    $id_customer = $this->_request['id_customer'];
    $passcode = $this->_request['passcode'];
    
    $sql = 'SELECT vault_code
            FROM '._DB_PREFIX_.'customer
            WHERE id_customer = '.$id_customer.';';
    $passcode_db = Db::getInstance()->getValue($sql);
    $result = ( $passcode_db == $passcode ) ? false : true;
    
    if ( $result ){
      $sql = 'UPDATE '._DB_PREFIX_.'customer
              SET vault_code = '.$passcode.'
              WHERE id_customer = '.$id_customer.';';
      $result = Db::getInstance()->execute($sql);
      if($result == 1){
        $this->response($this->json(array(
          "success" => true, 
          "message" => "Todo ok.",
          "result"  => $result
        )), 200);
      }
      else{
        $this->response($this->json(array(
          "success" => false, 
          "message" => "No se ha actualizado la contraseña.",
          "result"  => $result
        )), 206);
      }
    }
    else{
      $this->response($this->json(array(
        "success" => false, 
        "message" => "La contraseña no puede ser igual a la anterior.",
        "result"  => $result
      )), 204);
    }
  }
  
  /**
   * Método privado para actualizar el estado de los bonos
   * @param int $card Id de la tarjeta de regalo
   * @param string $used Estado de uso
   * @param int $price_card_used Valor usado de la tarjeta
   * @return json Resultado de la actualización del bono
   */
  private function updateBonus() {
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
  
  /**
   * Método privado que obtiene el teléfono por id de usuario
   * @param int $id_customer Id del usuario
   * @return json Número de teléfono
   */
  private function getPhoneByIdCustomer() {
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
  
  /**
   * Método privado que Asigna un teléfono a un usuario por id de usuario
   * @param int $id_customer Id de usuario
   * @return json Resultado de la asignación
   */
  private function setPhoneByIdCustomer() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    $id_customer = $this->_request['id_customer'];
    $phone = $this->_request['phone'];
    $sql = 'UPDATE '._DB_PREFIX_.'customer
            SET phone = '.$phone.'
            WHERE id_customer = '.$id_customer.';';
    $result = Db::getInstance()->execute($sql);
    return $this->response(json_encode(array('result' => $result)),200);
  }
  
  /**
   * Método privado que envia un SMS de confirmación al acceder a la cuenta.
   * @param int $id_customer Id usuario
   * @return json Resultado del envio del SMS de confirmación
   */
  private function sendSMSConfirm() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    $id_customer = (int)$this->_request['id_customer'];
    
    $sql = "SELECT phone
            FROM "._DB_PREFIX_."customer
            WHERE id_customer = ".$id_customer.";";
    error_log("\n\n\n sql: ".print_r($sql, true),3,"/tmp/error.log");
    $phone = Db::getInstance()->getValue($sql);
    error_log("\n\n\n phone: ".print_r($phone, true),3,"/tmp/error.log");
    $numberConfirm = rand(100000, 999999);
    error_log("\n\n\n numberConfirm: ".print_r($numberConfirm, true),3,"/tmp/error.log");
    $updateNumberConfirm = 'UPDATE '._DB_PREFIX_.'customer
                            SET sms_confirm = '.$numberConfirm.'
                            WHERE id_customer = '.$id_customer.';';
    error_log("\n\n\n updateNumberConfirm: ".print_r($updateNumberConfirm, true),3,"/tmp/error.log");
    $result = Db::getInstance()->execute($updateNumberConfirm);
    $curl = curl_init();
    $message_text= "Fluz Fluz te da la bienvenida!!! Tu codigo de verificacion es: ";
    $url = Configuration::get('APP_SMS_URL').$phone."&messagedata=".urlencode($message_text.$numberConfirm)."&longMessage=true";
    $send_sms = null;
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
    $send_sms = curl_exec($curl);
    curl_close($curl);
    
    $result = ( !empty($send_sms) ) ? "Se ha enviado el sms." : "No se pudo enviar el sms";
    return $this->response(json_encode(array('result' => $result)),200);
  }
  
  private function sendSMSConfirmRandom() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    $phone = $this->_request['phone'];
    $numberConfirm = rand(100000, 999999);
    $curl = curl_init();
    
    $url = Configuration::get('APP_SMS_URL').$phone."&messagedata=".$numberConfirm;
    $send_sms = null;
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
    $send_sms = curl_exec($curl);
    curl_close($curl);
    
    $result['error'] = ( !empty($send_sms) ) ? 0 : 1;
    $result['success'] = true;
    $result['numberConfirm'] = $numberConfirm;
    return $this->response(json_encode(array('result' => $result)),200);
  }
  
  /**
   * Método privado de confirmación de usuario con el número enviado por SMS
   * @param int $id_customer Id usuario
   * @param int $confirmNumber Número de confirmacion enviado por SMS
   * @return json Resultado de la confirmación de identidad.
   */
  private function confirm() {
    if($this->get_request_method() != "POST") {
      $this->response('',406);
    }
    
    $code =  trim( $code != NULL ? $code : $this->_request['confirmNumber']);
    $id_customer = $this->_request['id_customer'];

    $sql = "SELECT sms_confirm
          FROM "._DB_PREFIX_."customer
          WHERE id_customer = ".$id_customer.";";

    $app_confirm = Db::getInstance()->getValue($sql);
    if( $code == $app_confirm ){
      
      $sql = 'UPDATE '._DB_PREFIX_.'customer
            SET active = 1
            WHERE id_customer = '.$id_customer.';';
      
      $result = Db::getInstance()->execute($sql);
      
      $this->response($this->json(array(
                "success" => true,
                "error" => 0,
                "message" => "Usuario confirmado"
                )), 200);
    }
    else {
      $this->response($this->json(array(
          "success" => true, 
          "error" => 1,
          "message" => utf8_encode("¡El número de confirmación ingresado es incorrecto!")
          )), 200);
    }
  }
  
  /**
   * Método privado para obtener las direcciones de los comercios.
   * @param int $id_manufacturer Id del comercio
   * @return json Direcciones del comercio solicitado
   */
  private function getAddressManufacturer() {
    if($this->get_request_method() != "GET") {
      $this->response('',406);
    }
    
    $id_manufacturer = $this->_request['id_manufacturer'];
    
    $sql = "SELECT a.firstname, a.address1, a.city
            FROM "._DB_PREFIX_."address as a
            INNER JOIN "._DB_PREFIX_."manufacturer as m on (m.id_manufacturer = a.id_manufacturer)
            WHERE m.active = 1
              and a.active = 1
              and a.id_manufacturer = ".$id_manufacturer;
    
    $result = Db::getInstance()->executeS($sql);
    $total = count($result);
    return $this->response(json_encode(array('result' => $result, 'total' => $total)),200);
  }
  
  /**
   * Método privado para obtener las longitudes y latitudes de las direcciones de los comercios para ubicar en el mapa.
   * @param string $latitude Latitud de la ubicación del usuario
   * @param string $longitude Longitud de la ubicación del usuario
   * @param int $option Opción a consultar
   * @param int $id_manufacturer Id del comercio a consultar
   * @return json Resultado de la consulta. 
   */
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
    
    $positions = Db::getInstance()->executeS($sql);
    
    // Destinos
    foreach($positions as &$pos) {
      $pos['distance'] = $this->getDistanceToCoords($latitude,$longitude,$pos['latitude'],$pos['longitude']);
    }
    usort($positions, function($a, $b) {
      return str_replace('.', ',', $a['distance']) > str_replace('.', ',', $b['distance']) ? 1:-1 ;
    });
    
    foreach ($positions as $pos){
      if(floatval($pos['distance']) <= 10){
        $result[] = $pos;
      }
    }
    array_unique($result);
    
    return $this->response(json_encode(array('result' => $result)),200);
  }
  
  /**
   * Método privado que obtiene la informacion de las notificaciones iniciales.
   * @param int $id_customer Id del cliente
   * @return json Informacion de las notificaciones iniciales.
   */
  private function getNotificationBarOrders(){
    if($this->get_request_method() != "GET") {
      $this->response('',406);
    }
    $id_customer = $this->_request['id_customer'];
    
    $model = new Model();
    $result = $model->getNotificationOrder($id_customer);
    $this->response($this->json($result), 200);
  }
  
  /**
   * Método público que obtiene la distancia ente la posicion actual del usuario y un comercio
   * @param float $lat1 Latitud del usuario
   * @param float $lon1 Longitud del usuario
   * @param float $lat2 Latitud del comercio
   * @param float $lon2 Longitud del comercio
   * @return float Distancia entre el comercio y el usuario.
   */
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
  
  /**
   * Método publico para convertir de Degrees a Radians
   * @param float $deg valor en deg
   * @return float Valor en radians 
   */
  public function deg2rad($deg) {
    return $deg * (M_PI/180);
  }
  
  /**
   * Método publico para subir la imágen de perfil del usuario.
   */
  public function profileImage() {
    // subir imagen al servidor
    $target_path = _PS_IMG_DIR_ . "profile-images/";
    $target_path = $target_path . basename( $_FILES['file']['name'] );
 
    if ( !move_uploaded_file($_FILES['file']['tmp_name'], $target_path) ) {
        $this->errors[] = Tools::displayError('No fue posible cargar la imagen de perfil.');
    }

    // convertir imagen a PNG
    $patch_grabar = _PS_IMG_DIR_ . "profile-images/" . basename( $_FILES['file']['name'].".png" );
    $imagen = imagecreatefromjpeg($target_path);
    $pngquality = floor(($quality - 10) / 10);
    imagepng($imagen, $patch_grabar, $pngquality);

    // borrar imagen original
    unlink( $target_path );

    // cambiar tamaño imagen y recortarla en circulo
    include_once(_PS_ROOT_DIR_.'/classes/Thumb.php');
    $mythumb = new thumb();
    $mythumb->loadImage($patch_grabar);
    $mythumb->crop(400, 400, 'center');
    $mythumb->save($patch_grabar);
  }
  
  /**
   * Método privado para obsequiar un código a un usuario en la Red propia del usuario
   * @param int $id_customer Id usuario emisor
   * @param int $id_customer_receive Id usuario receptor
   * @param int $code El código a obsequiar
   * @param int $id_product_code Id del producto a obsequiar
   * @param int $message Mensaje añadido al regalo
   * @param int $customer_send Nombre del usuario receptor
   * @return json Resultado del obsequio del código
   */
  private function sendGiftCard(){
    if($this->get_request_method() != "GET") {
      $this->response('',406);
    }
    $id_customer = $this->_request['id_customer'];
    $id_customer_receive = $this->_request['id_customer_receive'];
    $code = $this->_request['code'];
    $id_product_code = $this->_request['id_product_code'];
    $message = $this->_request['message'];
    $customer_send = $this->_request['customer_send'];
    $customer_receive = new Customer($id_customer_receive);
    
    $error = 0;
    
    $id_info_gift = Db::getInstance()->getRow('SELECT pc.id_order as id_order, pc.id_product as id_product, pc.last_digits as last_digits, pc.pin_code as pin_code
                           FROM '._DB_PREFIX_.'product_code pc WHERE pc.id_product_code = '.$id_product_code);
    if(!empty($id_info_gift) && isset($id_info_gift) && $id_info_gift['id_order'] != '' && $id_info_gift['id_product'] != ''){
      $secure_key_sponsor = Db::getInstance()->getValue('SELECT c.secure_key 
                           FROM '._DB_PREFIX_.'customer c WHERE c.id_customer = '.$id_customer_receive);
      
      $code_encrypt_customer = Encrypt::encrypt($secure_key_sponsor, $code);
      
      if(!empty($secure_key_sponsor) && isset($secure_key_sponsor) && $secure_key_sponsor != '' &&
      !empty($code_encrypt_customer) && isset($code_encrypt_customer) && $code_encrypt_customer != ''){
        $sql_update = 'UPDATE '._DB_PREFIX_.'product_code 
                      SET send_gift = 1
                      WHERE id_product_code='.$id_product_code.'
                        AND id_order = '.$id_info_gift['id_order'];
        if ( Db::getInstance()->execute($sql_update) == 1 ){
          $sql_insert = 'INSERT INTO '. _DB_PREFIX_ .'transfer_gift (id_product, id_customer_send, id_customer_receive, message_motive)
                         VALUES ('.(int)$id_info_gift['id_product'].','.$id_customer.','.$id_customer_receive.',"'.$message.'")';
          $insert = Db::getInstance()->execute($sql_insert);
          if ($insert == 1){
            $id_transfer_gift = Db::getInstance()->getRow('SELECT id_transfer_gift FROM '._DB_PREFIX_.'transfer_gift WHERE id_customer_send='.(int)$id_customer. ' ORDER BY id_transfer_gift DESC');
            if(!empty($id_transfer_gift) && isset($id_transfer_gift) && $id_transfer_gift['id_transfer_gift'] != ''){
              $insert2 = Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "product_code (id_product, code, last_digits, pin_code, id_order, used, date_add, state, encry, send_gift, id_transfer_gift)
              VALUES (" . (int)$id_info_gift['id_product']. ",'".$code_encrypt_customer."','".$id_info_gift['last_digits']."'," . (int)$id_info_gift['pin_code']. ", 0, 0,'" . date("Y-m-d H:i:s") . "','Disponible', 1, 2," .$id_transfer_gift['id_transfer_gift']. ")");
              if($insert2 == 1){
                //Preparacion de correo
                $list_product = Db::getInstance()->getRow('SELECT od.product_name, od.product_quantity, o.total_paid, pl.description_short
                  FROM '. _DB_PREFIX_ .'order_detail od
                  LEFT JOIN '. _DB_PREFIX_ .'product_code pc ON (pc.id_order = od.id_order)
                  LEFT JOIN '. _DB_PREFIX_ .'orders o ON (od.id_order = o.id_order)
                  LEFT JOIN '. _DB_PREFIX_ .'product_lang pl ON (pl.id_product = od.product_id)    
                  WHERE od.id_order ='.(int)$id_info_gift['id_order']);
                if(isset($list_product) && !empty($list_product) && $list_product['product_name'] != ''){
                  $vars = array(
                    '{username}' => $customer_receive->username,
                    '{sender_username}' => $customer_send->username,
                    '{sender_message}' => $message,
                    '{name_product}'=> $list_product['product_name'],
                    '{code_product}'=> $code,
                    '{quantity}'=> $list_product['product_quantity'],
                    '{total_products}'=> round($list_product['total_paid']),
                    '{description}' => $list_product['description_short'],
                    '{date}' => Tools::displayDate(date('Y-m-d H:i:s'), null, 1),
                    '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                    '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                    '{shop_url_personal}' => Context::getContext()->link->getPageLink('identity', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
                    '{learn_more_url}' => "http://reglas.fluzfluz.co",
                  );
                  $template = 'send_gift';
                  $prefix_template = '16-send_gift';

                  $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
                  $row_subject = Db::getInstance()->getRow($query_subject);
                  
                  $message_subject = $row_subject['subject_mail'];

                  $allinone_rewards = new allinone_rewards();
                  $result = $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject),$vars, $customer_receive->email, $customer_receive->firstname.' '.$customer_receive->lastname);
                  
                  $error = 0;
                  $msgError = "Se ha transferido con exito.";
                }
                else {
                  $error = 6;
                  $msgError = "Error al traer los productos para el correo.";
                }
              }
              else{
                $error = 6;
                $msgError = "Error al asociar el producto.";
              }
            }
            else{
              $error = 5;
              $msgError = "Error al traer informacion de transferencia.";
            }
          }
          else{
            $error = 4;
            $msgError = "Error al hacer la transferencia.";
          }
          
        }
        else{
          $error = 3;
          $msgError = "Error al actualizar la información del producto.";
        }
      }
      else {
        $error = 2;
        $msgError = "Error al obtener la información del usuario.";        
      }
    }
    else {
      $error = 1;
      $msgError = "Error al obtener la información del producto regalo.";
    }
    return $this->response(json_encode(array('error' => $error, 'msg' => $msgError)),200);
  }
  
  /**
   * Método privado para obtener el historial de ordenes de un usuario.
   * @param int $id_customer Id del usuario
   * @return json Historial de ordenes.
   */
  private function getOrderHistory(){
    if($this->get_request_method() != "GET") {
      $this->response('',406);
    }
    $id_customer = $this->_request['id_customer'];
    $id_lang = 1 ;
    $sql = "SELECT o.id_order, os.name, o.payment, o.total_discounts, o.total_paid, DATE_FORMAT(o.date_add , '%Y-%m-%d') as date, osc.color
            FROM "._DB_PREFIX_."orders o
            INNER JOIN "._DB_PREFIX_."order_state_lang os ON (os.id_order_state = o.current_state)
            INNER JOIN "._DB_PREFIX_."order_state osc ON (osc.id_order_state = o.current_state) 
            WHERE id_customer = ".$id_customer." and os.id_lang = ".$id_lang."
            ORDER BY date_add DESC
            LIMIT 10;";
    $orders = Db::getInstance()->executeS($sql);
    usort($orders, 'ordenar');
    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
    $d = array();

    foreach ($orders as &$order){
      if ( !in_array(date('m-Y', strtotime($order['date'])), $d) ){
        $d[] = date('m-Y', strtotime($order['date']));
      }
    }

    foreach ($d as $date) {
      $dates[]['date'] = $date;
    }
    
    foreach ($dates as $key => &$date){
      $date['date_to_display'] = $meses[ ((int)substr($date['date'], 0, 2))-1 ]." de ".substr($date['date'], 3, 7);
      foreach ($orders as &$order){
        if ( $date['date'] == date('m-Y', strtotime($order['date'])) ) {
          $order['total_order'] = $this->formatPrice($order['total_paid'] + $order['total_discounts']);
          $order['total_discounts'] = $this->formatPrice($order['total_discounts']);
          $order['total_paid'] =  $this->formatPrice($order['total_paid']);
          $dates[$key]['orders'][] = $order;
        }
      }
    }

    $orders['result']= $dates;
    return $this->response(json_encode($orders),200);
  }
  
  /**
   * Método público para ordenar un arreglo.
   * @param array $a  
   * @param array $b
   * @return array arreglo ordenado
   */
  public function ordenar( $a, $b ) {
    return strtotime($a['date']) - strtotime($b['date']);
  }
  
  /**
   * Método privado para obtener el detalle de una orden.
   * @param int $id_order Id de la orden a consultar
   * @return json Detalle de la orden
   */
  private function getOrderDetail() {
    if($this->get_request_method() != "GET") {
      $this->response('',406);
    }
    
    $id_order = $this->_request['id_order'];
    
    $sql = "SELECT od.product_id, od.product_name, od.product_quantity, od.product_price, od.total_price_tax_incl as product_total, p.id_manufacturer as m_id, m.name as manufacturer
            FROM ps_order_detail od
            INNER JOIN ps_product p ON (p.id_product = od.product_id)
            INNER JOIN ps_manufacturer m ON (p.id_manufacturer = m.id_manufacturer)
            WHERE od.id_order = ".$id_order;
    
    $products = Db::getInstance()->executeS($sql);
    
    $link = new Link();
    foreach($products as &$product){
      $product['image'] = $link->getManufacturerImageLink($product['m_id']);
      $product['product_price'] = $this->formatPrice($product['product_price']);
      $product['product_total'] = $this->formatPrice($product['product_total']);
    }
    return $this->response(json_encode(array('result'=>$products)),200);
  }
  
  /**
   * Método privado para obtener el estado de un Comercio
   * @param int $id_manufacturer Id del comercio
   * @return json Estado ( Activo/Inactivo )
   */
  private function getStateManufacturer(){
    if($this->get_request_method() != "GET") {
      $this->response('',406);
    }
    
    $id_manufacturer = $this->_request['id_manufacturer'];
    
    $sql = "SELECT COUNT(DISTINCT id_product)
            FROM ps_product
            WHERE id_manufacturer = ".$id_manufacturer." and active = 1 and product_parent = 0";
    
    $result = Db::getInstance()->getValue($sql);
    
    return $this->response(json_encode(array('result'=> $result > 0 ? true : false)),200);
  }
  
  /**
   * Método privado para obtener la información de instagram de un comercio.
   * @param int $id_manufacturer Id del comercio
   * @param int $count Número que especifica la cantiodad de información que solicita.
   * @return json Instagram de un comercio.
   */
  private function getMediaInstagram() {
    if($this->get_request_method() != "GET") {
      $this->response('',406);
    }
    
    $id_manufacturer = $this->_request['id_manufacturer'];
    $count = $this->_request['count'];
    
    $sql = "SELECT instagram
            FROM ps_manufacturer
            WHERE active = 1 and id_manufacturer = ".$id_manufacturer;
    $instagram = DB::getInstance()->getValue($sql);

    $url = 'https://www.instagram.com/'.$instagram.'/?__a=1';
    $json = $this->fetchData($url);
    $data = json_decode($json, true);
    
    $return = array();
    $i = 0;

    foreach( $data['user']['media']['nodes'] as $post ) {
        $return[] = array(
            'link' => 'https://www.instagram.com/'.$instagram,
            'imgsmall' => $post['thumbnail_resources']['0'],
            'imgmedium' => $post['thumbnail_resources']['1'],
            'imglarge' => $post['thumbnail_resources']['4'],
        );
        $i++;
        if( $i >= $count ) {
            break;
        }
    }
    $result['imageData'] = $return;
    $result['instagram_profile'] = $instagram;
    $result['total'] = count($return) ;
    
    return $this->response(json_encode(array('result'=> $result)),200);
  }
  
  /**
   * Método privado que captura el resultado de consultar una url.
   * @param string $url Url a consultar
   * @return json Resultado
   */
  private function fetchData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }
  
  /**
   * Método privado para la consulta de la Red del usuario, usada para pintal el gráfico
   * @param int $id_customer Id de usuario
   * @return json Red
   */
  private function getNetworkGUser(){
    if($this->get_request_method() != "GET") {
      $this->response('',406);
    }
    $id_customer = $this->_request['id_customer'];
    $model = new Model();
    $my_network = $model->getMyNetwork( $this->id_lang_default, $id_customer );
    return $this->response(json_encode($my_network),200);
  }
  
  /**
   * Método privado para obtener la url de pago de BitPay
   * @param int $id_cart Id del carrito
   * @return json Url del pago con BitPay.
   */
  private function getBitPay(){
    if($this->get_request_method() != "POST") {
      $this->response('',406);
    }
    
    $id_cart = $this->_request['id_cart'];
    $cart = new Cart($id_cart);
    $reference = Order::generateReference();
    $products = $cart->getProducts();
    $total_products = 0;
    $total_paid_real = 0;
    foreach ($products as $p){
        $total_paid_real += $p['total_wt'];
        $total_products += $p['total'];
    }
    
    $order = new Order();
    $order->id_address_delivery = $cart->id_address_delivery;
    $order->id_address_invoice = $cart->id_address_invoice;
    $order->id_shop_group = $cart->id_shop_group;
    $order->id_shop = $cart->id_shop;
    $order->id_cart = $cart->id;
    $order->id_currency = $cart->id_currency;
    $order->id_lang = $cart->id_lang;
    $order->id_customer = $cart->id_customer;
    $order->id_carrier = $cart->id_carrier;
    $order->secure_key = $cart->secure_key;
    $order->payment = 'bitpay';
    $order->date_add = $cart->date_add;
    $order->date_upd = $cart->date_upd;
    $order->module = 'bitpay';
    $order->total_paid = $total_products;
    $order->total_paid_real = $total_paid_real;
    $order->total_products = $total_products;
    $order->total_products_wt = $total_paid_real;
    $order->total_paid_tax_incl = $total_products;
    $order->total_paid_tax_excl = $total_paid_real;
    $order->current_state = 15;
    $order->conversion_rate = 1;
    $order->reference = $reference;
    $order->method_add = 'Movil App';
    $order->save();
    
    $customer = new Customer($order->id_customer);
    $currency = new Currency($order->id_currency);
    $order_detail = new OrderDetail();
    $order_detail->createList($order, $cart, $order->getCurrentOrderState(), $cart->getProducts(), 0, true, 0);
    $order_status = new OrderState((int)$order->current_state, (int)$order->id_lang);
    
    Hook::exec('actionValidateOrder', array(
                'cart' => $cart,
                'order' => $order,
                'customer' => $customer,
                'currency' => $currency,
                'orderStatus' => $order_status
            ));
    
    bitpay::writeDetails($order->id, $cart->id, $order->id, $order_status->name);
    $model = new Model();
    $return = $model->getObjectBitPay($cart, $order);
    $return['success'] = true;
    $this->response(json_encode($return),200);
  }
  
  /**
   * Método privado para loguearse con el correo obtenido por alguna red Social
   * @param string $email Correo
   * @return json Resultado del login.
   */
  private function getEmailSocialMedia() {
    if($this->get_request_method() != "GET") {
      $this->response('',406);
    }
    
    $email = $this->_request['email'];
    
    $sql = "SELECT email, passwd, active
            FROM ps_customer
            WHERE email = '".$email."'";
    
    $result = Db::getInstance()->getRow($sql);
    
    $error = 0;
    $msg = '';
    if($result != '' || $result != NULL){
      if($result['active'] == 1){
        $id_customer = Customer::getCustomersByEmail($email);
        $customer = new Customer($id_customer['0']['id_customer']);
        $sql = "SELECT code
                FROM ps_rewards_sponsorship_code
                WHERE id_sponsor = ".$customer->id;
        
        $refer_code = DB::getInstance()->getValue($sql);
        
        $gender = $customer->id_gender  == 1 ? 'M' : ($customer->id_gender  == 2 ? 'F' : "");
        $result = array(
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
          'phone' => $customer->phone,
          'refer_code' => $refer_code,
          'success' => TRUE);
      }
      else {
        $error = 2;
        $msg = 'El ususario no esta activo.';
      }
    }
    else {
      $error = 1;
      $msg = 'No hay ningun usuario registrado con este correo.';
    }
    
    $return['error'] = $error;
    $return['msg'] = $msg;
    $return['result'] = ( $error != 0 ) ? '' : $result;
    
    return $this->response(json_encode($return),200);
  }
  
  /**
   * Método privado para solicitar soporte
   * @param int $id_customer Id de usuario
   * @param string $email correo
   * @param string $issue asunto del Problema
   * @param string $problem descripcion del problema
   */
  private function sendSupport(){
    if($this->get_request_method() != "POST") {
      $this->response('',406);
    }
    
    $data['id_customer'] = $this->_request['id_customer'];
    $data['name'] = $this->_request['name'];
    $data['email'] = $this->_request['email'];
    $data['issue'] = $this->_request['issue'];
    $data['problem'] = $this->_request['problem'];
    
    $vars = array(
      '{username}' => $data['name'],
      '{id_customer}' => $data['id_customer'],
      '{email}' => $data['email'],
      '{issue}'=> $data['issue'],
      '{problem}'=> $data['problem']
    );
    $template = 'support-email';
    $prefix_template = '16-support-email';

    $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
    $row_subject = Db::getInstance()->getRow($query_subject);

    $message_subject = $row_subject['subject_mail'];

    $allinone_rewards = new allinone_rewards();
    $result = $allinone_rewards->sendMail(Context::getContext()->language->id, $template, $allinone_rewards->getL($message_subject),$vars, 'info@fluzfluz.com', '');
    
    if($result == 1){
      $this->response($this->json(array("success" => true )), 200);
    }
    else {
      $this->response($this->json(array(
        "success" => false, 
        "message" => "Ha ocurrido un error."
      )), 400);
    }
  }
  
  /**
   * Método privado que actualiza el Token de FCM
   * @param int $id_customer Id de usuario
   * @param string $token Token de fcm
   * @return Json Resultado de la actualización
   */
  private function setTokenFCM() {
    if($this->get_request_method() != "GET") {
      $this->response('',406);
    }
    
    $id_customer = $this->_request['id_customer'];
    $token = $this->_request['token'];
    
    $sql = 'UPDATE '._DB_PREFIX_.'customer
            SET token_fcm = "'.$token.'"
            WHERE id_customer = '.$id_customer.';';
    
    $result = Db::getInstance()->execute($sql);
    
    if ($result  == 1){
      return $this->response(json_encode(array('result' => true)),200);
    }
    else {  
      return $this->response(json_encode(array('result' => false)),200);
    }
    
  }
  
  private function reactiveAccount(){
    if($this->get_request_method() != "GET") {
      $this->response('',406);
    }
    
    $id_customer = $this->_request['id_customer'];
    
    $model = new Model();
    $result = $model->reactiveAccount($id_customer);
    $this->response(json_encode(array('result' => $result)),200);
  }
  
  private function getRequestSMS(){
    if($this->get_request_method() != "GET") {
      $this->response('',406);
    }
    $result['requestSMS'] = (bool)Configuration::get('APP_REQUEST_SMS');
    $result['success']=true;
    
    $this->response(json_encode(array('result' => $result)),200);
  }
  
}

class errorLog{
  public $_error_log = array();
  public $show_error_log;
  
  public function __construct(){
    $this->show_error_log = true;
  }
  
  public function show(){
    foreach ($this->_error_log as $name => $param){
      if($this->show_error_log)error_log("\n\n Nombre: ".$name."\n Valor: ".print_r($param,true),3,"/tmp/error.log" );
    }
  }
  
  public function addText($string){
    if($this->show_error_log)error_log("\n\n Alerta: ".$string,3,"/tmp/error.log" );
  }
  
  public function addParam($name,$param){
    $this->_error_log[$name] = $param;
  }
  
  public function clear($nameFunction) {
    $this->_error_log = [];
    if($this->show_error_log)error_log("\n\n ******************************** \n ** Se borro el log de errores ** \n ******************************** \n\n Funcion: ".$nameFunction,3,"/tmp/error.log" );
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
