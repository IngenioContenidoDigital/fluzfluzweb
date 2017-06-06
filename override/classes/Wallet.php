<?php
require_once('./classes/codeBar/barcode.class.php');

class WalletCore extends ObjectModel
{
    
    public function __construct()
    {
        parent::__construct();
    }

    public static function getCards($id_customer, $id_manufacturer)
    {
        $context = Context::getContext();
        $query = "SELECT
                        PC.id_product_code,
                        PC.id_product, 
                        PC.code AS card_code, 
                        PC.used,
                        PC.price_card_used,
                        PL.name AS product_name,
                        PL.description,
                        PL.description_short,
                        PP.id_manufacturer, 
                        M.name manufacturer,
                        PP.price_shop AS price,
                        PP.codetype,
                        ROUND(PP.price) AS price_shop,
                        DATE_FORMAT(PO.date_add, '%d/%m/%Y') AS date,
                        DATE_FORMAT(PP.expiration, '%d/%m/%Y') AS expiration,
                        C.secure_key
                FROM "._DB_PREFIX_."product_code PC
                INNER JOIN "._DB_PREFIX_."order_detail POD ON PC.id_order = POD.id_order AND PC.id_product = POD.product_id
                INNER JOIN "._DB_PREFIX_."orders PO ON POD.id_order = PO.id_order
                INNER JOIN "._DB_PREFIX_."product PP ON PC.id_product = PP.id_product
                INNER JOIN "._DB_PREFIX_."product_lang PL ON PP.id_product = PL.id_product
                INNER JOIN "._DB_PREFIX_."manufacturer M on PP.id_manufacturer = M.id_manufacturer
                LEFT JOIN "._DB_PREFIX_."customer C ON PO.id_customer = C.id_customer
                WHERE (
                    (PO.current_state = 2)
                    AND (PO.id_customer = ".(int)$id_customer.")
                    AND (PP.id_manufacturer = ".(int)$id_manufacturer.")
                    AND (PL.id_lang = ".$context->language->id.")
                )
                -- AND PO.id_order = 0
                GROUP BY PC.code, PL.name
                ORDER BY used ASC";

        $cards = Db::getInstance()->executeS($query);
        
        foreach ($cards as &$card) {
            $code = str_replace(' ', '', Encrypt::decrypt($card['secure_key'] , $card['card_code']));

            $card['card_code_cry'] = $card['card_code'];
            $card['code_bar'] = Wallet::getCodebar($card['id_product'] , $code , $card['card_code_cry']);

            $i = 1;
            $cardcode = "";
            $code = str_split($code);
            foreach ( $code as $char ) {
                $cardcode .= $char;
                if ( $i % 4 == 0 ) { $cardcode .= " "; }
                $i++;
            }
            $card['card_code'] = $cardcode;
        }

        return $cards;
    }

    public static function getManufacturerAddress($id_manufacturer)
    {
        $query = "SELECT firstname, address1, city
                FROM "._DB_PREFIX_."address
                WHERE id_manufacturer = ".(int)$id_manufacturer."
                AND deleted = 0
                ORDER BY city";

        $address = Db::getInstance()->executeS($query);
        
        return $address;
    }
    
    public static function setUsedCard($card,$used)
    {
        $query = "UPDATE "._DB_PREFIX_."product_code
                SET used = ".$used.", state = '".($used == 1 ? 'Usada' : 'Terminada')."'
                WHERE id_product_code = ".$card;
        $setUsed = Db::getInstance()->execute($query);
        
        return $setUsed;
    }
    
    public static function setValueUsed($card,$value)
    {
        $query = "UPDATE "._DB_PREFIX_."product_code
                SET price_card_used = ".$value."
                WHERE id_product_code = ".$card;
        $setValue = Db::getInstance()->execute($query);
        
        return $setValue;
    }
    
    public static function getCodebar($product,$code,$codeCry)
    {
        $codebar = "";

        $codetype = Db::getInstance()->getValue("SELECT codetype
                                                FROM "._DB_PREFIX_."product
                                                WHERE id_product = ".$product);

        if ( $code != "" && $codeCry != "" && $codetype != "" ) {
            $ruta = "./upload/";
            $archivo = "code-".preg_replace('([^A-Za-z0-9\*\+\=\_\-\.])', '', $codeCry);
            $extension = ".png";

            // if ( file_exists($ruta.$archivo.$extension) ) { unlink($ruta.$archivo.$extension); }

            $barcode = new BARCODE();
            switch ($codetype) {
                case 0:
                    $algo = $barcode->QRCode_save("text", $code, $archivo, $ruta, $type = "png", $height = 50, $scale = 2, $bgcolor = "#FFFFFF", $barcolor = "#000000", $ECLevel = "L", $margin = true);
                    $codebar = $ruta.$archivo.$extension;
                    break;
                case 1:
                    $algo = $barcode->_c128Barcode($code, 1, $archivo, $ruta);
                    $codebar = $ruta.$archivo.$extension;
                    break;
                case 3:
                    $algo = $barcode->_eanBarcode($code, 1, $archivo, $ruta);
                    $codebar = $ruta.$archivo.$extension;
                    break;
            }
        }
        
        return $codebar;
    }
}
