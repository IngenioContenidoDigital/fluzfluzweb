<?php 
error_reporting(0);

require_once('./classes/codeBar/barcode.class.php');

$algo="";
$barcode= new BARCODE();
$barnumber=$_POST['codeImg2'];
$ruta="./upload/";
$archivo="code-".$barnumber;
$extension=".png";
if (file_exists($ruta.$archivo.$extension)) unlink($ruta.$archivo.$extension);
        if (!empty($barnumber)) {
            
            $algo = $barcode->_c128Barcode($barnumber,1,$archivo,$ruta);
            //if(isset($algo)){
                
              echo $ruta.$archivo.$extension;
              
            //}else{
            //    echo false;
            //}
        }
     

