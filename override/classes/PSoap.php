<?php

/**
 * Description of PSoap
 *
 * @author Ingenio Contenido Digital SAS
 * contacto@ingeniocontenido.co
 * www.ingeniocontenido.co
 * 
 * Esta clase obtiene las credenciales y configuración de un servicio web SOAP para realizar 
 * de forma dinámica los llamados al servicio correspondiente
 * 
 * Se requiere de la creación de un tabla en la BD de datos
 * Nombre Tabla: webservice_external
 * Campos:
 * id_webservice_external   int     10  PK  Llave Principal Autonumérico
 * name                     varchar 50      Nombre del Servicio
 * uri                      varchar 255     Dirección URL del servicio
 * login                    varchar 100     Usuario de inicio de sesión
 * password                 varchar 255     Contraseña del usuario de inicio de sesión
 * request                  text            Estructura de la petición a realizar
 * 

  DROP TABLE IF EXISTS `ps_webservice_external`;
  CREATE TABLE `ps_webservice_external` (
  `id_webservice_external` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  `uri` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  `login` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  `password` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  `request` text COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`id_webservice_external`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

 * 
 */

class PSoapCore extends ObjectModel {
    protected $sclient;
    protected $uri;
    
    public function __construct($wsid='') {
        $conn = $this->getCredentials($wsid);
        $this->sclient = new SoapClient($conn['wsdl'],array('login'=> $conn['login'],'password'=> $conn['password']));
    }
    
    private function getCredentials($wsid=''){
        
        /*Obtiene las credenciales de acceso en el momento de llamar el constructor*/
        $sql="SELECT * FROM "._DB_PREFIX_."webservice_external AS wse WHERE wse.id_webservice_external=".$wsid;
        try{
            $data = Db::getInstance()->getRow($sql);
            $this->uri=$data['uri'];
            return $data;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function doRequest($request, $action='', $version, $respuesta=1){
        /*Realiza la petición al uri definido y retorna resultado o error*/
        try{
            $response = $this->sclient->__doRequest($request, $this->uri, $action, $version,$respuesta);
        } catch(SoapFault $sf){
            $response = $sf->getMessage();
        }catch (Exception $e){
            $response = $e->getMessage();
        }
        return response;
    }
    
    public function lastResponse(){
        /*Devuelve la ultima respuesta obtenida*/
        return $this->sclient->__getLastResponse();
    }
    
    public function getFunctions(){
        /*Solo Funciona con WSDL*/
        return $this->sclient->__getFunctions();
    }
}
