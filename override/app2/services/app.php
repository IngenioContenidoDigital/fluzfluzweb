<?php

require_once('classes/Rest.inc.php');
require_once('classes/Model.php');

/**

 * Núcleo monitor que expone las rutas de los recursos. 
 * Procesa todas las urls para mantener el control de forma compacta.
 */
class API extends REST {

  public $id_lang_default = 0;
  public $protocol = '';
  public $priceDisplay = 0;

  public function __construct() {
    parent::__construct(); // Init parent contructor
    $this->id_lang_default = (int) Configuration::get('PS_LANG_DEFAULT');
    $this->protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
  }

  /**
   * Método público para el acceso a la API.
   * Este método llama dinámicamente el método basado en la cadena de consulta
   *
   */
  public function processApi() {
    $func = strtolower(trim(str_replace("/", "", $_REQUEST['rquest'])));
    if ((int) method_exists($this, $func) > 0)
      $this->$func();
    else
      $this->response('', 404); // If the method not exist with in this class, response would be "Page not found".
  }

  /**
   * Codifica el array en un JSON
   */
  private function json($data) {
    if (is_array($data) || is_object($data)) {
      return json_encode($data);
    }
  }

  /**
   * Productos API
   * GET /services/search?expr=Advil&page_number=2 - Busca de los productos
   * expr : <Nombre del producto o referencia>
   * page_number : <Número de página>
   * page_size : <Filas por página>
   * order_by : <Ordenar por ascendente ó descendente>
   * order_way : <Ordenar por campo>
   */
  private function search() {
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
    
    error_log("n\n\nEsto es lo que retorna el socio\n\n".print_r($search['result']),3,"/tmp/error.log");

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
   * GET /user → Recuperara la inforamación del usuario
   * POST /user → Crea un nuevo cliente
   * PUT /user/1 → Actualiza el cliente con el ID 1
   */
  private function user() {
    $context = Context::getContext();
    if (!$context->customer->isLogged() && $this->get_request_method() != "POST") {
      $this->response($this->json(array(
      'success' => false,
      'message' => 'El cliente debe estar autenticado para realizar operaciones con los recursos'
      )), 401);
    }

    $model = new Model();
    switch ($this->get_request_method()) {
      case 'GET'://consulta
        $customer = $context->customer;
        $this->response($this->json(array(
        'id' => (int) $customer->id,
        'id_default_group' => (int) $customer->id_default_group,
        'firstname' => $customer->firstname,
        'lastname' => $customer->lastname,
        'email' => $customer->email,
        'newsletter' => (bool) $customer->newsletter,
        'id_din' => (int) $customer->id_din,
        'din' => $customer->din,
        'id_gender' => (int) $customer->id_gender,
        'gender' => $customer->id_gender == 1 ? 'M' : ($customer->id_gender == 2 ? 'F' : ''),
        'birthday' => $customer->birthday,
        'active' => (bool) $customer->active,
        'group' => Group::getCurrent()
        )), 200);
        break;
      case 'POST'://inserta
        if (array_key_exists('id', $this->_request)) {

          $arguments = array(
          'id' => (int) $this->_request['id']
          );
          $requestData = array(
          'firstname' => '',
          'lastname' => '',
          'email' => '',
          'newsletter' => false,
          'id_gender' => 0,
          'id_din' => 0,
          'din' => '',
          'birthday' => ''
          );
          foreach ($requestData as $rqd => $value) {
            if (!empty($this->_request[$rqd])) {
              $arguments[$rqd] = isset($this->_request[$rqd]) ? $this->_request[$rqd] : $value;
            }
          }

          if ($customer = $model->setAccount($arguments)) {
            $this->response($this->json($customer), 200);
          }

          $this->response($this->json(array(
          "success" => false,
          "message" => "Error actualizando la cuenta."
          )), 400);
        } else {
          $arguments = array();
          $requestData = array(
          'firstname' => '',
          'lastname' => '',
          'email' => '',
          'newsletter' => false,
          'id_gender' => 0,
          'id_din' => 0,
          'passwd' => '',
          'din' => '',
          'birthday' => ''
          );
          foreach ($requestData as $rqd => $value) {
            $arguments[$rqd] = isset($this->_request[$rqd]) ? $this->_request[$rqd] : $value;
          }

          if (Validate::isEmail($arguments['email']) && !empty($arguments['email'])) {
            if (Customer::customerExists($arguments['email'])) {
              // Si las entradas son inválidas, mensaje de estado "Bad Request" y la razón
              $this->response($this->json(array(
              "success" => false,
              "message" => "No se pudo crear la cuenta, el " . $arguments['email'] . " email ya esta registrado"
              )), 400);
            }
          } else {
            $this->response($this->json(array(
            "success" => false,
            "message" => "Se requiere un correo valido " . $arguments['email']
            )), 400);
          }

          if (!Validate::isPasswd($arguments['passwd'])) {
            $this->response($this->json(array(
            "success" => false,
            "message" => "La contraseña no es valida, utiliza una contraseña con una longitud mínima de 5 caracteres."
            )), 400);
          }

          if ($customer = $model->setAccount($arguments)) {
            $this->response($this->json($customer), 200);
          }

          $this->response($this->json(array(
          "success" => false,
          "message" => "Error creando la cuenta."
          )), 400);
        }
        break;
      case 'PUT'://actualiza
        // Validación del identificador
        if (!isset($_REQUEST['id']) || empty($_REQUEST['id'])) {
          echo "aaaa";
          print_r($_REQUEST);
          exit;
          $this->response('', 406);
        }

        $arguments = array(
        'id' => (int) $_REQUEST['id']
        );
        $requestData = array(
        'firstname' => '',
        'lastname' => '',
        'email' => '',
        'newsletter' => false,
        'id_gender' => 0,
        'id_din' => 0,
        'din' => '',
        'birthday' => ''
        );
        foreach ($requestData as $rqd => $value) {
          $arguments[$rqd] = isset($this->_request[$rqd]) ? $this->_request[$rqd] : $value;
        }

        if (empty($arguments['email'])) {
          $this->response('', 406);
        }

        if ($customer = $model->setAccount($arguments)) {
          $this->response($this->json($customer), 200);
        }

        $this->response($this->json(array(
        "success" => false,
        "message" => "Error actualizando la cuenta."
        )), 400);

        break;
      default://metodo NO soportado
        // Validación Cross si el método de la petición esta en los casos de lo contrario volverá estado de "no aceptable"
        $this->response('No method', 406);
        break;
    }
  }

  /**
   * Inicio de sesión fb
   * POST /login - valida que el usuario se encuentré registrado, de lo contrario lo crea y realiza el
   * inicio de sesión.
   * email : <Correo eléctronico>
   * idFb : <Contraseña>
   */
  private function loginFb() {

    $arguments['firstname'] = $this->_request['first_name'];
    $arguments['lastname'] = $this->_request['last_name'];
    $arguments['email'] = $this->_request['email'];
    $arguments['idFb'] = $this->_request['idFb'];
    $arguments['passwd'] = $this->_request['idFb'];
    $arguments['gender'] = substr($this->_request['gender'], 0, 1);

    if (Validate::isEmail($arguments['email']) && !empty($arguments['idFb']) && !empty($arguments['firstname'])) {

      $result = Customer::customerExists($arguments['email']);
      if (empty($result[0]['id_customer'])) {
        $model = new Model();
        if ($customer = $model->setAccount($arguments)) {
          return $customer;
        }
      } else {
        return $arguments;
      }
    }
    exit;
  }

  /**
   * Inicio de sesión
   * POST /login - Válida credenciales de usuario, si todo sale bien agrega el usuario al contexto
   * email : <Correo eléctronico>
   * pwd : <Contraseña>
   */
  private function login() {
    // Validación Cross si el método de la petición es POST de lo contrario volverá estado de "no aceptable"
    if ($this->get_request_method() != "POST") {
      $this->response('', 406);
    }

    $requestData = array('email', 'pwd');
    foreach ($requestData as $rqd) {
      ${$rqd} = isset($this->_request[$rqd]) ? $this->_request[$rqd] : "";
    }

    if ($this->_request['fb'] == 1) {
      $resultLoginFb = $this->loginFb();
      $email = $resultLoginFb['email'];
      $pwd = $resultLoginFb['passwd'];
      $methodLogin = "getByFb";
    } else {
      $methodLogin = "getByEmail";
    }

    // Validaciones de entrada
    if (!empty($email) && !empty($pwd)) {
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $customer = new Customer();
        $authentication = $customer->$methodLogin(trim($email));

        if (!$authentication || !$customer->id) {
          // Error de autenticación
          $this->response('', 204); // Si no hay registros, estado "No Content"
        } else {
          // Web Service - customer affiliate
          $cafamWS = new CafamWebService();
          $affiliated = $cafamWS->checkAffiliation($customer->din, (int) $customer->id_din);
          $customer->updateGroupDiscount($customer->din, $affiliated);

          $context = Context::getContext();
          $context->cookie->id_compare = isset($context->cookie->id_compare) ? $context->cookie->id_compare : CompareProduct::getIdCompareByIdCustomer($customer->id);
          $context->cookie->id_customer = (int) ($customer->id);
          $context->cookie->customer_lastname = $customer->lastname;
          $context->cookie->customer_firstname = $customer->firstname;
          $context->cookie->is_guest = $customer->isGuest();
          $context->cookie->passwd = $customer->passwd;
          $context->cookie->email = $customer->email;
          $context->cookie->logged = 1;
          $customer->logged = 1;

          // Agrega el cliente a el contexto
          $context->customer = $customer;

          // Si todo sale bien, enviará cabecera de "OK" y los detalles del usuario en formato JSON
          unset($customer->passwd, $customer->last_passwd_gen);
          $gender = $customer->id_gender == 1 ? 'M' : ($customer->id_gender == 2 ? 'F' : '');
          $this->response($this->json(array(
          'id' => (int) $customer->id,
          'lastname' => $customer->lastname,
          'firstname' => $customer->firstname,
          'email' => $customer->email,
          'newsletter' => (bool) $customer->newsletter,
          'identification' => $customer->din,
          'gender' => $gender,
          'id_gender' => (int) $customer->id_gender,
          'id_din' => (int) $customer->id_din,
          'din' => $customer->din,
          'birthday' => $customer->birthday,
          'group' => Group::getCurrent(),
          'img_profile' => $customer->img_profile
          )), 200);
        }
      }
    }

    // Si las entradas son inválidas, mensaje de estado "Bad Request" y la razón
    $this->response($this->json(array(
    "success" => false,
    "message" => "Dirección de correo electrónico o contraseña no válidos"
    )), 400);
  }

