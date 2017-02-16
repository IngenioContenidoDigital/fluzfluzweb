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
 */

class CSoap extends ObjectModel {
    protected $sclient;
    private $login;
    private $password;
    public $uri;
    public $request;
    public $last_response = NULL;
    public $header = array(
        "Content-type: text/xml; charset=utf-8",
        "Accept: text/xml",
        "Cache-Control: no-cache",
        "Pragma: no-cache"
    );
    
    public function __construct($wsid='') {
        $conn = $this->getCredentials($wsid);
        $this->login=$conn['login'];
        $this->password=$conn['password'];
        $this->sclient = curl_init($conn['uri']);
    }
    
    private function getCredentials($wsid=''){
        /*Obtiene las credenciales de acceso en el momento de llamar el constructor*/
        $sql="SELECT * FROM "._DB_PREFIX_."webservice_external AS wse WHERE wse.id_webservice_external=".$wsid;
        try{
            $data = Db::getInstance()->getRow($sql);
            $this->uri=$data['uri'];
            $this->request=$data['request'];
            return $data;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function doRequest($action, $request){
        /*Realiza la petición al uri definido y retorna resultado o error*/
        array_push($this->header,"SOAPAction: \"".$action."\"");
        array_push($this->header,"Content-length: ".strlen($request));
        try{
            curl_setopt($this->sclient, CURLOPT_HTTPHEADER, $this->header);
            curl_setopt($this->sclient, CURLOPT_POST, 1);
            curl_setopt($this->sclient, CURLOPT_POSTFIELDS, "$request");
            curl_setopt($this->sclient, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($this->sclient, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->sclient, CURLOPT_TIMEOUT, 90);
            curl_setopt($this->sclient, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($this->sclient, CURLOPT_USERPWD, "$this->login\":\"$this->password");
            $response = curl_exec($this->sclient);
            $this->last_response=$response;
            $error = curl_errno($this->sclient);
            if($error!=0) $response=$error;
        }catch (Exception $e){
            $response = $e->getCode();
        }
        return $response;
    }
    
    public function close(){
        curl_close($this->sclient);
    }
    
    public function getResponseCode(){
        $code = curl_getinfo($this->sclient, CURLINFO_HTTP_CODE);
        return $code;
    }
    
    public function lastResponse(){
        /*Devuelve la ultima respuesta obtenida*/
        return $this->last_response;
    }
    
    public function setRequest($xml){
        $this->request=$xml;
    }
    
    public function setValue($request,$function,$name,$value){
        $xml = new SimpleXMLElement($request);
        $xml->children('soapenv',true)->Body->children('ext',true)->$function->children('ext',true)->$name=$value;
        return $xml->asXML();
    }
    
}
