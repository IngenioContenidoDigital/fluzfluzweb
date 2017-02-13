<?php

class fluzfluzapishoppingcartModuleFrontController extends ModuleFrontController{
    
    public function init(){
        parent::init();
        $this->display_header = false;
        $this->display_footer = false;
    }
    
    public function postProcess(){
        $customer=$this->context->customer->id;
        $cart=$this->context->cart->id;
        $phone=Tools::getValue('phone');
        $product=Tools::getValue('product');
        $action=Tools::getValue('action');
        $total_address=0;
        $response="";
        
        if($action=='add'){
            echo $this->savePhoneinCart($cart,$product, $phone);
        }
        
        $numadd="SELECT * FROM "._DB_PREFIX_."address WHERE id_customer=".$customer;
        Db::getInstance()->executeS($numadd);
        $total_address=Db::getInstance()->numRows();
        $total_address+=1;
        
        
        if ($action=='new'){
            $sql="INSERT INTO "._DB_PREFIX_."address (id_country,id_state,id_customer,alias,lastname,firstname,address1,address2,city,phone_mobile, active, date_add, date_upd)"
                . "(SELECT id_country, id_state, id_customer, CONCAT('Direccion',' - ', '$total_address'), lastname, firstname, address1, address2, city, '$phone', '1','".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."' FROM "._DB_PREFIX_."address WHERE id_customer='$customer' ORDER BY date_add DESC LIMIT 1)";
        
            try{
                Db::getInstance()->execute($sql);
                $result="success";
            }catch(Exception $e){
                $result=$e->getMessage();
            }
        }
        
        echo $result;
    }
    
    public function savePhoneinCart($cart,$product,$phone){
        $check="SELECT * FROM "._DB_PREFIX_."webservice_external_telco WHERE id_cart=".$cart." AND id_product=".$product;
        if(Db::getInstance()->executeS($check)){
            $phonetocharge="UPDATE "._DB_PREFIX_."webservice_external_telco SET phone_mobile='$phone' WHERE id_cart=".$cart." AND id_product=".$product;
        }else{
            $phonetocharge="INSERT INTO "._DB_PREFIX_."webservice_external_telco (id_cart, id_product, phone_mobile) VALUES ('$cart', '$product', '$phone')";
        }
        try{
            Db::getInstance()->execute($phonetocharge);
            $result="success";
        }catch (Exception $e){
            $result=$e->getMessage();
        }
        return $result;
    }
    
}