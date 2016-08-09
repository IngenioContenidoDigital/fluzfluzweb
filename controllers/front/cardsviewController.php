<?php
error_reporting(0);
class cardsviewControllerCore extends FrontController{
    public $auth = true;
    public $php_self = 'cardsview';
    public $authRedirection = 'cardsview';
    public $ssl = true;
      
    
      public function setMedia(){
         parent::setMedia();
         $this->addCSS(_THEME_CSS_DIR_.'cardsview.css');
      }

      public function initContent(){
          
          parent::initContent();
          
          $link = $this->context->link->getPageLink('cardsview', Tools::getValue("manufacturer"), array(), true);
          $card = $this->getCardsbySupplier($this->context->customer->id, Tools::getValue("manufacturer"),false, true, ((int)(Tools::getValue('n')) > 0 ? (int)(Tools::getValue('n')) : 10), ((int)(Tools::getValue('p')) > 0 ? (int)(Tools::getValue('p')) : 1));
          
          $this->context->smarty->assign(array(
            'cards'=>$card,
            'page' => ((int)(Tools::getValue('p')) > 0 ? (int)(Tools::getValue('p')) : 1),
            'nbpagination' => ((int)(Tools::getValue('n') > 0) ? (int)(Tools::getValue('n')) : 10),
            'nArray' => array(10, 20, 50),
            'pagination_link' => $link . (strpos($link, '?') !== false ? '&' : '?'),
            'max_page' => floor(sizeof($card) / ((int)(Tools::getValue('n') > 0) ? (int)(Tools::getValue('n')) : 10))
          ));
          $this->setTemplate(_PS_THEME_DIR_.'cardsview.tpl');
      }
      
      public function getCardsbySupplier($id_customer,$id_manufacturer,$onlyValidate = false,$pagination = false, $nb = 10, $page = 1){
          $query="SELECT PC.`code` AS card_code, 
	PL.`name` AS product_name, PL.link_rewrite, PL.id_lang,  PL.description,
	PC.id_product, 
	PP.id_manufacturer, 
	PP.id_supplier, 
        PP.price_shop AS price,
	PPI.id_image, 
	PPI.cover
FROM ps_product_code PC INNER JOIN ps_order_detail POD ON PC.id_order = POD.id_order AND PC.id_product = POD.product_id
	 INNER JOIN ps_orders PO ON POD.id_order = PO.id_order
	 INNER JOIN ps_product PP ON PC.id_product = PP.id_product
	 LEFT JOIN ps_image AS PPI ON PP.id_product = PPI.id_product
	 INNER JOIN ps_product_lang PL ON PP.id_product = PL.id_product
WHERE ((PO.current_state = 2 OR PO.current_state = 5) AND (PO.id_customer =".(int)$id_customer.") AND (PP.id_manufacturer =".(int)$id_manufacturer.") AND (PPI.cover=1) AND (PL.id_lang=".$this->context->language->id."))
GROUP BY PC.`code`, PL.`name`, PL.link_rewrite
ORDER BY product_name ASC";
          
          if ($onlyValidate === true)
                $query .= ' GROUP BY PC.`code`, PL.`name`, PL.link_rewrite ORDER BY product_name ASC '.
		($pagination ? 'LIMIT '.(((int)($page) - 1) * (int)($nb)).', '.(int)$nb : '');
          
          $cards=Db::getInstance()->executeS($query);
          return $cards;
      }
      
}
