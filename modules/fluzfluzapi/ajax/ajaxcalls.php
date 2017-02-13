<?php

include_once('../../../config/config.inc.php');
include_once('../../../init.php');
include_once('../../../config/defines.inc.php');

if(Tools::getValue('action')=='api'){
    $ws = Tools::getValue('webservice');
    $op = Tools::getValue('operador');
    $product = Tools::getValue('product');
    
    echo setWS($ws,$op,$product);
}


function setWS($webservice, $operator, $product){
    $sql="";

    if($webservice<=0){
        $sql="DELETE FROM "._DB_PREFIX_."webservice_external_product WHERE id_product=".$product;
        $stock="UPDATE "._DB_PREFIX_."stock_available SET quantity=0 WHERE id_product='".$product."'";
    }else{
        $check="SELECT * FROM "._DB_PREFIX_."webservice_external_product AS wep WHERE wep.id_product=".$product;
        Db::getInstance()->executeS($check);
        $stock="UPDATE "._DB_PREFIX_."stock_available SET quantity=10000 WHERE id_product='".$product."'";
        if(Db::getInstance()->numRows()>0){
            $sql="UPDATE "._DB_PREFIX_."webservice_external_product AS wep SET wep.id_webservice_external=".$webservice.", wep.id_operator=".$operator." WHERE wep.id_product=".$product;
        }else{
            $sql="INSERT INTO "._DB_PREFIX_."webservice_external_product (id_webservice_external, id_product, id_operator) "
                . "VALUES ('$webservice','".$product."', '$operator')";
        }
    }

    try{
        Db::getInstance()->execute($sql);
        Db::getInstance()->execute($stock);
        $message='success';
    }catch(Exception $e){
        $message=$e->getMessage();
    }
    return $message;
}