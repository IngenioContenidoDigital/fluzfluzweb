<?php
error_reporting(0);

class cardsviewController extends cardsviewControllerCore {

    public function initContent() {
        parent::initContent();
        
        $product = new Product(Tools::getValue("id_product"));
          
        $link = $this->context->link->getPageLink('cardsview', $product->id_manufacturer, array(), true);
        $card = $this->getCardsbySupplier($this->context->customer->id, $product->id_manufacturer, Tools::getValue("id_order"));

        $this->context->smarty->assign(array(
            's3'=> _S3_PATH_,
            'cards'=> $card,
            'page' => ((int)(Tools::getValue('p')) > 0 ? (int)(Tools::getValue('p')) : 1),
            'nbpagination' => ((int)(Tools::getValue('n') > 0) ? (int)(Tools::getValue('n')) : 10),
            'nArray' => array(10, 20, 50),
            'pagination_link' => $link . (strpos($link, '?') !== false ? '&' : '?'),
            'max_page' => floor(sizeof($card) / ((int)(Tools::getValue('n') > 0) ? (int)(Tools::getValue('n')) : 10))
        ));
        $this->setTemplate(_PS_THEME_DIR_.'cardsview.tpl');
    }

    public function getCardsbySupplier($id_customer, $id_manufacturer, $id_order) {
        $query = "SELECT
                        PC.`code` AS card_code, 
                        PL.`name` AS product_name,
                        PL.link_rewrite,
                        PL.id_lang,
                        PL.description,
                        PL.description_short,
                        PC.id_product, 
                        PP.id_manufacturer, 
                        PP.id_supplier, 
                        PP.price_shop AS price,
                        ROUND(PP.price) AS price_shop,
                        DATE_FORMAT(PO.date_add, '%M %d, %Y') AS date,
                        PPI.id_image, 
                        PPI.cover,
                        C.secure_key
                FROM "._DB_PREFIX_."product_code PC
                INNER JOIN "._DB_PREFIX_."order_detail POD ON PC.id_order = POD.id_order AND PC.id_product = POD.product_id
                INNER JOIN "._DB_PREFIX_."orders PO ON POD.id_order = PO.id_order
                INNER JOIN "._DB_PREFIX_."product PP ON PC.id_product = PP.id_product
                INNER JOIN "._DB_PREFIX_."product_lang PL ON PP.id_product = PL.id_product
                LEFT JOIN "._DB_PREFIX_."image AS PPI ON PP.id_product = PPI.id_product
                LEFT JOIN "._DB_PREFIX_."customer C ON PO.id_customer = C.id_customer
                WHERE (
                    (PO.current_state = 2 OR PO.current_state = 5)
                    AND (PO.id_customer =".(int)$id_customer.")
                    AND (PP.id_manufacturer =".(int)$id_manufacturer.")
                    AND (PL.id_lang=".$this->context->language->id.")
                )
                AND PO.id_order = ".$id_order."
                GROUP BY PC.`code`, PL.`name`, PL.link_rewrite
                ORDER BY product_name ASC";

        $cards = Db::getInstance()->executeS($query);
        
        foreach ($cards as &$card) {
            $card['card_code_cry'] = $card['card_code'];
            $card['card_code'] = Encrypt::decrypt($card['secure_key'] , $card['card_code']);
        }
        
        return $cards;
    }
}