  /**
   * GET /logout - Cierra sesión
   */
  private function logout() {
    // Validación Cross si el método de la petición es POST de lo contrario volverá estado de "no aceptable"
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    $context = Context::getContext();
    if (!$context->customer->isLogged()) {
      $this->response($this->json(array(
      'success' => false,
      'message' => 'El cliente debe estar autenticado para realizar operaciones con los recursos'
      )), 401);
    }

    $context->customer->mylogout();
    $this->response($this->json(array(
    'success' => true,
    'message' => "Cierre de sesión satisfactorio",
    )), 200);
  }

  /**
   * GET /addresses → recuperara una lista de direcciones
   * GET /addresses/1 → recupera la información de una dirección en especifico
   * POST /addresses → Crea una nueva dirección
   * PUT /addresses/1 → Actualiza la dirección con el ID 1
   * DELETE /addresses/1 → Elimina la dirección con ID 1
   */
  private function addresses() {
    $model = new Model();

    switch ($this->get_request_method()) {
      case 'GET'://consulta

        $envio = json_decode(base64_decode($_REQUEST['id']));
        $customer_id = $envio->ctmr;
        $address_id = $envio->address;
        $customer = new Customer($customer_id);
        // Validación del identificador
        if (isset($address_id) && !empty($address_id)) {
          $this->response($this->json($model->getAddress((int) $customer->id, (int) $address_id)), 200);
        }

        $this->response($this->json($model->getAddress((int) $customer->id)), 200);
        break;
      case $this->get_request_method() == 'POST' || $this->get_request_method() == "PUT"://inserta/actualiza

        $customer_id = $this->_request['ctmr'];
        $address_id = $_REQUEST['id'];
        $customer = new Customer($customer_id);

        $context = Context::getContext();
        $arguments = array(
        'id_customer' => $customer->id,
        'id_country' => $context->country->id,
        'firstname' => $customer->firstname,
        'lastname' => $customer->lastname
        );

        if (isset($address_id) && !empty($address_id)) {
          $arguments['id'] = (int) $address_id;
          if (!isset($arguments['id'])) {
            $this->response($this->json(array(
            'success' => false,
            'message' => "El identificador de la dirección es obligatorio"
            )), 400);
          }

          if (!$model->checkCustomerAddress($arguments['id'], $arguments['id_customer'])) {
            $this->response($this->json(array(
            'success' => false,
            'message' => "El cliente ha intentado acceder a un recurso al que no tiene acceso"
            )), 403);
          }
        }

        // if ($this->get_request_method() == "PUT") {
        // }

        $requestData = array(
        'id_state' => 0,
        'address1' => '',
        'address2' => '',
        'phone' => '',
        'phone_mobile' => '',
        'alias' => ''
        );
        foreach ($requestData as $rqd => $value) {
          $arguments[$rqd] = isset($this->_request[$rqd]) ? $this->_request[$rqd] : $value;
        }

        // Validación - La solicitud no fue válida
        if (empty($arguments['id_state'])) {
          $this->response($this->json(array(
          'success' => false,
          'message' => "El identificador del estado es obligatorio"
          )), 400);
        }

        $result = $model->setAddress($arguments);
        if ($result) {
          $this->response($this->json(array(
          'success' => true,
          'message' => "Dirección procesada correctamente",
          'id' => (int) $result
          )), 201);
        }

        $this->response($this->json(array(
        'success' => false,
        'message' => 'Se ha producido un error interno, no se pudo crear la dirección'
        )), 500);

        break;
      default://metodo NO soportado
        // Validación Cross si el método de la petición esta en los casos de lo contrario volverá estado de "no aceptable"
        $this->response('', 406);
        break;
    }
  }

