<?php
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
          $this->context->smarty->assign(array(
            'cards'=>$this->getCardsbySupplier($this->context->customer->id, Tools::getValue("manufacturer"))
          ));
          $this->setTemplate(_PS_THEME_DIR_.'cardsview.tpl');
      }
      
      public function getCardsbySupplier($id_customer,$id_manufacturer){
          $query="SELECT PC.`code` AS card_code, PL.link_rewrite, PL.`name` AS product_name,
PC.id_product AS id_product, PP.id_manufacturer, PP.id_supplier
FROM ps_product_code AS PC
INNER JOIN ps_order_detail AS POD ON PC.id_order = POD.id_order AND PC.id_product = POD.product_id
INNER JOIN ps_orders AS PO ON POD.id_order = PO.id_order
INNER JOIN ps_product AS PP ON PC.id_product = PP.id_product
INNER JOIN ps_product_lang AS PL ON PP.id_product = PL.id_product
WHERE ((PO.current_state = 2 OR PO.current_state = 5) AND (PO.id_customer = ".(int)$id_customer.") AND (PP.id_manufacturer =".(int)$id_manufacturer."))
GROUP BY PC.`code`, PL.link_rewrite, PL.`name` ORDER BY product_name ASC";
          $cards=Db::getInstance()->executeS($query);
          return $cards;
      }
      
}
