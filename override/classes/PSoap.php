<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PSoap
 *
 * @author lfelipeqn
 */
class PSoapCore extends ObjectModel {
    protected $sclient;
    
    public function __construct($wsid='') {
        $conn = $this->getCredentials($wsid);
        $sclient = new SoapClient($conn['wsdl'],array('login'=> $conn['login'],'password'=> $conn['password']));
    }
    
    private function getCredentials($wsid=''){
        $sql="SELECT * FROM "._DB_PREFIX_."webservice_external AS wse WHERE wse.id_webservice_external=".$wsid;
        try{
            $data = Db::getInstance()->getRow($sql);
            return $data;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
}