  /**
   * GET /products?start=0&limit=10&order_by=id_product&order_way=ASC&id_category=5 - recuperara una lista de productos
   * GET /products/1 → recupera la información de una dirección en especifico
   */
  private function products() {
    // Validación Cross si el método de la petición es GET de lo contrario volverá estado de "no aceptable"
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    $context = Context::getContext();
    $link = new Link();

    // Obtiene un producto especifico
    if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
      // Decodifica una cadena de datos que ha sido codificada utilizando base-64
      $req = json_decode(base64_decode($_REQUEST['id']));

      if (!isset($req->idProduct) || empty($req->idProduct)) {
        $this->response($this->json(array(
        'success' => false,
        'message' => 'El id del carrito es obligatorio'
        )), 400);
      }

      if (isset($req->idCustomer) && !empty($req->idCustomer)) {
        $context->customer = new Customer((int) $req->idCustomer);
      }

      $product = new Product((int) $req->idProduct, true, $context->language->id, $context->shop->id);
      if (!$product->id || !$product->active || !$product->checkAccess($context->customer->id)) {

        $this->response($this->json(array(
        'success' => false,
        'message' => "Producto no disponible"
        )), 400);
      }

      $images = $product->getImages($context->language->id, $context);
      for ($i = 0; $i < count($images); $i++) {
        $images[$i]['medium_default'] = $this->protocol . $link->getImageLink($product->link_rewrite, $images[$i]['id_image'], 'medium_default');
        $images[$i]['large_default'] = $this->protocol . $link->getImageLink($product->link_rewrite, $images[$i]['id_image'], 'large_default');
        $images[$i]['thickbox_default'] = $this->protocol . $link->getImageLink($product->link_rewrite, $images[$i]['id_image'], 'thickbox_default');
      }

      $product->images = $images;
      $product->features = $product->getFrontFeatures($context->language->id);
      $priceDisplay = Product::getTaxCalculationMethod((int) $context->customer->id);
      $product->priceDisplay = $priceDisplay;

      if (!$priceDisplay || $priceDisplay == 2) {
        $product->price = $product->getPrice(true);
        $product->priceWithoutReduction = $product->getPriceWithoutReduct(false, null);
      } elseif ($priceDisplay == 1) {
        $product->price = $product->getPrice(false);
        $product->priceWithoutReduction = $product->getPriceWithoutReduct(true, null);
      }
      $product->formatPrice = Product::convertAndFormatPrice($product->price);
      $product->formatPriceWithoutReduction = Product::convertAndFormatPrice($product->priceWithoutReduction);
      $product->total_format_price = Product::convertAndFormatPrice($product->total_price);

      // Retorna los datos del producto
      $this->response($this->json($product), 200);
    }

