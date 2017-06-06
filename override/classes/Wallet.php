<?php

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
                        PP.is_virtual,
                        M.name manufacturer,
                        PP.price_shop AS price,
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
            $card['card_code_cry'] = $card['card_code'];
            $card['card_code'] = Encrypt::decrypt($card['secure_key'] , $card['card_code']);
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
}
