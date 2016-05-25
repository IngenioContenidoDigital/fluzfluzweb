<?php 
require_once('classes/codeBar/barcode.class.php');
$algo="";
$barcode= new BARCODE();
$barnumber=$_POST['codeImg2'];
$ruta="upload/";
$archivo="code-".$barnumber;
$extension=".png";
if (file_exists($ruta.$archivo.$extension)) unlink($ruta.$archivo.$extension);
        if (!empty($barnumber)) {
            
            $algo = $barcode->_c128Barcode($barnumber,1,$archivo,$ruta);
            //$response = $file_path."<br/><img src='".$file_path."'>"; 
            if(isset($algo)){
                
              echo $algo;
            }
            else{
                echo false;
            }
        }    
     