    // Productos filtrados por categooría
    $arguments = array();
    $requestData = array(
    'start' => 0,
    'limit' => 10,
    'order_by' => "id_product", //price, date_add, date_upd, name, position
    'order_way' => "ASC",
    'id_category' => 5,
    'only_active' => true
    );
    foreach ($requestData as $rqd => $value) {
      ${$rqd} = isset($this->_request[$rqd]) ? $this->_request[$rqd] : $value;
    }

    // Validaciones
    $checkOrderBy = array("id_product", "price", "date_add", "date_upd", "name", "position");
    if (!in_array($order_by, $checkOrderBy)) {
      $this->response($this->json(array(
      "success" => false,
      "message" => "Ordenar por (order_by) inválido"
      )), 400);
    }

    $order_way = strtoupper($order_way);
    if ($order_way != "ASC" && $order_way != "DESC") {
      $this->response($this->json(array(
      "success" => false,
      "message" => "Forma de ordernar (order_way) inválido"
      )), 400);
    }

    $products = Product::getProducts(
    $context->language->id, $start, $limit, $order_by, $order_way, $id_category, $only_active, $context
    );

    for ($i = 0; $i < count($products); $i++) {
      $cover = Product::getCover($products[$i]['id_product'], $context);
      $products[$i]['id_image'] = (int) $cover['id_image'];
      $products[$i]['image'] = $this->protocol . $link->getImageLink($products[$i]['link_rewrite'], $cover['id_image'], 'medium_default');
      $products[$i]['total_format_price'] = Product::convertAndFormatPrice(Product::getPriceStatic($products[$i]['id_product']));
    }

