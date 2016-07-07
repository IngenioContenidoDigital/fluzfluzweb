<?php
include_once('../../../config/config.inc.php');
include_once('../../../config/defines.inc.php');

if ( isset($_POST) && !empty($_POST) && isset($_POST["action"]) && !empty($_POST["action"]) ) {
    switch ( $_POST["action"] ) {
        case "export":
            $fluzfluzcodes = new fluzfluzcodes_admin();
            echo $fluzfluzcodes->exportProductCodes( $_POST["product"] );
            break;
        
        case "deletecode":
            $fluzfluzcodes = new fluzfluzcodes_admin();
            echo $fluzfluzcodes->deleteCode( $_POST["product"], $_POST["code"] );
            break;
        
        default:
            echo 0;
    }
} else { echo 0; }

class fluzfluzcodes_admin {
    public function exportProductCodes( $product ) {
        $query = "SELECT "._DB_PREFIX_."product_code.`code`,(CASE "._DB_PREFIX_."product_code.id_order WHEN 0 THEN 'Disponible' ELSE 'Asignado' END) AS estado, CASE "._DB_PREFIX_."product_code.id_order WHEN 0 THEN '' ELSE "._DB_PREFIX_."product_code.id_order END AS `order` 
                    FROM ps_product_code
                    WHERE id_product = ".$product;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
        
        $excel = "";
        
        $excel .= "header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename='CodigosProducto.xlsx'');
                    header('Cache-Control: max-age=0');";
        
        $excel .= "<hmtl><table>
                    <thead>
                        <tr>
                            <th><strong>Codigos</strong></th>
                            <th><strong>Estado</strong></th>
                            <th><strong>Orden</strong></th>
                        </tr>
                    </thead>
                    <tbody>";

        foreach ($result as $code) {
            $excel .= "<tr>
                            <td>".$code['code']."</td>
                            <td>".$code['estado']."</td>
                            <td>".$code['order']."</td>
                        </tr>";
        }
        
        $excel .= "</tbody>
                </table>";
        
        return $excel;
    }

    public function deleteCode( $product, $code ) {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS( "DELETE FROM "._DB_PREFIX_."product_code WHERE code = '".$code."' AND id_product = '".$product."'" );
        return $result;
    }
}