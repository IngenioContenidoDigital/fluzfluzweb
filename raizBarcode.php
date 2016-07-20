<?php 
include_once('./config/defines.inc.php');
require_once('./classes/codeBar/barcode.class.php');
include_once('./config/config.inc.php');

error_reporting(0);

if ( isset($_POST) && !empty($_POST) && isset($_POST["action"]) && !empty($_POST["action"]) ) {
    $algo="";
    $barnumber=$_POST['codeImg2'];
    $idproduct=$_POST['idproduct'];
    $ruta="./upload/";
    $archivo="code-".$barnumber;
    $extension=".png";
    $used = $_POST["val"];
    
    if (file_exists($ruta.$archivo.$extension)) unlink($ruta.$archivo.$extension);
    
    switch ( $_POST["action"] ) {        
        case "consultcodebar":
            $raizBarcode = new raizBarcode();
            $response['code'] = $raizBarcode->consultcodebar($idproduct,$ruta,$archivo,$extension,$barnumber);
            $response['used'] = $raizBarcode->getUsed($idproduct,$barnumber);
            echo json_encode($response);
            break;

        case "updateUsed":
            $raizBarcode = new raizBarcode();
            echo $raizBarcode->updateUsed($idproduct, $barnumber, $used);
            break;

        default:
            echo 0;
    }
    
} else {
    echo 0;
}
     
class raizBarcode {
    public function consultcodebar($idproduct,$ruta,$archivo,$extension,$barnumber) {
        $query = 'SELECT codetype FROM '._DB_PREFIX_.'product WHERE id_product = '.$idproduct;
        $row = Db::getInstance()->getRow($query);
        $code = $row["codetype"];
        
        if (!empty($barnumber)) {
            $barcode= new BARCODE();
            if($code==1){
                $algo = $barcode->_c128Barcode($barnumber,1,$archivo,$ruta);
            }
            else {
                $algo = $barcode->QRCode_save("text", $barnumber, $archivo, $ruta, $type = "png", $height = 50, $scale = 2, $bgcolor = "#FFFFFF", $barcolor = "#000000", $ECLevel = "L", $margin = true);
            }

            return $ruta.$archivo.$extension;
        }
    }
    
    public function updateUsed($idproduct, $barnumber, $used) {
        $query2 = 'UPDATE '._DB_PREFIX_.'product_code SET used ='.$used.' WHERE id_product = '.$idproduct.' AND code= "'.$barnumber.'"';
        return Db::getInstance()->execute($query2);
    }
    
    public function getUsed($idproduct, $barnumber){
        $query = 'SELECT used FROM '._DB_PREFIX_.'product_code WHERE id_product = '.$idproduct.' AND code= "'.$barnumber.'"';
        $row = Db::getInstance()->getRow($query);
        return $row["used"];
    }   
}