    if ($products) {
      $category = new Category((int) $id_category);
      $this->response($this->json(array(
      'expr' => trim($category->getName($context->language->id)),
      'start' => $start,
      'limit' => $limit,
      'result' => $products
      )), 200);
    }

    $this->response($this->json(array(
    'success' => false,
    'message' => "Productos no encontrados"
    )), 400);
  }

  /**
   * Endpoint of Most popular product list
   * 
   * @return json Most popular product list
   */
  private function getProductsPopulars() {

    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    $context = Context::getContext();
    $link = new Link();

    $category = new CategoryCore((int) ConfigurationCore::get('HOME_FEATURED_CAT'), (int) $context->language->id);

    if (isset($_REQUEST['id'])) {
      $nb = $_REQUEST['id'];
    } else {
      $nb = (int) Configuration::get('HOME_FEATURED_NBR');
    }
    if (Configuration::get('HOME_FEATURED_RANDOMIZE')) {
      $listProductPopular = $category->getProducts((int) $context->language->id, 1, ($nb ? $nb : 8), null, null, false, true, true, ($nb ? $nb : 8));
    } else {
      $listProductPopular = $category->getProducts((int) $context->language->id, 1, ($nb ? $nb : 8), 'position');
    }

    $products = Product::getProducts(
    $context->language->id, 0, $nb, "id_product", "ASC", (int) ConfigurationCore::get('HOME_FEATURED_CAT'), true, $context
    );

    for ($i = 0; $i < count($products); $i++) {
      $cover = Product::getCover($products[$i]['id_product'], $context);
      $products[$i]['id_image'] = (int) $cover['id_image'];
      $products[$i]['image'] = $this->protocol . $link->getImageLink($products[$i]['link_rewrite'], $cover['id_image'], 'medium_default');
      $products[$i]['total_format_price'] = Product::convertAndFormatPrice(Product::getPriceStatic($products[$i]['id_product']));
    }

    if ($products) {
      $category = new Category((int) $id_category);
      $this->response($this->json(array(
      'expr' => trim($category->getName($context->language->id)),
      'start' => 0,
      'limit' => $nb,
      'result' => $products
      )), 200);
    } else {
      $this->response($this->json(array(
      'success' => false,
      'message' => "Productos no encontrados"
      )), 400);
    }
  }

  /**
   * GET /cart/1 → recupera la información del carrito en especifico
   */
  private function cart() {
    $context = Context::getContext();
    $model = new Model();

    error_log("\n\n\t\t a ver que pasa \n\n\t\t");

    switch ($this->get_request_method()) {
      case 'GET'://consulta
        // Obtiene un argumento codificado
        if (!isset($_REQUEST['id']) || empty($_REQUEST['id'])) {
          $this->response($this->json(array(
            'success' => false,
            'message' => 'El argumento del carrito es obligatorio'
          )), 400);
        }

        // Decodifica una cadena de datos que ha sido codificada utilizando base-64
        $req = json_decode(base64_decode($_REQUEST['id']));

        if (!isset($req->idCart) || empty($req->idCart)) {
          $this->response($this->json(array(
            'success' => false,
            'message' => 'El id del carrito es obligatorio'
          )), 400);
        }

        if (isset($req->idCustomer) && !empty($req->idCustomer)) {
          $context->customer = new Customer((int) $req->idCustomer);
        }

        $cart = $model->getCart((int) $req->idCart);

        if (!$cart) {
          $this->response($this->json(array(
            'success' => false,
            'message' => 'El carrito no esta disponible'
          )), 400);
        }

        $this->response($this->json($cart), 200);
        break;
        
      case 'PUT'://actualiza
        error_log(print_r($this->_request, true));
        $this->response($this->json($this->_request), 200);
        // Obtiene un producto especifico
        /*if (isset($this->_request['id_address'])
        && empty($this->_request['id_address'])) {
        $this->response($this->json(array(
        'success' => false,
        'message' => 'This product is no longer available'
        )), 400);
        }

        if (!$context->customer->isLogged()) {
        $this->response($this->json(array(
        'success' => false,
        'message' => 'El cliente debe estar autenticado para realizar operaciones con los recursos'
        )), 401);
        }

        $id_address = (int)$this->_request['id_address'];

        if (!$model->checkCustomerAddress($this->_request['id_address'], $customer->id)) {
        $this->response($this->json(array(
        'success' => false,
        'message' => "El cliente ha intentado acceder a un recurso al que no tiene acceso"
        )), 403);
        }

        $context->cart->id_address_delivery = (int) $id_address;
        $context->cart->id_address_invoice =  (int) $id_address;

        if ($context->cart->update()) {
        $this->response($this->json(array(
        'success' => true,
        'message' => 'Se actualizo la dirección del carrito'
        )), 200);
        }

        $this->response($this->json(array(
        'success' => false,
        'message' => 'Se ha producido un error interno, no se pudo actualizar la dirección del carrito'
        )), 500);*/
        break;
      
      case 'POST'://inserta
        $arguments = array();
        $requestData = array(
          'idCart' => 0,
          'idProduct' => 0,
          'qty' => 1,
          'op' => 'down'
        );
        foreach ($requestData as $rqd => $value) {
          ${$rqd} = isset($this->_request[$rqd]) ? $this->_request[$rqd] : $value;
        }

        if (isset($this->_request['idCustomer']) && !empty($this->_request['idCustomer'])) {
          $context->customer = new Customer((int) $this->_request['idCustomer']);
        }

        $cart = $model->setCart($idCart, $idProduct, $qty, $op);

        if (!is_array($cart)) {
          $this->response($this->json(array(
          'success' => false,
          'message' => $cart
          )), 400);
        }

        $this->response($this->json($cart), 200);

        break;
      default://metodo NO soportado
        // Validación Cross si el método de la petición esta en los casos de lo contrario volverá estado de "no aceptable"
        $this->response('', 406);
        break;
    }
  }

  private function test() {
    /* $this->response($this->json(array(
      'i' => isset($_REQUEST['id']) ? $_REQUEST['id'] : 0,
      'r' => isset($_REQUEST['rquest
      ']) ? $_REQUEST['rquest'] : '',
      'm' => $this->get_request_method()
      )), 200); */

    //echo "<pre>";
    $customer = new Customer(11);
    //print_r(Group::getCurrent());
    $this->response($this->json(Group::getCurrent()));
    //var_dump(Group::getReduction(11));
  }

  /**
   * Trae el listado de todas las ciudades activas
   * @return Array [success, message, data]
   */
  private function getCities() {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    $model = new Model();
    $this->response($this->json($model->getCities(), 200));
  }

  /**
   * Obtengo el listado de las ordenes por usuario
   */
  public function getOrderHistory() {

    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    $orders = Order::getCustomerOrders($_REQUEST['id']);

    $this->response($this->json($orders, 200));
  }

  /**
   * Obtengo el detalle de una orden
   */
  public function getOrderHistoryDetail() {

    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    $products = null;
    $dataProductsDetail = [];

    $idsCustomerOrders = json_decode(base64_decode($_REQUEST['id']));

    $order = new Order($idsCustomerOrders->order_id);

    if (Validate::isLoadedObject($order) && $order->id_customer == $idsCustomerOrders->customer_id) {
      $products = $order->getProducts();
      $addressDelivery = new Address((int) $order->id_address_delivery);
      $dlv_adr_fields = AddressFormat::getOrderedAddressFields($addressDelivery->id_country);
      $deliveryAddressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($addressDelivery, $dlv_adr_fields);

      $i = 0;

      foreach ($products as $key => $value) {

        $dataProductsDetail['detail'][$i]['product_name'] = $value['product_name'];
        $dataProductsDetail['detail'][$i]['product_quantity'] = $value['product_quantity'];
        $dataProductsDetail['detail'][$i]['total_price'] = $value['total_price_tax_incl'];
        $dataProductsDetail['detail'][$i]['product_price'] = $value['product_price'];
        $dataProductsDetail['detail'][$i]['total_shipping_price_tax_incl'] = $value['total_shipping_price_tax_incl'];
        $i++;
      }

      $dataProductsDetail['delivery'] = $deliveryAddressFormatedValues;

      /* print_r($deliveryAddressFormatedValues);
        exit; */
      $this->response($this->json($dataProductsDetail, 200));
    } else {
      unset($order);
      $dataOrderDetail = array('This order cannot be found.');
      $this->response($this->json($dataOrderDetail, 204));
    }
  }

  /**
   * Add a comment to this line
   * Establece la imagen de perfil del usuario
   */
  private function setProfileImage() {

    if ($this->get_request_method() != "POST") {
      $this->response('', 406);
    }
    $options = $_REQUEST;
    $model = new Model();
    $flag = true;
    foreach ($_FILES as $key) {
      $imagen = $model->add_image($key, $options);
      if (!$imagen) {
        $flag = false;
        break;
      }
    }
    $this->response(json_encode(array('success' => $flag)), 200);
  }

  private function getImgProfile() {

    if (isset($_REQUEST['id'])) {

      $id_customer = base64_decode($_REQUEST['id']);
      $customer = new Model();
      $img = $customer->get_img_profile($id_customer);

      if (isset($img)) {
        if ($img == "") {
          $Img_customer = "assets/img/home/perfil.png";
          $img = "perfil.png";
        } else {
          $Img_customer = _PS_BASE_URL_ . __PS_BASE_URI__ . 'img/customers/profile/' . $img;
        }
        $response = array('success' => true, 'Url' => $Img_customer, 'img' => $img);
        $this->response($this->json($response, 200));
      } else {
        $response = array('success' => false, 'Url' => "Not Fount");
        $this->response($this->json($response, 400));
      }
    }
  }

  public function getTermsConditions() {

    $sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
    SELECT l.content
    FROM ' . _DB_PREFIX_ . 'cms_lang l
    WHERE l.id_cms = ' . (int) $_REQUEST['id']);

    $this->response($this->json($sql, 204));
  }

  public function getStoreContacts() {

    $sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
    SELECT name, latitude, longitude
    FROM ' . _DB_PREFIX_ . 'store s
    WHERE s.active = 1');

    $this->response($this->json($sql), 200);
  }

}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
header('Access-Control-Allow-Credentials: true');

// Iniciar
$api = new API;
$api->processApi();
?>
