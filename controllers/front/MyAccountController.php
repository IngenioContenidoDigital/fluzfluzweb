<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class MyAccountControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'my-account';
    public $authRedirection = 'my-account';
    public $ssl = true;
    
    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'my-account.css');
        $this->addCSS(_THEME_CSS_DIR_.'cardsview.css');
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        
        
        $totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
        $totalAvailable = round(isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0);
        $this->context->smarty->assign('totalAvailable', $totalAvailable);
        
        $customerProfile = $this->getProfileCustomer($this->context->customer->id);
        $this->context->smarty->assign('customerProfile', $customerProfile);
        $this->context->smarty->assign('profile', $this->context->customer->id);
        
        $datePoint = $this->getPointsLastDays($this->context->customer->id);
        $lastPoint = round($datePoint, $precision=0);
        $this->context->smarty->assign('lastPoint', $lastPoint);
        
        $sql = 'SELECT COUNT(id_customer) as members FROM ps_rewards_sponsorship';
        $sqlmember = Db::getInstance()->getRow($sql);
        $members = $sqlmember['members'];
        $this->context->smarty->assign('members', $members);
        
        $has_address = $this->context->customer->getAddresses($this->context->language->id);
        //$manufacturer = $_COOKIE["manufacturerCards"];
        //$manufacturer = $_GET['id_manu'];
        $manufacturer = 14;
        
        $this->context->smarty->assign(array(
            'manufacturers'=> $this->getProductsByManufacturer($this->context->customer->id),
            'has_customer_an_address' => empty($has_address),
            'cards'=>$this->getCardsbySupplier($this->context->customer->id, $manufacturer),
            'voucherAllowed' => (int)CartRule::isFeatureActive(),
            'topPoint'=> $this->TopNetworkUnique(),
            'worstPoint'=> $this->WorstNetworkUnique(),
            'returnAllowed' => (int)Configuration::get('PS_ORDER_RETURN')
        ));
        $this->context->smarty->assign('HOOK_CUSTOMER_ACCOUNT', Hook::exec('displayCustomerAccount'));

        $this->setTemplate(_PS_THEME_DIR_.'my-account.tpl');
    }
    
    public function getCardsbySupplier($id_customer,$id_manufacturer){
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
        ORDER BY product_name ASC LIMIT 6";
          
          /*if ($onlyValidate === true)
                $query .= ' AND r.id_reward_state = '.(int)RewardsStateModel::getValidationId();
                $query .= ' ORDER BY POD.date_add DESC '.
                ($pagination ? 'LIMIT '.(((int)($page) - 1) * (int)($nb)).', '.(int)$nb : '');*/
          
        $cards=Db::getInstance()->executeS($query);
        return $cards;
      }
    
    public function getProductsByManufacturer($id_customer){
        
        $query='SELECT
                PM.id_manufacturer AS id_manufacturer,
                PM.`name` AS manufacturer_name,
                Count(OD.product_id) AS products,
                Sum(PP.price) AS total
                FROM
                ps_orders AS PO
                INNER JOIN ps_order_state_lang AS OSL ON PO.current_state = OSL.id_order_state
                INNER JOIN ps_order_detail AS OD ON PO.id_order = OD.id_order
                INNER JOIN ps_product AS PP ON OD.product_id = PP.id_product
                INNER JOIN ps_supplier AS PS ON PS.id_supplier = PP.id_supplier
                INNER JOIN ps_manufacturer AS PM ON PP.id_manufacturer = PM.id_manufacturer
                WHERE
                ((OSL.id_order_state = 2 OR
                OSL.id_order_state = 5) AND
                (PO.id_customer ='.$id_customer.') AND (PP.reference<>"MFLUZ") AND (OSL.id_lang='.$this->context->language->id.'))
                GROUP BY
                 id_manufacturer,
                manufacturer_name
                ORDER BY
                manufacturer_name ASC';
        
        $supplier = Db::getInstance()->executeS($query);
        return $supplier;
    }
    
    public function getProfileCustomer($id_customer){
        
        
        $query = 'SELECT firstname FROM '._DB_PREFIX_.'customer WHERE id_customer='.(int)$id_customer;
        
        $row= Db::getInstance()->getRow($query);
        $name = $row['firstname'];
        return $name;
    }
    
    public function getPointsLastDays($id_customer){
        
        $query = 'SELECT sum(credits) FROM '._DB_PREFIX_.'rewards WHERE id_customer='.(int)$id_customer.' AND date_add >= curdate() + interval -30 day'.' AND id_reward_state = 2';
        
        $row= Db::getInstance()->getRow($query);
        $datePoint = $row['sum(credits)'];
        return $datePoint;
        
    }
    
    
      
    public function getCustomerSponsorship($id_customer){
            
            //$seguir = true;
            $childs = array();
            array_push($childs,$id_customer);
            $childs2=array();
            //$childs3=array();
            
            
            $query =Db::getInstance()->executeS('SELECT id_customer FROM '._DB_PREFIX_.'rewards_sponsorship WHERE id_sponsor IN ('.implode(',', $childs).')');
                if($query != ""){
                    foreach ($query as $valor){
                        array_push($childs2,$valor['id_customer']);
                    }
                    //print_r(array_values($childs2));
                    //array_push($childs3,$childs);
                    
                }
             $childs=array_merge($childs, $childs2);   
             print_r(array_values($childs));
             die();
             
           return $query;
        }
        
    public function TopNetworkUnique() {
            $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
            
            foreach ($tree as $valor){
                $queryTop = 'SELECT c.username AS username, c.firstname AS name, c.lastname AS lastname, SUM(n.credits) AS points
                            FROM '._DB_PREFIX_.'rewards n 
                            LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = n.id_customer) WHERE n.id_customer='.$valor['id'];
                $result = Db::getInstance()->executeS($queryTop);
                
                if ($result[0]['points'] != "" ) {
                    $top[] = $result[0];
                }                
            }
            usort($top, function($a, $b) {
                return $b['points'] - $a['points'];
            });
            
            return array_slice($top, 0, 1);    
            
        }
        
        public function WorstNetworkUnique() {
            $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
            foreach ($tree as $valor){
                $queryTop = 'SELECT c.username AS username, c.firstname AS name, c.lastname AS lastname, SUM(n.credits) AS points
                            FROM '._DB_PREFIX_.'rewards n 
                            LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = n.id_customer) WHERE n.id_customer='.$valor['id'];
                $result = Db::getInstance()->executeS($queryTop);
                
                if ($result[0]['points'] != "" ) {
                    $top[] = $result[0];
                }                
            }
            usort($top, function($a, $b) {
                return $a['points'] - $b['points'];
            });
            
            return array_slice($top, 0, 1);    
            
        }
        
}
