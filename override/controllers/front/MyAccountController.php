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

require_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsModel.php');
class MyAccountController extends MyAccountControllerCore
{
    public function setMedia()
    {
        FrontController::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'my-account.css');
        $this->addCSS(_THEME_CSS_DIR_.'cardsview.css');
    }
    
    public function initContent()
    {
        FrontController::initContent();
        
        
        $totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
        $totalAvailable = round(isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0);
        $this->context->smarty->assign('totalAvailable', $totalAvailable);
        
        $customerProfile = $this->getProfileCustomer($this->context->customer->id);
        $this->context->smarty->assign('customerProfile', $customerProfile);
        $this->context->smarty->assign('profile', $this->context->customer->id);
        
        $ptosTotal = $this->getPointTotal($this->context->customer->id);
        $this->context->smarty->assign('ptosTotal', $ptosTotal);
        
        $lastPoint = $this->getPointsLastDays($this->context->customer->id);
        
        $this->context->smarty->assign('lastPoint', $lastPoint);
        $has_address = $this->context->customer->getAddresses($this->context->language->id);
        $membersCount = $this->numberMembers();

        $profile_complete = Customer::percentProfileComplete($this->context->customer->id);
        $this->context->smarty->assign('profile_complete', $profile_complete);
        
        $this->context->smarty->assign('membersCount', $membersCount);
        $this->context->smarty->assign(array(
            's3'=> _S3_PATH_,
            'manufacturers'=> $this->getProductsByManufacturer($this->context->customer->id),
            'pin_code' => $this->pinCodeProduct(),
            'has_customer_an_address' => empty($has_address),
            'voucherAllowed' => (int)CartRule::isFeatureActive(),
            'order_lastmonth' => $this->orderQuantity(),
            'topPoint'=> $this->TopNetworkUnique(),
            'worstPoint'=> $this->WorstNetworkUnique(),
            'returnAllowed' => (int)Configuration::get('PS_ORDER_RETURN')
        ));
        
        $groupCustomer = 'SELECT cg.id_group, gl.`name` FROM '._DB_PREFIX_.'customer_group cg 
                          LEFT JOIN '._DB_PREFIX_.'group_lang gl ON (cg.id_group = gl.id_group)
                          WHERE cg.id_customer = '.$this->context->customer->id.' AND gl.id_lang = 1';
        
        $rowcustomer = Db::getInstance()->executeS($groupCustomer);
        
        $this->context->smarty->assign('grupo',$rowcustomer);

        $imgprofile = "";
        if ( file_exists(_PS_IMG_DIR_."profile-images/".$this->context->customer->id.".png") ) {
            $imgprofile = "/img/profile-images/".$this->context->customer->id.".png";
        }
        $this->context->smarty->assign('imgprofile',$imgprofile);

        $this->context->smarty->assign('HOOK_CUSTOMER_ACCOUNT', Hook::exec('displayCustomerAccount'));
        
        // SPONSORS
        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
        $members = array();
        $searchnetwork = strtolower(Tools::getValue('searchnetwork'));

        foreach ($tree as $sponsor) {
            if ( $this->context->customer->id != $sponsor['id'] ) {
                $customer = new Customer($sponsor['id']);
                $name = strtolower($customer->firstname." ".$customer->lastname);
                if ( $customer->firstname != "" ) {
                    if ( $searchnetwork != "" ) {
                        $coincidence = strpos($name, $searchnetwork);
                        if ( $coincidence !== false ) {
                            $members[$sponsor['id']]['name'] = $name;
                            $members[$sponsor['id']]['username'] = $customer->username;
                            $members[$sponsor['id']]['id'] = $sponsor['id'];
                            $members[$sponsor['id']]['dateadd'] = date_format( date_create($customer->date_add) ,"d/m/y");
                            $members[$sponsor['id']]['level'] = $sponsor['level'];
                            $imgprofile = "";
                            if ( file_exists(_PS_IMG_DIR_."profile-images/".$sponsor['id'].".png") ) {
                                $imgprofile = "/img/profile-images/".$sponsor['id'].".png";
                            }
                            $members[$sponsor['id']]['img'] = $imgprofile;
                            $points = Db::getInstance()->ExecuteS("SELECT SUM(credits) AS points
                                                                    FROM "._DB_PREFIX_."rewards
                                                                    WHERE  id_customer = ".$this->context->customer->id."
                                                                    AND plugin = 'sponsorship'
                                                                    AND id_order IN (
                                                                            SELECT id_order
                                                                            FROM "._DB_PREFIX_."rewards
                                                                            WHERE  id_customer = ".$sponsor['id']."
                                                                            AND plugin = 'loyalty'
                                                                    )
                                                                    GROUP BY id_customer");
                            $members[$sponsor['id']]['points'] = $points[0]['points'];
                        }
                    } else {
                        $members[$sponsor['id']]['name'] = $name;
                        $members[$sponsor['id']]['username'] = $customer->username;
                        $members[$sponsor['id']]['id'] = $sponsor['id'];
                        $members[$sponsor['id']]['dateadd'] = date_format( date_create($customer->date_add) ,"d/m/y");
                        $members[$sponsor['id']]['level'] = $sponsor['level'];
                        $imgprofile = "";
                        if ( file_exists(_PS_IMG_DIR_."profile-images/".$sponsor['id'].".png") ) {
                            $imgprofile = "/img/profile-images/".$sponsor['id'].".png";
                        }
                        $members[$sponsor['id']]['img'] = $imgprofile;
                        $points = Db::getInstance()->ExecuteS("SELECT SUM(credits) AS points
                                                                FROM "._DB_PREFIX_."rewards
                                                                WHERE  id_customer = ".$this->context->customer->id."
                                                                AND plugin = 'sponsorship'
                                                                AND id_order IN (
                                                                        SELECT id_order
                                                                        FROM "._DB_PREFIX_."rewards
                                                                        WHERE  id_customer = ".$sponsor['id']."
                                                                        AND plugin = 'loyalty'
                                                                )
                                                                GROUP BY id_customer");
                        $members[$sponsor['id']]['points'] = $points[0]['points'];
                    }
                }
            }
        }
        /* ORGANIZAR POR NOMBRE */
        // asort($members);
        
        /* ORGANIZAR POR NIVEL */
        usort($members, function($a, $b) {
            return  $a['level'] - $b['level'];
        });
        $this->context->smarty->assign('members', $members);
        $this->context->smarty->assign('searchnetwork', $searchnetwork);
        
        
        // MESSAGES
        $messages = Db::getInstance()->ExecuteS("SELECT ms.id_customer_send, ms.id_customer_receive, ms.message, c.username, c.id_customer id
                                                FROM "._DB_PREFIX_."message_sponsor ms
                                                INNER JOIN "._DB_PREFIX_."customer c
                                                ON ( (ms.id_customer_send = c.id_customer AND c.id_customer <> ".$this->context->customer->id.") OR (ms.id_customer_receive = c.id_customer AND c.id_customer <> ".$this->context->customer->id.")  )
                                                WHERE ms.id_customer_send = ".$this->context->customer->id."
                                                OR ms.id_customer_receive = ".$this->context->customer->id."
                                                ORDER BY ms.date_send DESC");
        foreach ($messages AS $key => &$message) {
            $imgprofile = "";
            if ( file_exists(_PS_IMG_DIR_."profile-images/".$message['id'].".png") ) {
                $imgprofile = "/img/profile-images/".$message['id'].".png";
            }
            $message['img'] = $imgprofile;
        }
        $this->context->smarty->assign('messages', $messages);
        $this->context->smarty->assign('id_customer', $this->context->customer->id);
        
        // MEMBERS FEED
        $stringidsponsors = "";
        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
        foreach ($tree as $sponsor) {
            $stringidsponsors .= $sponsor['id'].",";
        }
        $last_shopping_products = Db::getInstance()->ExecuteS("SELECT
                                                        o.id_order,
                                                        o.date_add,
                                                        o.id_customer,
                                                        c.username name_customer,
                                                        pl.id_product,
                                                        i.id_image,
                                                        m.name name_product,
                                                        m.id_manufacturer,
                                                        pl.link_rewrite,
                                                        p.price,
                                                        od.points as credits
                                                FROM "._DB_PREFIX_."orders o
                                                INNER JOIN "._DB_PREFIX_."rewards r ON ( o.id_order = r.id_order AND r.plugin = 'sponsorship' AND r.id_customer = ".$this->context->customer->id." )
                                                INNER JOIN "._DB_PREFIX_."customer c ON ( o.id_customer = c.id_customer )
                                                INNER JOIN "._DB_PREFIX_."order_detail od ON ( o.id_order = od.id_order )
                                                INNER JOIN "._DB_PREFIX_."product p ON ( od.product_id = p.id_product )
                                                INNER JOIN "._DB_PREFIX_."image i ON ( od.product_id = i.id_product AND i.cover = 1 )
                                                INNER JOIN "._DB_PREFIX_."product_lang pl ON ( od.product_id = pl.id_product AND pl.id_lang = ".$this->context->language->id." )
                                                INNER JOIN ps_manufacturer m ON ( p.id_manufacturer = m.id_manufacturer )
                                                WHERE o.id_customer IN ( ".substr($stringidsponsors, 0, -1)." ) AND o.current_state = 2
                                                ORDER BY o.date_add DESC ");
        foreach ($last_shopping_products as &$last_shopping_product) {
            $imgprofile = "";
            if ( file_exists(_PS_IMG_DIR_."profile-images/".$last_shopping_product['id_customer'].".png") ) {
                $imgprofile = "/img/profile-images/".$last_shopping_product['id_customer'].".png";
            }
            $last_shopping_product['img'] = $imgprofile;
        }
        $this->context->smarty->assign('last_shopping_products', $last_shopping_products);

        $this->setTemplate(_PS_THEME_DIR_.'my-account.tpl');
    }
    
    public function getUserDataAccountApp( $id_customer ) {
      $customer = new Customer($id_customer);
      $this->context->customer = $customer;
      $userData = array();
      $lastPoint = $this->getPointsLastDays((int)$this->context->customer->id);
      $totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);

      $userData['image'] = false;
      if ( file_exists(_PS_IMG_DIR_."profile-images/".(string)$id_customer.".png") ) {
        $userData['image'] = "http://".Configuration::get('PS_SHOP_DOMAIN')."/img/profile-images/".(string)$id_customer.".png";
      }
      $userData['fluzLasted'] = $lastPoint['points'];
      $userData['fluzTotal'] = round(isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0);
      $userData['winnerMembers'] = $this->numberMembers();
      $userData['totalSavings'] = $userData['fluzTotal'] * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1');
      
      $sql = 'SELECT date_add AS date
              FROM ps_customer
              WHERE id_customer = '.$id_customer;
      $date_add = Db::getInstance()->getValue($sql);
      $userData['dateAdd'] = substr($date_add, 0, 4);
      $userData['dateAddFull'] = substr($date_add, 0, 10);
      return $userData;
    }
    
    public function getProductsByManufacturer($id_customer, $id_lang = null){
      
        $id_lang = $id_lang != null ? $id_lang : $this->context->language->id;
        $query='SELECT
                  PM.id_manufacturer AS id_manufacturer,
                  PM.`name` AS manufacturer_name,
                  PP.id_product AS id_product,
                  SUM(OD.product_quantity) AS products,
                  Count(wp.id_webservice_external_product) as count_m,
                  Sum(PP.price) AS total
                FROM
                  ps_orders AS PO
                  INNER JOIN '._DB_PREFIX_.'order_state_lang AS OSL ON PO.current_state = OSL.id_order_state
                  INNER JOIN '._DB_PREFIX_.'order_detail AS OD ON PO.id_order = OD.id_order
                  INNER JOIN '._DB_PREFIX_.'product AS PP ON OD.product_id = PP.id_product
                  INNER JOIN '._DB_PREFIX_.'supplier AS PS ON PS.id_supplier = PP.id_supplier
                  INNER JOIN '._DB_PREFIX_.'manufacturer AS PM ON PP.id_manufacturer = PM.id_manufacturer
                  LEFT JOIN '._DB_PREFIX_.'webservice_external_product  AS wp ON (PP.id_product=wp.id_product)
                WHERE
                  ((OSL.id_order_state = 2) AND
                  (PO.id_customer ='.$id_customer.') AND (PP.reference<>"MFLUZ") AND (OSL.id_lang='.$id_lang.'))
                GROUP BY
                 id_manufacturer,
                  manufacturer_name
                ORDER BY
                  manufacturer_name ASC';
        $supplier = Db::getInstance()->executeS($query);
        
        return $supplier;
    }
    
    public function pinCodeProduct(){
        
        $products = $this->getProductsByManufacturer($this->context->customer->id);
        $listPin = array();
        foreach ($products as &$p){
            $querypin = 'SELECT COUNT(pc.pin_code) AS pin, p.id_manufacturer FROM '._DB_PREFIX_.'product_code pc 
                     LEFT JOIN '._DB_PREFIX_.'product p ON (pc.id_product = p.id_product)  
                     WHERE p.id_product = '.$p['id_product'].' AND pc.pin_code != ""';
            
            $rowpin = Db::getInstance()->executeS($querypin);
            
            $listPin[] = $rowpin[0]; 
        }
        
        return $listPin;
    }
    
    public function getProfileCustomer($id_customer){
        
        
        $query = 'SELECT username FROM '._DB_PREFIX_.'customer WHERE id_customer='.(int)$id_customer;
        
        $row= Db::getInstance()->getRow($query);
        $name = $row['username'];
        return $name;
    }
    
    public function getPointTotal($id_customer){
        
        $query = 'SELECT SUM(credits) FROM '._DB_PREFIX_.'rewards WHERE id_customer='.$id_customer.' AND credits > 0 AND id_reward_state = 2';
        $pto = Db::getInstance()->getRow($query);
        $row = $pto['SUM(credits)'];
        
        return $row;
    }
    
    public function orderQuantity($id = ''){
      $id_customer = ( $id == '') ? $this->context->customer->id : $id;
      $query = "SELECT
                      c.id_customer,
                      c.date_kick_out,
                      DATE_FORMAT(c.date_kick_out,'%Y-%m-%d') date_kick_out_show,
                      c.warning_kick_out,
                      c.kick_out
                  FROM "._DB_PREFIX_."customer c
                  WHERE c.id_customer = ".$id_customer;
      $customer = Db::getInstance()->getRow($query);

      $query = "SELECT IFNULL(SUM(od.product_quantity),0) purchases
                  FROM "._DB_PREFIX_."orders o
                  INNER JOIN "._DB_PREFIX_."order_detail od ON ( o.id_order = od.id_order AND od.product_reference NOT LIKE 'MFLUZ%' )
                  WHERE o.current_state = 2
                  AND ( o.date_add BETWEEN DATE_ADD('".$customer['date_kick_out_show']." 00:00:00', INTERVAL ".($customer['warning_kick_out'] == 0 ? '-30' : '-60')." DAY)  AND '".$customer['date_kick_out_show']." 23:59:59' )
                  AND id_customer = ".$customer['id_customer'];
      $purchases = Db::getInstance()->getValue($query);

      $query = "SELECT DATE_FORMAT(DATE_ADD(date_kick_out, INTERVAL ".($customer['warning_kick_out'] == 0 ? '30' : '0')." DAY),'%Y-%m-%d') date
                  FROM "._DB_PREFIX_."customer
                  WHERE id_customer = ".$customer['id_customer'];
      $expiration_date = Db::getInstance()->getValue($query);

      if ( $customer['warning_kick_out'] == 0 && $purchases < 2 ) {
          $alertpurchaseorder['alert'] = 1;
          $alertpurchaseorder['orden'] = $purchases;
          $alertpurchaseorder['total'] = 2;
          $alertpurchaseorder['quantity'] = 2 - $purchases;
          $alertpurchaseorder['date'] = $customer['date_kick_out_show'];
      }

      if ( $customer['warning_kick_out'] == 0 && $purchases >= 2 ) {
          $alertpurchaseorder['alert'] = 2;
      }

      if ( $customer['warning_kick_out'] == 1 && $purchases < 4 ) {
          $alertpurchaseorder['alert'] = 3;
          $alertpurchaseorder['orden'] = $purchases;
          $alertpurchaseorder['quantity_max'] = 4;
          $alertpurchaseorder['total'] = 4;
          $alertpurchaseorder['quantity'] = 4 - $purchases;
          $alertpurchaseorder['date'] = $customer['date_kick_out_show'];
          $alertpurchaseorder['dateCancel'] = $expiration_date;
      }

      if ( $customer['kick_out'] == 1 ) {
          $alertpurchaseorder['alert'] = 4;
      }

      return $alertpurchaseorder;
    }
    
    public function getPointsLastDays($id_customer){
     
                $queryTop = 'SELECT ROUND(SUM(n.credits)) AS points
                            FROM ps_rewards n 
                            LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = n.id_customer) 
                            LEFT JOIN '._DB_PREFIX_.'order_detail s ON (s.id_order = n.id_order) 
                            WHERE n.id_customer='.$id_customer.'    
                            AND s.product_reference NOT LIKE "MFLUZ%" AND n.date_add >= curdate() + interval -30 day
                            AND n.id_reward_state = 2 AND n.credits > 0';
                
                $result = Db::getInstance()->getRow($queryTop);
                
            return $result;    
        }
        
    public function getCustomerSponsorship($id_customer){
            
            $childs = array();
            array_push($childs,$id_customer);
            $childs2=array();
          
            $query =Db::getInstance()->executeS('SELECT id_customer FROM '._DB_PREFIX_.'rewards_sponsorship WHERE id_sponsor IN ('.implode(',', $childs).')');
                if($query != ""){
                    foreach ($query as $valor){
                        array_push($childs2,$valor['id_customer']);
                    }
                    
                }
             $childs=array_merge($childs, $childs2);   
             
           return $query;
        }
    
    public function numberMembers(){
        
        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
        $num_members = (count($tree)-1);
        
        return $num_members; 
    }    
        
    public function TopNetworkUnique() {
            $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
            
            foreach ($tree as $valor){
                $queryTop = 'SELECT c.username AS username, s.product_reference AS reference, c.firstname AS name, c.lastname AS lastname, SUM(n.credits) AS points
                            FROM '._DB_PREFIX_.'rewards n 
                            LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = n.id_customer) 
                            LEFT JOIN '._DB_PREFIX_.'order_detail s ON (s.id_order = n.id_order) WHERE n.id_customer='.$valor['id'].'
                            AND s.product_reference NOT LIKE "MFLUZ%" AND n.credits > 0 AND '.$valor['level'].'!=0';
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
                $queryTop = 'SELECT c.username AS username, s.product_reference AS reference, c.firstname AS name, c.lastname AS lastname, SUM(n.credits) AS points
                            FROM '._DB_PREFIX_.'rewards n 
                            LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = n.id_customer) 
                            LEFT JOIN '._DB_PREFIX_.'order_detail s ON (s.id_order = n.id_order) WHERE n.id_customer='.$valor['id'].'
                            AND s.product_reference NOT LIKE "MFLUZ%" AND n.credits > 0 AND '.$valor['level'].'!=0';
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

?>
